<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../controllers/StudentController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/EnrollmentController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = array_values(array_filter(explode('/', $path)));

// Login endpoint (no auth required)
if ($pathParts[0] === 'api' && $pathParts[1] === 'login' && $method === 'POST') {
    $pdo = getDBConnection();
    $controller = new AuthController($pdo);
    $controller->login();
    exit();
}

// All other endpoints require authentication
authenticate();

$pdo = getDBConnection();

try {
    // Students routes
    if ($pathParts[0] === 'api' && $pathParts[1] === 'students') {
        $controller = new StudentController($pdo);
        
        if ($method === 'GET' && !isset($pathParts[2])) {
            $controller->index();
        } elseif ($method === 'GET' && isset($pathParts[2])) {
            $controller->show((int)$pathParts[2]);
        } elseif ($method === 'POST') {
            $controller->store();
        } elseif ($method === 'PUT' && isset($pathParts[2])) {
            $controller->update((int)$pathParts[2]);
        } elseif ($method === 'DELETE' && isset($pathParts[2])) {
            $controller->destroy((int)$pathParts[2]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
        }
    }
    
    // Courses routes
    elseif ($pathParts[0] === 'api' && $pathParts[1] === 'courses') {
        $controller = new CourseController($pdo);
        
        if ($method === 'GET' && !isset($pathParts[2])) {
            $controller->index();
        } elseif ($method === 'GET' && isset($pathParts[2])) {
            $controller->show((int)$pathParts[2]);
        } elseif ($method === 'POST') {
            $controller->store();
        } elseif ($method === 'PUT' && isset($pathParts[2])) {
            $controller->update((int)$pathParts[2]);
        } elseif ($method === 'DELETE' && isset($pathParts[2])) {
            $controller->destroy((int)$pathParts[2]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
        }
    }
    
    // Enrollments routes
    elseif ($pathParts[0] === 'api' && $pathParts[1] === 'enrollments') {
        $controller = new EnrollmentController($pdo);
        
        if ($method === 'GET' && $pathParts[2] === 'student' && isset($pathParts[3])) {
            $controller->getByStudent((int)$pathParts[3]);
        } elseif ($method === 'GET' && $pathParts[2] === 'course' && isset($pathParts[3])) {
            $controller->getByCourse((int)$pathParts[3]);
        } elseif ($method === 'POST') {
            $controller->store();
        } elseif ($method === 'DELETE') {
            $controller->destroy();
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
        }
    }
    
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
