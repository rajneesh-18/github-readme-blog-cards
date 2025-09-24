<?php
// Serve CSS from src/demo/css/styles.css
header('Content-Type: text/css; charset=UTF-8');
readfile(__DIR__ . '/../src/demo/css/styles.css');
