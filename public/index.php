<?php
require_once __DIR__ . '/../lib/helpers.php';

// Allow CORS (for frontend apps like React/Vue to connect easily)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Routes
require_once __DIR__ . '/../routes/students.php';
