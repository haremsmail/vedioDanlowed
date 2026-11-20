<?php

namespace App\Console\Commands;

use App\Services\YtDlpService;
use Illuminate\Console\Command;

class ProcessVideoJob extends Command
{
    protected $signature = 'video:process {id}';

    protected $description = 'Background worker: download one video job with yt-dlp';

    public function handle(YtDlpService $service): int
    {
        $id = (string) $this->argument('id');
        if (!$service->isValidJobId($id)) {
            return self::FAILURE;
        }

        @set_time_limit(0);
        ignore_user_abort(true);

        $service->runJob($id);

        return self::SUCCESS;
    }
}
