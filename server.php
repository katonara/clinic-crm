<?php

$uri = $_SERVER['REQUEST_URI'] ?? '';

// Health check
if ($uri === '/healthz' || $uri === '') {
    http_response_code(200);
    echo 'ok';
    return true;
}

// Validate URI before passing to Laravel
if (!$uri || parse_url($uri) === false) {
    http_response_code(400);
    echo 'Bad Request';
    return true;
}

$publicPath = __DIR__ . '/public';
$path = parse_url($uri, PHP_URL_PATH) ?? '/';

// Serve static files directly
if ($path !== '/' && file_exists($publicPath . $path)) {
    return false;
}

// Pass to Laravel
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
require $publicPath . '/index.php';
