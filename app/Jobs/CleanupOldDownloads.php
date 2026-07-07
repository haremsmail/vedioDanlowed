<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupOldDownloads
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Execute the job.
     * Removes downloaded files older than 24 hours
     */
    public function handle(): void
    {
        $downloadsPath = storage_path('downloads');
        $maxAgeSeconds = 24 * 60 * 60; // 24 hours
        $now = time();
        $deletedCount = 0;

        if (!is_dir($downloadsPath)) {
            Log::warning('Downloads directory does not exist', [
                'path' => $downloadsPath,
            ]);
            return;
        }

        $files = @glob($downloadsPath . '/*');

        if ($files === false) {
            Log::warning('Failed to read downloads directory');
            return;
        }

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $fileAge = $now - filemtime($file);

            if ($fileAge > $maxAgeSeconds) {
                if (@unlink($file)) {
                    $deletedCount++;
                    Log::info('Deleted old download', [
                        'file' => basename($file),
                        'age_hours' => round($fileAge / 3600),
                    ]);
                } else {
                    Log::warning('Failed to delete old download', [
                        'file' => basename($file),
                    ]);
                }
            }
        }

        Log::info('Cleanup completed', [
            'deleted_count' => $deletedCount,
        ]);
    }
}
