<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// 1. Tangkap request secara manual
$request = Request::capture();

// 2. JIKA ENVIRONMENT ADALAH PRODUCTION (VPS), PAKSA SUBFOLDER
// Logika ini otomatis dilewati saat kamu run di laptop karena APP_ENV kamu bernilai 'local'
if (env('APP_ENV') === 'production') {
    $request->server->set('SCRIPT_NAME', '/2026/index.php');
    $request->server->set('SCRIPT_FILENAME', __FILE__);
}

// 3. Eksekusi request yang sudah diproses secara dinamis
$app->handleRequest($request);
