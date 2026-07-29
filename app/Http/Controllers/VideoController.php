<?php
/*
Full File Purpose

This controller does 4 main jobs:

Show homepage
Analyze video URL
Download video
Send downloaded file to browser


*/
namespace App\Http\Controllers;

use App\Services\YtDlpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoController extends Controller
{
    private YtDlpService $ytDlpService;

    public function __construct(YtDlpService $ytDlpService)
    {
        $this->ytDlpService = $ytDlpService;
    }

    /**
     * Index page
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Analyze video from URL
     * POST /api/analyze
     */
    public function analyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'url' => 'required|string|max:2048',
            ]);

            $url = $validated['url'];

            // Check if URL is from supported platform
            if (!$this->ytDlpService->isSupportedPlatform($url)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unsupported_platform'),
                ], 400);
            }

            // Analyze video
            $videoData = $this->ytDlpService->analyzeVideo($url);

            return response()->json([
                'success' => true,
                'data'    => $videoData,
            ]);
        } catch (Exception $e) {
            Log::error('Analyze error', [
                'error' => $e->getMessage(),
                'url'   => $url ?? 'unknown',
                'ip'    => $request->ip(),
            ]);
            
            // Generic error message in production
            $message = config('app.debug')
                ? $e->getMessage()
                : __('messages.video_fetch_failed');
            
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }
    }

    /**
     * Download video synchronously and return a direct download URL
     * POST /api/download
     */
    public function download(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'url'       => 'required|string|max:2048',
                'format_id' => 'required|string|max:100',
                'quality'   => 'required|string|max:50',
            ]);

            $url      = $validated['url'];
            $formatId = $validated['format_id'];
            $quality  = $validated['quality'];

            // Check platform
            if (!$this->ytDlpService->isSupportedPlatform($url)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unsupported_platform'),
                ], 400);
            }

            // Validate format_id (prevent injection) — allow + for merged formats like "137+140"
            if (!preg_match('/^[a-zA-Z0-9_+\-]+$/', $formatId)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_format'),
                ], 400);
            }

            // Download synchronously
            $result = $this->ytDlpService->downloadVideo($url, $formatId);

            return response()->json([
                'success'      => true,
                'message'      => __('messages.download_ready'),
                'download_url' => $result['download_url'],
                'filename'     => basename($result['file']),
            ]);
        } catch (Exception $e) {
            Log::error('Download error', [
                'error'    => $e->getMessage(),
                'url'      => $url ?? 'unknown',
                'format'   => $formatId ?? 'unknown',
                'ip'       => $request->ip(),
            ]);
            
            // Generic error message in production
            $message = config('app.debug')
                ? $e->getMessage()
                : __('messages.download_failed');
            
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }
    }

    /**
     * Serve the downloaded file to the browser
     * GET /download/{file}
     */
    public function downloadFile(Request $request, string $file)
    {
        try {
            // Prevent directory traversal
            if (str_contains($file, '..') || str_contains($file, '/') || str_contains($file, '\\')) {
                abort(400, __('messages.invalid_file'));
            }

            $filePath = $this->ytDlpService->getDownloadsPath() . DIRECTORY_SEPARATOR . $file;

            if (!file_exists($filePath)) {
                abort(404, __('messages.file_not_found'));
            }

            return response()->download($filePath, $file, [
                'Content-Type' => 'application/octet-stream',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            Log::error('File download error', ['error' => $e->getMessage()]);
            abort(500, __('messages.download_failed'));
        }
    }
}
