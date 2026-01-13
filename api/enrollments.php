<?php
require_once '../db.php';
require_once '../auth.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

authenticate();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

switch ($method) {
    case 'GET':
        if (isset($pathParts[2]) && $pathParts[2] === 'student' && isset($pathParts[3])) {
            getStudentEnrollments($pdo, (int)$pathParts[3]);
        } elseif (isset($pathParts[2]) && $pathParts[2] === 'course' && isset($pathParts[3])) {
            getCourseEnrollments($pdo, (int)$pathParts[3]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid endpoint']);
        }
        break;
    case 'POST':
        createEnrollment($pdo);
        break;
    case 'DELETE':
        deleteEnrollment($pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function getStudentEnrollments($pdo, $studentId) {
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id = :id");
        $stmt->execute(['id' => $studentId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
            return;
        }
        
        $stmt = $pdo->prepare("
            SELECT e.id, e.enrollment_date, c.id as course_id, c.name, c.code, c.description, c.credits
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.student_id = :student_id
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        $enrollments = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'student_id' => $studentId,
            'data' => $enrollments,
            'count' => count($enrollments)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch enrollments']);
    }
}

function getCourseEnrollments($pdo, $courseId) {
    try {
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id");
        $stmt->execute(['id' => $courseId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        $stmt = $pdo->prepare("
            SELECT e.id, e.enrollment_date, s.id as student_id, s.first_name, s.last_name, s.email, s.phone
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            WHERE e.course_id = :course_id
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->execute(['course_id' => $courseId]);
        $enrollments = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'course_id' => $courseId,
            'data' => $enrollments,
            'count' => count($enrollments)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch enrollments']);
    }
}

function createEnrollment($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['student_id']) || !isset($input['course_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Student ID and Course ID are required']);
        return;
    }
    
    $studentId = (int)$input['student_id'];
    $courseId = (int)$input['course_id'];
    $enrollmentDate = isset($input['enrollment_date']) ? $input['enrollment_date'] : date('Y-m-d');
    
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id = :id");
        $stmt->execute(['id' => $studentId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
            return;
        }
        
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id");
        $stmt->execute(['id' => $courseId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        // Check if already enrolled
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Student is already enrolled in this course']);
            return;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (student_id, course_id, enrollment_date) 
            VALUES (:student_id, :course_id, :enrollment_date)
        ");
        $stmt->execute([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => $enrollmentDate
        ]);
        
        $id = $pdo->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Enrollment created successfully',
            'data' => [
                'id' => $id,
                'student_id' => $studentId,
                'course_id' => $courseId,
                'enrollment_date' => $enrollmentDate
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create enrollment']);
    }
}

function deleteEnrollment($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['student_id']) || !isset($input['course_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Student ID and Course ID are required']);
        return;
    }
    
    $studentId = (int)$input['student_id'];
    $courseId = (int)$input['course_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Enrollment not found']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Enrollment deleted successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete enrollment']);
    }
}
