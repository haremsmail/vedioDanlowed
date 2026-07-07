<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\ValidateApiToken;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Video API routes with rate limiting and authentication
Route::middleware([ValidateApiToken::class, RateLimitMiddleware::class])->group(function () {
    Route::post('/analyze', [VideoController::class, 'analyze'])->name('api.analyze');
    Route::post('/download', [VideoController::class, 'download'])->name('api.download');
});

