<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VideoController::class, 'index'])->name('home');

// JSON endpoints used by the website. They are in the "web" group so they get
// sessions + real CSRF protection (outsiders can't abuse them as a free API).
Route::prefix('api')->group(function () {
    Route::post('/analyze', [VideoController::class, 'analyze'])
        ->middleware('throttle:analyze')->name('api.analyze');

    Route::post('/download', [VideoController::class, 'download'])
        ->middleware('throttle:download')->name('api.download');

    Route::get('/status/{id}', [VideoController::class, 'status'])
        ->middleware('throttle:status')->where('id', '[a-f0-9]{32}')->name('api.status');
});

Route::get('/download/{id}', [VideoController::class, 'downloadFile'])
    ->middleware('throttle:status')
    ->where('id', '[a-f0-9]{32}')
    ->name('video.download.file');

// ads.txt for AdSense / other ad networks (content from ADS_TXT env variable)
Route::get('/ads.txt', function () {
    $content = str_replace('\n', "\n", (string) config('ads.ads_txt'));
    abort_if($content === '', 404);

    return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

Route::get('/privacy', fn () => view('privacy'))->name('privacy');
Route::get('/terms', fn () => view('terms'))->name('terms');
