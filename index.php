<?php
// Forward request to public directory for Apache / XAMPP subdirectory deployment
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if (!str_contains($requestUri, '/public')) {
    $target = $baseDir . '/public' . str_replace($baseDir, '', $requestUri);
    header('Location: ' . $target, true, 302);
    exit;
}

require_once __DIR__ . '/public/index.php';
