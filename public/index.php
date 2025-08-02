<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Card.php';

// Ignore favicon requests from browsers
if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    http_response_code(204); // No content
    exit();
}

// Set default blog URL if 'url' param is missing or empty
$blogURL = isset($_GET['url']) && $_GET['url'] !== '' ? $_GET['url'] : null;

// get layout param
$layout = $_GET['layout'] ?? 'horizontal';
$layout = match (strtolower($layout)) {
    'h', 'horizontal' => 'horizontal',
    'v', 'vertical' => 'vertical',
    default => 'horizontal',
};

if (!$blogURL) {
    http_response_code(400);
    echo "Missing 'url' parameter";
    exit();
}

// Generate and output SVG
header('Content-Type: image/svg+xml');
$card = new Card($blogURL, $layout);
echo $card->render();
