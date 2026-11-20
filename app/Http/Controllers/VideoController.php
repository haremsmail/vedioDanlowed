<?php

namespace App\Http\Controllers;

use App\Services\YtDlpService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Flow:
 *  1. POST /api/analyze          → video info + list of qualities
 *  2. POST /api/download         → starts a background job, returns job id
 *  3. GET  /api/status/{id}      → progress (polled by the browser)
 *  4. GET  /download/{id}        → the finished file
 */
class VideoController extends Controller
{
    public function __construct(private YtDlpService $ytDlp) {}

    public function index()
    {
        return view('index');
    }

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|string|url:http,https|max:2048',
        ]);
        $url = trim($validated['url']);

        if (!$this->ytDlp->isSupportedPlatform($url)) {
            return $this->fail(__('messages.unsupported_platform'));
        }

        try {
            $data = $this->ytDlp->analyzeVideo($url);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }

        // Remember which URL this visitor analyzed, so /download can't be
        // used to fetch arbitrary URLs without going through validation.
        $request->session()->put('analyzed.' . sha1($url), [
            'title'   => $data['title'],
            'formats' => array_column($data['formats'], 'type', 'id'),
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function download(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url'       => 'required|string|url:http,https|max:2048',
            'format_id' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9_.\-]+(\+[A-Za-z0-9_.\-]+)?$/'],
        ]);
        $url = trim($validated['url']);

        $analyzed = $request->session()->get('analyzed.' . sha1($url));
        if (!$analyzed || !isset($analyzed['formats'][$validated['format_id']])) {
            return $this->fail(__('messages.invalid_format'));
        }

        if (!$this->ytDlp->isSupportedPlatform($url)) {
            return $this->fail(__('messages.unsupported_platform'));
        }

        $ipHash = hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));
        $active = $this->ytDlp->countActiveJobs($ipHash);

        if ($active['ip'] >= (int) config('ytdlp.max_concurrent_per_ip', 1)) {
            return $this->fail(__('messages.one_at_a_time'), 429);
        }
        if ($active['total'] >= (int) config('ytdlp.max_concurrent', 3)) {
            return $this->fail(__('messages.server_busy'), 503);
        }

        try {
            $job = $this->ytDlp->createJob(
                $url,
                $validated['format_id'],
                $analyzed['formats'][$validated['format_id']],
                $analyzed['title'],
                $ipHash,
            );
        } catch (Exception $e) {
            Log::error('Could not start download job', ['error' => $e->getMessage()]);
            return $this->fail(__('messages.download_failed'), 500);
        }

        // Only this browser session may read/download this job
        $request->session()->push('jobs', $job['id']);

        return response()->json(['success' => true, 'job_id' => $job['id']]);
    }

    public function status(Request $request, string $id): JsonResponse
    {
        $job = $this->ownedJob($request, $id);
        if (!$job) {
            return $this->fail(__('messages.download_not_found'), 404);
        }

        // Worker died without reporting (e.g. server restart)
        if (in_array($job['status'], ['queued', 'downloading', 'processing'], true)
            && time() - (int) $job['updated_at'] > 90) {
            $job['status'] = 'error';
            $job['error']  = __('messages.download_failed');
        }

        return response()->json([
            'success'  => true,
            'status'   => $job['status'],
            'progress' => (int) $job['progress'],
            'speed'    => $job['speed'],
            'eta'      => $job['eta'],
            'size'     => $job['size'],
            'error'    => $job['status'] === 'error' ? $job['error'] : null,
            'file_url' => $job['status'] === 'done' ? route('video.download.file', ['id' => $job['id']], false) : null,
        ]);
    }

    public function downloadFile(Request $request, string $id)
    {
        $job = $this->ownedJob($request, $id);
        if (!$job || $job['status'] !== 'done' || empty($job['file'])) {
            abort(404);
        }

        $file = basename($job['file']);
        $path = $this->ytDlp->getDownloadsPath() . DIRECTORY_SEPARATOR . $file;
        if (!preg_match('/^v_[a-f0-9]{32}\.[a-z0-9]{2,5}$/', $file) || !is_file($path)) {
            abort(404, __('messages.file_not_found'));
        }

        $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'mkv'  => 'video/x-matroska',
            'm4a'  => 'audio/mp4',
            'mp3'  => 'audio/mpeg',
            'ogg', 'opus' => 'audio/ogg',
            default => 'application/octet-stream',
        };

        // Readable file name from the video title
        $title = preg_replace('/[^\p{L}\p{N}\s\-_.()]+/u', '', (string) $job['title']);
        $title = trim(preg_replace('/\s+/u', ' ', $title)) ?: 'video';
        $name  = Str::limit($title, 80, '') . '.' . $ext;

        // BinaryFileResponse: efficient streaming + HTTP Range (resume) support
        return response()->download($path, $name, [
            'Content-Type'           => $mime,
            'Cache-Control'          => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function ownedJob(Request $request, string $id): ?array
    {
        if (!$this->ytDlp->isValidJobId($id) || !in_array($id, (array) $request->session()->get('jobs', []), true)) {
            return null;
        }

        return $this->ytDlp->readJob($id);
    }

    private function fail(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
