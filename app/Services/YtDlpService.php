<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class YtDlpService
{
    private string $ytDlpPath;
    private string $downloadsPath;
    private string $jobsPath;
    private string $ffmpegPath;
    private int    $timeout;

    public function __construct()
    {
        $this->ytDlpPath     = $this->findYtDlp();
        $this->ffmpegPath    = $this->findFfmpeg();
        $this->downloadsPath = rtrim(config('ytdlp.downloads_path', storage_path('downloads')), '/\\');
        $this->jobsPath      = rtrim(config('ytdlp.jobs_path', storage_path('app/jobs')), '/\\');
        $this->timeout       = (int) config('ytdlp.timeout', 1800);

        foreach ([$this->downloadsPath, $this->jobsPath] as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Binary discovery
    // ─────────────────────────────────────────────────────────────────────

    private function findYtDlp(): string
    {
        $configPath = (string) config('ytdlp.yt_dlp_path', 'yt-dlp');

        if (PHP_OS_FAMILY !== 'Windows' || $configPath !== 'yt-dlp') {
            return $configPath;
        }

        $locations = [base_path('yt-dlp.exe'), 'C:\\laragon\\bin\\yt-dlp\\yt-dlp.exe'];
        if ($profile = getenv('USERPROFILE')) {
            foreach (['Python311', 'Python312', 'Python313', 'Python314'] as $py) {
                $locations[] = $profile . "\\AppData\\Local\\Programs\\Python\\{$py}\\Scripts\\yt-dlp.exe";
            }
        }
        foreach ($locations as $loc) {
            if (file_exists($loc)) {
                return $loc;
            }
        }

        return 'yt-dlp';
    }

    private function findFfmpeg(): string
    {
        $binary  = PHP_OS_FAMILY === 'Windows' ? 'ffmpeg.exe' : 'ffmpeg';
        $envPath = (string) config('ytdlp.ffmpeg_path');

        $locations = array_filter([
            $envPath,
            storage_path('tools/ffmpeg-master-latest-win64-gpl/bin'),
            storage_path('tools/ffmpeg/bin'),
            storage_path('tools'),
            base_path('ffmpeg/bin'),
            base_path('ffmpeg'),
            PHP_OS_FAMILY === 'Windows' ? 'C:\\laragon\\bin\\ffmpeg' : null,
        ]);

        foreach ($locations as $dir) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . $binary)) {
                return $dir;
            }
        }

        return ''; // yt-dlp will look in PATH
    }

    // ─────────────────────────────────────────────────────────────────────
    //  URL validation (whitelist + SSRF protection)
    // ─────────────────────────────────────────────────────────────────────

    public function isSupportedPlatform(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        // No credentials or unusual ports in the URL
        if (parse_url($url, PHP_URL_USER) !== null || parse_url($url, PHP_URL_PASS) !== null) {
            return false;
        }
        $port = parse_url($url, PHP_URL_PORT);
        if ($port !== null && !in_array($port, [80, 443], true)) {
            return false;
        }

        $host = strtolower(trim((string) parse_url($url, PHP_URL_HOST), '[].'));
        if ($host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
            return false; // raw IPs are never a supported platform
        }

        $matched = false;
        foreach ((array) config('ytdlp.supported_platforms', []) as $platform) {
            $platform = strtolower(trim((string) $platform));
            if ($platform !== '' && ($host === $platform || str_ends_with($host, '.' . $platform))) {
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            return false;
        }

        // DNS rebinding protection: resolved IPs must be public
        $ips = @gethostbynamel($host) ?: [];
        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Shared yt-dlp arguments
    // ─────────────────────────────────────────────────────────────────────

    private function baseArgs(): array
    {
        $args = [
            $this->ytDlpPath,
            '--ignore-config',
            '--no-playlist',
            '--no-warnings',
            '--no-colors',
            '--socket-timeout', '30',
            '--retries', '5',
            '--fragment-retries', '10',
            '--concurrent-fragments', '4',
            '--cache-dir', storage_path('app/yt-dlp-cache'),
        ];

        if ($this->ffmpegPath !== '') {
            array_push($args, '--ffmpeg-location', $this->ffmpegPath);
        }

        $cookies = (string) config('ytdlp.cookies_file');
        if ($cookies !== '' && is_file($cookies) && filesize($cookies) > 0) {
            array_push($args, '--cookies', $cookies);
        }

        $proxy = (string) config('ytdlp.proxy');
        if ($proxy !== '') {
            array_push($args, '--proxy', $proxy);
        }

        return $args;
    }

    private function processEnv(): ?array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }

        $env = array_merge($_SERVER, $_ENV);
        foreach (['SystemRoot' => 'C:\\Windows', 'SystemDrive' => 'C:', 'TEMP' => 'C:\\Windows\\Temp', 'PATH' => ''] as $k => $default) {
            $env[$k] = $env[$k] ?? (getenv($k) ?: $default);
        }

        return array_filter($env, 'is_scalar');
    }

    /**
     * Turn yt-dlp stderr into a user-friendly translated message.
     */
    private function friendlyError(string $stderr): string
    {
        $e = strtolower($stderr);

        return match (true) {
            str_contains($e, 'sign in to confirm') || str_contains($e, 'not a bot')
                || str_contains($e, 'login required') || str_contains($e, 'rate-limit') => __('messages.video_login_required'),
            str_contains($e, 'private')                                               => __('messages.video_private'),
            str_contains($e, 'removed') || str_contains($e, 'deleted')
                || str_contains($e, 'does not exist')                                 => __('messages.video_deleted'),
            str_contains($e, 'unsupported url')                                       => __('messages.unsupported_platform'),
            str_contains($e, 'larger than max-filesize') || str_contains($e, 'file is larger') => __('messages.file_too_large'),
            str_contains($e, 'not available') || str_contains($e, 'blocked')
                || str_contains($e, 'geo')                                            => __('messages.video_blocked'),
            default                                                                   => __('messages.video_fetch_failed'),
        };
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Analyze
    // ─────────────────────────────────────────────────────────────────────

    public function analyzeVideo(string $url): array
    {
        $cacheKey = 'analyze:' . sha1($url);
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $command = array_merge($this->baseArgs(), ['--dump-single-json', '--skip-download', '--', $url]);

        $process = new Process($command, null, $this->processEnv());
        $process->setTimeout(90);
        $process->run();

        if (!$process->isSuccessful()) {
            $stderr = $process->getErrorOutput();
            Log::warning('yt-dlp analyze failed', ['url' => $url, 'error' => mb_substr($stderr, 0, 600)]);
            throw new Exception($this->friendlyError($stderr));
        }

        $data = json_decode(trim($process->getOutput()), true);
        if (!is_array($data)) {
            throw new Exception(__('messages.invalid_video_data'));
        }

        $result = [
            'title'     => mb_substr((string) ($data['title'] ?? 'video'), 0, 200),
            'thumbnail' => $this->safeThumb($data['thumbnail'] ?? null),
            'duration'  => (int) ($data['duration'] ?? 0),
            'uploader'  => mb_substr((string) ($data['uploader'] ?? $data['channel'] ?? ''), 0, 100),
            'platform'  => (string) ($data['extractor_key'] ?? ''),
            'formats'   => $this->extractFormats($data),
        ];

        Cache::put($cacheKey, $result, (int) config('ytdlp.analyze_cache_seconds', 900));

        return $result;
    }

    private function safeThumb(?string $thumb): ?string
    {
        return ($thumb && str_starts_with($thumb, 'https://')) ? $thumb : null;
    }

    /**
     * Build a clean list of qualities. For every height we pick the most
     * compatible stream (H.264/MP4 plays on every phone, incl. iPhone),
     * and merge it with the best M4A audio when it has no audio.
     */
    private function extractFormats(array $data): array
    {
        $raw = $data['formats'] ?? [];

        $codecScore = static function (array $f): int {
            $v   = strtolower((string) ($f['vcodec'] ?? ''));
            $ext = strtolower((string) ($f['ext'] ?? ''));
            $score = match (true) {
                str_starts_with($v, 'avc') || str_starts_with($v, 'h264') => 30,
                str_starts_with($v, 'hev') || str_starts_with($v, 'hvc') => 20,
                str_starts_with($v, 'vp9') || str_starts_with($v, 'vp09') => 15,
                str_starts_with($v, 'av01') => 10,
                default => 12,
            };
            return $score + ($ext === 'mp4' ? 5 : 0);
        };

        $video = [];
        $bestAudio = null;
        $bestAudioScore = -1;

        foreach ($raw as $f) {
            if (!isset($f['format_id']) || !preg_match('/^[A-Za-z0-9_.\-]+$/', (string) $f['format_id'])) {
                continue;
            }
            $proto = (string) ($f['protocol'] ?? '');
            if (str_contains($proto, 'mhtml')) {
                continue; // storyboards
            }

            $vcodec   = $f['vcodec'] ?? null;
            $acodec   = $f['acodec'] ?? null;
            $hasVideo = $vcodec !== null ? $vcodec !== 'none' : in_array($f['ext'] ?? '', ['mp4', 'webm', 'mov', 'mkv'], true);
            $hasAudio = $acodec !== null ? $acodec !== 'none' : $hasVideo && $vcodec === null;

            if (!$hasVideo && $hasAudio) {
                $abr   = (float) ($f['abr'] ?? $f['tbr'] ?? 0);
                $score = $abr + (($f['ext'] ?? '') === 'm4a' ? 1000 : 0); // m4a merges cleanly into mp4
                if ($score > $bestAudioScore) {
                    $bestAudioScore = $score;
                    $bestAudio = $f;
                }
                continue;
            }

            if (!$hasVideo) {
                continue;
            }

            $height = (int) ($f['height'] ?? 0);
            $key    = $height > 0 ? $height : 0;
            $score  = $codecScore($f) * 100000 + ($hasAudio ? 50000 : 0) + (int) ($f['tbr'] ?? 0);

            if (!isset($video[$key]) || $score > $video[$key]['score']) {
                $video[$key] = ['f' => $f, 'score' => $score, 'hasAudio' => $hasAudio];
            }
        }

        krsort($video);
        $formats = [];

        foreach ($video as $height => $v) {
            $f  = $v['f'];
            $id = (string) $f['format_id'];
            $size = (int) ($f['filesize'] ?? $f['filesize_approx'] ?? 0);

            if (!$v['hasAudio'] && $bestAudio) {
                $id   .= '+' . $bestAudio['format_id'];
                $size += (int) ($bestAudio['filesize'] ?? $bestAudio['filesize_approx'] ?? 0);
            }

            $formats[] = [
                'id'      => $id,
                'type'    => 'video',
                'quality' => $height > 0 ? $height . 'p' : 'HD',
                'height'  => $height,
                'fps'     => (int) ($f['fps'] ?? 0),
                'ext'     => 'mp4',
                'size'    => $size,
            ];
        }

        if ($bestAudio) {
            $formats[] = [
                'id'      => (string) $bestAudio['format_id'],
                'type'    => 'audio',
                'quality' => 'MP3',
                'height'  => 0,
                'fps'     => 0,
                'ext'     => 'mp3',
                'size'    => (int) ($bestAudio['filesize'] ?? $bestAudio['filesize_approx'] ?? 0),
            ];
        }

        if (empty($formats)) {
            $formats[] = ['id' => 'best', 'type' => 'video', 'quality' => 'HD', 'height' => 0, 'fps' => 0, 'ext' => 'mp4', 'size' => 0];
        }

        return $formats;
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Download jobs (run in a detached background process)
    // ─────────────────────────────────────────────────────────────────────

    private function jobFile(string $id): string
    {
        return $this->jobsPath . DIRECTORY_SEPARATOR . $id . '.json';
    }

    public function isValidJobId(string $id): bool
    {
        return (bool) preg_match('/^[a-f0-9]{32}$/', $id);
    }

    public function readJob(string $id): ?array
    {
        if (!$this->isValidJobId($id) || !is_file($this->jobFile($id))) {
            return null;
        }
        $job = json_decode((string) @file_get_contents($this->jobFile($id)), true);

        return is_array($job) ? $job : null;
    }

    public function writeJob(array $job): void
    {
        $job['updated_at'] = time();
        $tmp = $this->jobFile($job['id']) . '.' . bin2hex(random_bytes(4)) . '.tmp';
        file_put_contents($tmp, json_encode($job, JSON_UNESCAPED_UNICODE));
        @rename($tmp, $this->jobFile($job['id'])) || @unlink($tmp);
    }

    /**
     * A job is considered active when it is queued/running and still sends heartbeats.
     */
    private function isActive(array $job): bool
    {
        return in_array($job['status'] ?? '', ['queued', 'downloading', 'processing'], true)
            && (time() - (int) ($job['updated_at'] ?? 0)) < 90;
    }

    /**
     * @return array{total:int, ip:int}
     */
    public function countActiveJobs(string $ipHash): array
    {
        $total = 0;
        $ip    = 0;
        foreach (glob($this->jobsPath . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
            $job = json_decode((string) @file_get_contents($file), true);
            if (is_array($job) && $this->isActive($job)) {
                $total++;
                if (($job['ip'] ?? '') === $ipHash) {
                    $ip++;
                }
            }
        }

        return ['total' => $total, 'ip' => $ip];
    }

    public function createJob(string $url, string $formatId, string $type, string $title, string $ipHash): array
    {
        $job = [
            'id'         => bin2hex(random_bytes(16)),
            'url'        => $url,
            'format_id'  => $formatId,
            'type'       => $type,
            'title'      => $title,
            'ip'         => $ipHash,
            'status'     => 'queued',
            'progress'   => 0,
            'speed'      => null,
            'eta'        => null,
            'file'       => null,
            'size'       => 0,
            'error'      => null,
            'created_at' => time(),
        ];
        $this->writeJob($job);
        $this->launchWorker($job['id']);

        return $job;
    }

    /**
     * In production the `video:worker` daemon picks up queued jobs.
     * For local development (DOWNLOAD_WORKER=spawn, default on Windows) we
     * start `php artisan video:process {id}` detached right away instead.
     */
    private function launchWorker(string $id): void
    {
        if (config('ytdlp.worker_mode') !== 'spawn') {
            return;
        }

        $php     = $this->phpBinary();
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'start "" /B ' . escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' video:process ' . $id;
            pclose(popen($cmd, 'r'));
            return;
        }

        exec('nohup ' . escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' video:process ' . $id . ' > /dev/null 2>&1 &');
    }

    private function phpBinary(): string
    {
        $configured = (string) config('ytdlp.php_binary');
        if ($configured !== '') {
            return $configured;
        }
        // PHP_BINARY is the CLI binary when running under `php -S` / artisan serve
        $name = strtolower(basename(PHP_BINARY));
        if (PHP_BINARY !== '' && (in_array($name, ['php', 'php.exe'], true) || preg_match('/^php\d[\d.]*$/', $name))) {
            return PHP_BINARY;
        }

        return PHP_OS_FAMILY === 'Windows' ? 'php.exe' : 'php';
    }

    /**
     * Runs inside the background worker: downloads the file and keeps the
     * job file updated with progress.
     */
    public function runJob(string $id): void
    {
        $job = $this->readJob($id);
        if (!$job || $job['status'] !== 'queued') {
            return;
        }

        $base     = 'v_' . $id;
        $template = $this->downloadsPath . DIRECTORY_SEPARATOR . $base . '.%(ext)s';
        $streams  = substr_count($job['format_id'], '+') + 1;

        $command = $this->baseArgs();
        array_push(
            $command,
            '-f', $job['format_id'] . '/best',
            '-o', $template,
            '--newline',
            '--no-part',
            '--no-mtime',
            '--progress-template',
            'download:PROG|%(progress.downloaded_bytes)s|%(progress.total_bytes)s|%(progress.total_bytes_estimate)s|%(progress.speed)s|%(progress.eta)s',
        );

        if ($job['type'] === 'audio') {
            array_push($command, '-x', '--audio-format', 'mp3', '--audio-quality', '192K');
        } else {
            array_push($command, '--merge-output-format', 'mp4', '--remux-video', 'mp4');
        }

        if ($max = config('ytdlp.max_download_size')) {
            array_push($command, '--max-filesize', (string) $max);
        }

        $command[] = '--';
        $command[] = $job['url'];

        $job['status'] = 'downloading';
        $this->writeJob($job);

        $process = new Process($command, null, $this->processEnv());
        $process->setTimeout($this->timeout);

        $stderr      = '';
        $buffer      = '';
        $streamIndex = 0;
        $lastBytes   = 0;
        $lastWrite   = 0;

        try {
            $process->start();

            while ($process->isRunning()) {
                $process->checkTimeout();
                $buffer .= $process->getIncrementalOutput();
                $stderr .= $process->getIncrementalErrorOutput();

                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line   = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if (str_starts_with($line, 'PROG|')) {
                        [, $done, $total, $estimate, $speed, $eta] = array_pad(explode('|', $line), 6, 'NA');
                        $done  = is_numeric($done) ? (float) $done : 0;
                        $total = is_numeric($total) ? (float) $total : (is_numeric($estimate) ? (float) $estimate : 0);

                        if ($done + 1 < $lastBytes && $streamIndex < $streams - 1) {
                            $streamIndex++; // next stream (audio after video)
                        }
                        $lastBytes = $done;

                        if ($total > 0) {
                            $pct = (($streamIndex + min(1, $done / $total)) / $streams) * 100;
                            $job['progress'] = (int) min(99, max($job['progress'], $pct));
                        }
                        $job['speed'] = is_numeric($speed) ? (int) $speed : null;
                        $job['eta']   = is_numeric($eta) ? (int) $eta : null;
                    } elseif (preg_match('/^\[(Merger|ExtractAudio|VideoRemuxer|FixupM3u8|VideoConvertor)\]/', $line)) {
                        $job['status']   = 'processing';
                        $job['progress'] = max($job['progress'], 99);
                    }
                }

                if (microtime(true) - $lastWrite >= 1) {
                    $this->writeJob($job); // also acts as heartbeat
                    $lastWrite = microtime(true);
                }

                if (strlen($stderr) > 20000) {
                    $stderr = substr($stderr, -10000);
                }

                usleep(300000);
            }

            $stderr .= $process->getIncrementalErrorOutput();
        } catch (\Throwable $e) {
            $process->stop(1);
            $stderr .= "\n" . $e->getMessage();
        }

        $file = $this->findDownloadedFile($base);

        if ($process->isSuccessful() && $file) {
            $job['status']   = 'done';
            $job['progress'] = 100;
            $job['file']     = basename($file);
            $job['size']     = filesize($file);
            $job['url']      = null; // don't keep visitor URLs longer than needed
            $this->writeJob($job);
            return;
        }

        Log::warning('yt-dlp download failed', ['format' => $job['format_id'], 'error' => mb_substr($stderr, -800)]);
        foreach (glob($this->downloadsPath . DIRECTORY_SEPARATOR . $base . '*') ?: [] as $leftover) {
            @unlink($leftover);
        }

        $job['status'] = 'error';
        $job['error']  = str_contains(strtolower($stderr), 'timed out')
            ? __('messages.download_failed')
            : $this->friendlyError($stderr);
        $job['url']    = null;
        $this->writeJob($job);
    }

    private function findDownloadedFile(string $base): ?string
    {
        foreach (glob($this->downloadsPath . DIRECTORY_SEPARATOR . $base . '.*') ?: [] as $file) {
            $name = basename($file);
            // skip intermediate per-stream files like v_xxx.f137.mp4 / .part / .ytdl / .temp
            if (preg_match('/^' . preg_quote($base, '/') . '\.[a-z0-9]{2,5}$/', $name) && is_file($file) && filesize($file) > 0) {
                return $file;
            }
        }

        return null;
    }

    public function getDownloadsPath(): string
    {
        return $this->downloadsPath;
    }

    public function getJobsPath(): string
    {
        return $this->jobsPath;
    }
}
