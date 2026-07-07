<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\CleanupOldDownloads;

Schedule::job(new CleanupOldDownloads)->hourly();
