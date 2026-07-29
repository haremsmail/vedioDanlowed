<?php

/*
|--------------------------------------------------------------------------
| Public Path
|--------------------------------------------------------------------------
|
| This file is the entry point for all requests to your Laravel application
|
*/

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the incoming request
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
