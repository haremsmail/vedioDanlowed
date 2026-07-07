<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

// Home page
Route::get('/', [VideoController::class, 'index'])->name('home');

// Serve downloaded file (no rate limit — user already paid the cost in /api/download)
Route::get('/download/{file}', [VideoController::class, 'downloadFile'])
    ->name('video.download.file')
    ->where('file', '[^/]+');
