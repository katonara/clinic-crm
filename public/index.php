<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Simple health check for Railway (bypass Laravel)
if (($_SERVER['REQUEST_URI'] ?? '') === '/healthz') {
    http_response_code(200);
    echo 'ok';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
try {
    $request = Request::capture();
} catch (\Throwable $e) {
    http_response_code(400);
    echo 'Bad Request';
    exit;
}

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest($request);
