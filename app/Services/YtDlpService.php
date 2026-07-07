<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Exception;

class YtDlpService
{
    private string $ytDlpPath;
    private string $downloadsPath;
    private string $ffmpegPath;
    private int    $timeout;

    public function __construct()
    {
        // Use configured path from .env, or auto-detect
        $configPath = config('ytdlp.yt_dlp_path', 'yt-dlp');
        
        if (PHP_OS_FAMILY === 'Windows') {
            // If config is just 'yt-dlp', try common Windows locations
            if ($configPath === 'yt-dlp') {
                $locations = [
                    base_path('yt-dlp.exe'),
                    'C:\\laragon\\bin\\yt-dlp\\yt-dlp.exe',
                ];
                
                // Also check Python Scripts directory
                $userProfile = getenv('USERPROFILE');
                if ($userProfile) {
                    $locations[] = $userProfile . '\\AppData\\Local\\Programs\\Python\\Python312\\Scripts\\yt-dlp.exe';
                    $locations[] = $userProfile . '\\AppData\\Local\\Programs\\Python\\Python313\\Scripts\\yt-dlp.exe';
                }
                
                $this->ytDlpPath = 'yt-dlp'; // fallback to PATH
                foreach ($locations as $loc) {
                    if (file_exists($loc)) {
                        $this->ytDlpPath = $loc;
                        break;
                    }
                }
            } else {
                $this->ytDlpPath = $configPath;
            }
        } else {
            $this->ytDlpPath = $configPath;
        }
        
        // Find ffmpeg for merging video+audio streams
        $this->ffmpegPath = $this->findFfmpeg();
        
        // Config already has storage_path() applied
        $this->downloadsPath = config('ytdlp.downloads_path', storage_path('downloads'));
        $this->timeout       = (int) config('ytdlp.timeout', 3600);

        if (!is_dir($this->downloadsPath)) {
            @mkdir($this->downloadsPath, 0755, true);
        }
        
        Log::debug('YtDlpService initialized', [
            'yt_dlp'  => $this->ytDlpPath,
            'ffmpeg'  => $this->ffmpegPath,
            'downloads' => $this->downloadsPath,
        ]);
    }
    
