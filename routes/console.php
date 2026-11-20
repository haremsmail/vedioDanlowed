<?php

use App\Jobs\CleanupOldDownloads;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn () => (new CleanupOldDownloads())->handle())
    ->everyFiveMinutes()
    ->name('cleanup-downloads');
