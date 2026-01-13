<?php
require_once '../db.php';
require_once '../auth.php';
require_once '../config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication
authenticate();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

// Parse URL path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$id = isset($pathParts[2]) && is_numeric($pathParts[2]) ? (int)$pathParts[2] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            getStudent($pdo, $id);
        } else {
            getStudents($pdo);
        }
        break;
    case 'POST':
        createStudent($pdo);
        break;
    case 'PUT':
        if ($id) {
            updateStudent($pdo, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Student ID is required']);
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteStudent($pdo, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Student ID is required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function getStudents($pdo) {
    try {
        $query = "SELECT * FROM students WHERE 1=1";
        $params = [];
        
        // Search
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = '%' . trim($_GET['search']) . '%';
            $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params['search'] = $search;
        }
        
        // Export to CSV
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            exportStudentsCSV($pdo, $query, $params);
            return;
        }
        
        // Count total
        $countStmt = $pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $query));
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        // Pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? 
                 min(MAX_PAGE_SIZE, max(1, (int)$_GET['limit'])) : DEFAULT_PAGE_SIZE;
        $offset = ($page - 1) * $limit;
        
        $query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $students = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $students,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch students']);
    }
}

function getStudent($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch();
        
        if (!$student) {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
            return;
        }
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $student]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch student']);
    }
}

function createStudent($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['first_name', 'last_name', 'email'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    $email = trim($input['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }
    
    try {
        // Check duplicate email
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO students (first_name, last_name, email, phone, address, dob, gender) 
            VALUES (:first_name, :last_name, :email, :phone, :address, :dob, :gender)
        ");
        
        $stmt->execute([
            'first_name' => trim($input['first_name']),
            'last_name' => trim($input['last_name']),
            'email' => $email,
            'phone' => isset($input['phone']) ? trim($input['phone']) : null,
            'address' => isset($input['address']) ? trim($input['address']) : null,
            'dob' => isset($input['dob']) ? $input['dob'] : null,
            'gender' => isset($input['gender']) ? $input['gender'] : null
        ]);
        
        $id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create student']);
    }
}

function updateStudent($pdo, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
            return;
        }
        
        $required = ['first_name', 'last_name', 'email'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                http_response_code(400);
                echo json_encode(['error' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }
        
        $email = trim($input['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }
        
        // Check duplicate email
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $id]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
        
        $stmt = $pdo->prepare("
            UPDATE students 
            SET first_name = :first_name, last_name = :last_name, email = :email, 
                phone = :phone, address = :address, dob = :dob, gender = :gender
            WHERE id = :id
        ");
        
        $stmt->execute([
            'first_name' => trim($input['first_name']),
            'last_name' => trim($input['last_name']),
            'email' => $email,
            'phone' => isset($input['phone']) ? trim($input['phone']) : null,
            'address' => isset($input['address']) ? trim($input['address']) : null,
            'dob' => isset($input['dob']) ? $input['dob'] : null,
            'gender' => isset($input['gender']) ? $input['gender'] : null,
            'id' => $id
        ]);
        
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update student']);
    }
}

function deleteStudent($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete student']);
    }
}

function exportStudentsCSV($pdo, $query, $params) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $students = $stmt->fetchAll();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Address', 'DOB', 'Gender', 'Created At']);
        
        foreach ($students as $student) {
            fputcsv($output, [
                $student['id'],
                $student['first_name'],
                $student['last_name'],
                $student['email'],
                $student['phone'],
                $student['address'],
                $student['dob'],
                $student['gender'],
                $student['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to export students']);
    }
}