    /**
     * Locate ffmpeg binary for merging video+audio
     */
    private function findFfmpeg(): string
    {
        // Check env first
        $envPath = env('FFMPEG_PATH');
        if ($envPath && file_exists($envPath . DIRECTORY_SEPARATOR . 'ffmpeg' . (PHP_OS_FAMILY === 'Windows' ? '.exe' : ''))) {
            return $envPath;
        }
        
        $locations = [
            storage_path('tools/ffmpeg-master-latest-win64-gpl/bin'),
            storage_path('tools/ffmpeg/bin'),
            storage_path('tools'),
            base_path('ffmpeg/bin'),
            base_path('ffmpeg'),
        ];

        if (PHP_OS_FAMILY === 'Windows') {
            $locations[] = 'C:\\laragon\\bin\\ffmpeg';
        }
        
        $binary = PHP_OS_FAMILY === 'Windows' ? 'ffmpeg.exe' : 'ffmpeg';
        
        foreach ($locations as $dir) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . $binary)) {
                return $dir;
            }
        }
        
        return ''; // not found, yt-dlp will try PATH
    }

    /**
     * Check if the URL is a valid web URL that yt-dlp can try to process.
     * Accepts any valid HTTP/HTTPS URL — yt-dlp supports thousands of sites.
     */
    public function isSupportedPlatform(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host   = (string) parse_url($url, PHP_URL_HOST);

        return in_array($scheme, ['http', 'https'], true) && $host !== '';
    }

    /**
     * Analyze video and return metadata + all available formats
     * Uses Symfony Process on all platforms for safe command execution
     */
    public function analyzeVideo(string $url): array
    {
        // Build command safely (no shell interpretation)
        $command = [
            $this->ytDlpPath,
            '--dump-json',
            '--no-warnings',
            '--no-playlist',
            '--socket-timeout', '30',
            '--no-abort-on-unavailable-fragments',
            '--no-check-certificates',
        ];

        // Tell yt-dlp where ffmpeg is (needed for merging video+audio)
        if ($this->ffmpegPath !== '') {
            $command[] = '--ffmpeg-location';
            $command[] = $this->ffmpegPath;
        }

        $command[] = $url; // Safe - Process class handles escaping

        Log::info('Starting yt-dlp analyze', ['url' => $url]);

        $env = $this->buildProcessEnv();

        // Use Process class on all platforms
        $process = new Process($command, null, $env);
        $process->setTimeout($this->timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            Log::error('yt-dlp analyze failed', [
                'url'   => $url,
                'error' => substr($errorOutput, 0, 500),
            ]);
            
            // Give a more helpful error message for specific cases
            if (str_contains($errorOutput, 'is not a valid URL') || str_contains($errorOutput, 'Unsupported URL')) {
                throw new Exception(__('messages.unsupported_platform'));
            }
            if (str_contains($errorOutput, 'Private video') || str_contains($errorOutput, 'private')) {
                throw new Exception(__('messages.video_private'));
            }
            if (str_contains($errorOutput, 'been removed') || str_contains($errorOutput, 'deleted')) {
                throw new Exception(__('messages.video_deleted'));
            }
            if (str_contains($errorOutput, 'blocked') || str_contains($errorOutput, 'not available')) {
                throw new Exception(__('messages.video_blocked'));
            }
            
            throw new Exception(__('messages.video_fetch_failed'));
        }

        $output = trim($process->getOutput());
        if (empty($output)) {
            Log::warning('yt-dlp returned empty output', ['url' => $url]);
            throw new Exception(__('messages.video_fetch_failed'));
        }

        $videoData = json_decode($output, true);

        if (!$videoData || json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse yt-dlp JSON response', [
                'url'   => $url,
                'error' => json_last_error_msg(),
            ]);
            throw new Exception(__('messages.invalid_video_data'));
        }

        return [
            'success'   => true,
            'title'     => $videoData['title']     ?? 'Unknown',
            'thumbnail' => $videoData['thumbnail'] ?? null,
            'duration'  => $videoData['duration']  ?? 0,
            'uploader'  => $videoData['uploader']  ?? '',
            'formats'   => $this->extractFormats($videoData),
            'url'       => $url,
        ];
    }

    private function extractFormats(array $videoData): array
    {
        $rawFormats = $videoData['formats'] ?? [];

        // ── 1. Collect video-only and audio-only streams ─────────────────
        $videoStreams = []; // keyed by height or counter
        $audioStream  = null;
        $unknownHeightCounter = -1;
        $extPriority  = static function (string $ext): int {
            return match (strtolower($ext)) {
                'mp4', 'm4v' => 3,
                'mov', 'mkv' => 2,
                'webm' => 1,
                default => 0,
            };
        };

        foreach ($rawFormats as $f) {
            $hasVideo = isset($f['vcodec']) && $f['vcodec'] !== 'none';
            $hasAudio = isset($f['acodec']) && $f['acodec'] !== 'none';

            // If codecs are totally missing, check extension to guess if it's a video
            if (!isset($f['vcodec']) && !isset($f['acodec'])) {
                if (in_array(strtolower($f['ext'] ?? ''), ['mp4', 'webm', 'mov', 'mkv'])) {
                    $hasVideo = true;
                    $hasAudio = true; // Typically these single files have both
                }
            }

            $height = (int) ($f['height'] ?? 0);

            // Pure audio stream — keep the best bitrate one
            if (!$hasVideo && $hasAudio) {
                $abr = (float) ($f['abr'] ?? $f['tbr'] ?? 0);
                if ($audioStream === null || $abr > ($audioStream['abr'] ?? 0)) {
                    $audioStream = [
                        'id'     => $f['format_id'],
                        'format' => 'audio',
                        'ext'    => $f['ext'] ?? 'm4a',
                        'abr'    => $abr,
                    ];
                }
                continue;
            }

            // Video stream (may or may not carry audio)
            if ($hasVideo) {
                $ext = $f['ext'] ?? 'mp4';
                $tbr = (float) ($f['tbr'] ?? 0);

                // If height is 0, assign a unique negative counter so it still gets listed
                $hKey = $height > 0 ? $height : $unknownHeightCounter--;

                $currentExt = $videoStreams[$hKey]['ext'] ?? '';
                $shouldReplace = !isset($videoStreams[$hKey])
                    || $extPriority($ext) > $extPriority($currentExt)
                    || (
                        $extPriority($ext) === $extPriority($currentExt)
                        && $tbr > ($videoStreams[$hKey]['tbr'] ?? 0)
                    );

                if ($shouldReplace) {
                    $videoStreams[$hKey] = [
                        'id'       => $f['format_id'],
                        'height'   => $height,
                        'ext'      => $ext,
                        'hasAudio' => $hasAudio,
                        'tbr'      => $tbr,
                        'format_note' => $f['format_note'] ?? '',
                    ];
                }
            }
        }

        // ── 2. Build the formats array ────────────────────────────────────
        krsort($videoStreams); // highest quality first

        $formats = [];

        foreach ($videoStreams as $hKey => $vs) {
            $quality = $vs['height'] > 0 ? $vs['height'] . 'p' : 'HD/Default';
            if (!empty($vs['format_note'])) {
                $quality .= ' (' . $vs['format_note'] . ')';
            }

            // If the video stream has no audio, merge with best audio stream
            if (!$vs['hasAudio'] && $audioStream !== null) {
                $formatId = $vs['id'] . '+' . $audioStream['id'];
                $ext      = 'mp4'; // yt-dlp merges to mp4/mkv; we'll force mp4
            } else {
                $formatId = $vs['id'];
                $ext      = $vs['ext'];
            }

            $formats[] = [
                'id'      => $formatId,
                'format'  => 'video',
                'quality' => trim($quality),
                'height'  => $vs['height'],
                'ext'     => $ext,
            ];
        }

        // Audio-only entry at the end
        if ($audioStream !== null) {
            $formats[] = [
                'id'      => $audioStream['id'],
                'format'  => 'audio',
                'quality' => 'Audio Only',
                'height'  => 0,
                'ext'     => $audioStream['ext'],
            ];
        }

        // ── 3. Fallback if no formats found but there is a direct URL ───
        if (empty($formats) && !empty($videoData['url'])) {
            $formats[] = [
                'id'      => $videoData['format_id'] ?? 'best',
                'format'  => 'video',
                'quality' => 'Default Quality',
                'height'  => 0,
                'ext'     => $videoData['ext'] ?? 'mp4',
            ];
        }

        // ── 4. Ultimate fallback — always provide at least one option ────
        if (empty($formats)) {
            $formats[] = [
                'id'      => 'best',
                'format'  => 'video',
                'quality' => 'Best Available',
                'height'  => 0,
                'ext'     => 'mp4',
            ];
        }

        return $formats;
    }

    /**
     * Download video synchronously and return file info
     * Uses Symfony\Component\Process\Process for safe command execution on all platforms
     */
    public function downloadVideo(string $url, string $formatId): array
    {
        // Use cryptographically secure random names (impossible to guess)
        $fileBase  = 'v_' . bin2hex(random_bytes(16));
        $outputTpl = $this->downloadsPath . DIRECTORY_SEPARATOR . $fileBase . '.%(ext)s';

        // Build command arguments safely (no shell interpretation)
        $command = [
            $this->ytDlpPath,
            '-f', $formatId,
            '-o', $outputTpl,
            '--no-warnings',
            '--no-playlist',
            '--socket-timeout', '60',
            '--merge-output-format', 'mp4',
            '--no-abort-on-unavailable-fragments',
            '--no-check-certificates',
            '--progress-template', 'download:%(progress._percent_str)s',
        ];

        // Tell yt-dlp where ffmpeg is (critical for merging video+audio)
        if ($this->ffmpegPath !== '') {
            $command[] = '--ffmpeg-location';
            $command[] = $this->ffmpegPath;
        }

        // Add max file size if configured
        $maxSize = config('ytdlp.max_download_size');
        if ($maxSize) {
            $command[] = '--max-filesize';
            $command[] = $maxSize;
        }

        // Add URL last (safe - no shell interpretation)
        $command[] = $url;

        Log::info('Starting yt-dlp download', [
            'format'   => $formatId,
            'file_base' => $fileBase,
        ]);

        $env = $this->buildProcessEnv();

        // Use Process class on all platforms (safe, cross-platform)
        $process = new Process($command, null, $env);
        $process->setTimeout($this->timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            Log::error('yt-dlp download failed', [
                'url'      => $url,
                'formatId' => $formatId,
                'error'    => substr($errorOutput, 0, 500), // Limit log size
            ]);
            throw new Exception(__('messages.download_failed'));
        }

        // Find the file that was written
        $downloadedFile = $this->findDownloadedFile($fileBase);
        if (!$downloadedFile) {
            Log::error('Downloaded file not found', ['prefix' => $fileBase]);
            throw new Exception(__('messages.download_failed'));
        }

        Log::info('Download completed successfully', [
            'file' => basename($downloadedFile),
            'size' => filesize($downloadedFile),
        ]);

        return [
            'success'      => true,
            'file'         => $downloadedFile,
            'download_url' => route('video.download.file', ['file' => basename($downloadedFile)]),
        ];
    }

    /**
     * Locate the file created by yt-dlp (unknown extension)
     */
    private function findDownloadedFile(string $prefix): ?string
    {
        if (!is_dir($this->downloadsPath)) {
            return null;
        }

        foreach (scandir($this->downloadsPath) as $file) {
            if (str_starts_with($file, $prefix) && is_file($this->downloadsPath . DIRECTORY_SEPARATOR . $file)) {
                return $this->downloadsPath . DIRECTORY_SEPARATOR . $file;
            }
        }

        return null;
    }

    public function getDownloadsPath(): string
    {
        return $this->downloadsPath;
    }
    
    /**
     * Build environment variables for yt-dlp process.
     * Ensures critical Windows system variables are available.
     */
    private function buildProcessEnv(): ?array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }
        
        $env = array_merge($_SERVER, $_ENV);
        $env['SystemRoot']  = $env['SystemRoot']  ?? getenv('SystemRoot')  ?: 'C:\\Windows';
        $env['SystemDrive'] = $env['SystemDrive'] ?? getenv('SystemDrive') ?: 'C:';
        $env['USERPROFILE'] = $env['USERPROFILE'] ?? getenv('USERPROFILE') ?: 'C:\\Users\\Default';
        $env['TEMP']        = $env['TEMP']        ?? getenv('TEMP')        ?: 'C:\\Windows\\Temp';
        $env['PATH']        = $env['PATH']        ?? getenv('PATH')        ?: '';
        
        return $env;
    }
}
