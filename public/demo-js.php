<?php
// Serve JS from src/demo/js/main.js
header('Content-Type: application/javascript; charset=UTF-8');
readfile(__DIR__ . '/../src/demo/js/main.js');
