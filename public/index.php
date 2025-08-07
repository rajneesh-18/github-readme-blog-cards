<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Card.php';

// Ignore favicon requests from browsers
if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    http_response_code(204); // No content
    exit();
}

// health check route
$requestUri = $_SERVER['REQUEST_URI'];

if ($requestUri === '/healthz') {
    http_response_code(200);
    echo 'OK';
    exit;
}

// Set default blog URL if 'url' param is missing or empty
$blogURL = isset($_GET['url']) && $_GET['url'] !== '' ? $_GET['url'] : null;

if (!$blogURL) {
    http_response_code(400);
    echo "Missing 'url' parameter";
    exit();
}

// get layout param
$layout = $_GET['layout'] ?? 'vertical';
$layout = match (strtolower($layout)) {
    'h', 'horizontal' => 'horizontal',
    'v', 'vertical' => 'vertical',
    default => 'vertical',
};

// get theme param
$theme = $_GET['theme'] ?? 'default';

// Generate and output SVG
header('Content-Type: image/svg+xml');
$card = new Card($blogURL, $layout, $theme);
echo $card->render();
