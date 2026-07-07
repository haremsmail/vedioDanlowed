<?php

namespace App\Jobs;

use App\Models\Download;
use App\Services\YtDlpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessVideoDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    public function __construct(
        private Download $download,
    ) {}

    public function handle(YtDlpService $ytDlpService): void
    {
        try {
            $this->download->update(['status' => Download::STATUS_DOWNLOADING]);

            Log::info('Starting download', [
                'download_id' => $this->download->id,
                'url' => $this->download->url,
                'format_id' => $this->download->format_id,
            ]);

            $result = $ytDlpService->downloadVideo(
                $this->download->url,
                $this->download->format_id
            );

            $this->download->update([
                'status' => Download::STATUS_COMPLETED,
                'file_path' => $result['file'],
            ]);

            Log::info('Download completed', [
                'download_id' => $this->download->id,
                'file' => $result['file'],
            ]);
        } catch (Exception $e) {
            Log::error('Download failed', [
                'download_id' => $this->download->id,
                'error' => $e->getMessage(),
            ]);

            $this->download->update([
                'status' => Download::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('Download job failed permanently', [
            'download_id' => $this->download->id,
            'error' => $exception->getMessage(),
        ]);

        $this->download->update([
            'status' => Download::STATUS_FAILED,
            'error_message' => __('messages.download_failed_permanently'),
        ]);
    }
}
