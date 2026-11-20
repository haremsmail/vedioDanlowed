<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Deletes finished files and old job records so the (small, free) disk never fills up.
 */
class CleanupOldDownloads
{
    use Dispatchable;

    public function handle(): void
    {
        $ttl = max(5, (int) config('ytdlp.file_ttl_minutes', 60)) * 60;
        $now = time();

        $dirs = [
            config('ytdlp.downloads_path', storage_path('downloads')),
            config('ytdlp.jobs_path', storage_path('app/jobs')),
        ];

        foreach ($dirs as $dir) {
            foreach (glob(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
                if (is_file($file) && $now - filemtime($file) > $ttl && !str_starts_with(basename($file), '.')) {
                    @unlink($file);
                }
            }
        }
    }
}
