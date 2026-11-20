<?php

use Illuminate\Support\Facades\Route;

// The video endpoints live in routes/web.php (session + CSRF protected).
Route::get('/health', fn () => response()->json(['status' => 'ok']));
