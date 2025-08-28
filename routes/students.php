<?php
require_once __DIR__ . '/../controllers/StudentController.php';

$studentController = new StudentController();

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$uri = strtok($uri, '?');

// Detect script name dynamically
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove the script path from URI
if (strpos($uri, $script_name) === 0) {
    $uri = substr($uri, strlen($script_name));
}

// Also remove any folder prefixes before index.php (for Apache setups)
$uri = preg_replace('#^.*/index\.php#', '', $uri);

// Ensure leading slash
$uri = '/' . ltrim($uri, '/');
$studentController = new StudentController();

// Routing logic 
switch (true) {
    case $uri === '/students' && $method === 'GET':
        $studentController->index();
        break;

    case preg_match('#^/students/(\d+)$#', $uri, $matches) && $method === 'GET':
        $studentController->show($matches[1]);
        break;

    case $uri === '/students' && $method === 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $studentController->store($data);
        break;

    case preg_match('#^/students/(\d+)$#', $uri, $matches) && $method === 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $studentController->update($matches[1], $data);
        break;

    case preg_match('#^/students/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        $studentController->destroy($matches[1]);
        break;

    case preg_match('#^/students/(\d+)/fee$#', $uri, $matches) && $method === 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $studentController->updateFee($matches[1], $data);
        break;

    case $uri === '/students/search' && $method === 'GET':
        $name = $_GET['name'] ?? '';
        $studentController->search($name);
        break;

    default:
        jsonError("Route not found", 404);
}
