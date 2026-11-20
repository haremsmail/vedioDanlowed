<?php

namespace App\Console\Commands;

use App\Services\YtDlpService;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Long-running daemon (started by docker-entrypoint.sh) that picks up queued
 * download jobs and runs each one in its own `video:process` child process.
 */
class VideoWorker extends Command
{
    protected $signature = 'video:worker';

    protected $description = 'Run queued video download jobs in the background';

    public function handle(YtDlpService $service): int
    {
        @set_time_limit(0);
        $this->info('Video worker started');

        /** @var array<string, Process> $running */
        $running = [];
        $max     = max(1, (int) config('ytdlp.max_concurrent', 3));

        while (true) {
            foreach ($running as $id => $proc) {
                if (!$proc->isRunning()) {
                    unset($running[$id]);
                }
            }

            if (count($running) < $max) {
                foreach ($this->queuedJobs($service) as $id) {
                    if (isset($running[$id]) || count($running) >= $max) {
                        continue;
                    }
                    $proc = new Process([PHP_BINARY, base_path('artisan'), 'video:process', $id], base_path());
                    $proc->setTimeout(null);
                    $proc->start();
                    $running[$id] = $proc;
                }
            }

            usleep(400000);
        }
    }

    /**
     * @return string[] queued job ids, oldest first
     */
    private function queuedJobs(YtDlpService $service): array
    {
        $jobs = [];
        foreach (glob($service->getJobsPath() . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
            $id  = basename($file, '.json');
            $job = $service->readJob($id);
            if ($job && $job['status'] === 'queued') {
                $jobs[$id] = $job['created_at'] ?? 0;
            }
        }
        asort($jobs);

        return array_keys($jobs);
    }
}
