<?php
/**
 * Vercel PHP Router
 * This file routes all requests to the root PHP files.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim($uri, '/');

// Default to index.php
if ($uri === '') {
    $uri = 'index.php';
}

// Check if it's a direct file request in the root
$file = __DIR__ . '/../' . $uri;

if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// Check if it's an API request that already exists in the api folder
$apiFile = __DIR__ . '/' . $uri;
if (file_exists($apiFile) && is_file($apiFile) && pathinfo($apiFile, PATHINFO_EXTENSION) === 'php') {
    require $apiFile;
    exit;
}

// Fallback to index.php if not found, or 404
if (file_exists(__DIR__ . '/../index.php')) {
    require __DIR__ . '/../index.php';
    exit;
}

http_response_code(404);
echo "404 Not Found";
