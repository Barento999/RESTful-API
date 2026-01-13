<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
$pathParts = explode('/', $path);

// Remove base directory if present
if (isset($pathParts[0]) && strpos($pathParts[0], '.php') === false) {
    array_shift($pathParts);
}

$endpoint = isset($pathParts[0]) ? $pathParts[0] : '';

switch ($endpoint) {
    case 'login':
        require_once 'api/login.php';
        break;
    case 'students':
        require_once 'api/students.php';
        break;
    case 'courses':
        require_once 'api/courses.php';
        break;
    case 'enrollments':
        require_once 'api/enrollments.php';
        break;
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'available_endpoints' => [
                'POST /login',
                'GET|POST|PUT|DELETE /students',
                'GET|POST|PUT|DELETE /courses',
                'GET|POST|DELETE /enrollments'
            ]
        ]);
}
