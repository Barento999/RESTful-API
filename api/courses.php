<?php
// Route to MVC structure
require_once __DIR__ . '/../routes/api.php';
exit();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
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
$id = isset($pathParts[2]) && is_numeric($pathParts[2]) ? (int)$pathParts[2] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            getCourse($pdo, $id);
        } else {
            getCourses($pdo);
        }
        break;
    case 'POST':
        createCourse($pdo);
        break;
    case 'PUT':
        if ($id) {
            updateCourse($pdo, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID is required']);
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteCourse($pdo, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID is required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function getCourses($pdo) {
    try {
        $query = "SELECT * FROM courses WHERE 1=1";
        $params = [];
        
        // Search
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = '%' . trim($_GET['search']) . '%';
            $query .= " AND (name LIKE :search OR code LIKE :search)";
            $params['search'] = $search;
        }
        
        // Export to CSV
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            exportCoursesCSV($pdo, $query, $params);
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
        
        $courses = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $courses,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch courses']);
    }
}

function getCourse($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $course = $stmt->fetch();
        
        if (!$course) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $course]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch course']);
    }
}

function createCourse($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['name', 'code'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => ucfirst($field) . ' is required']);
            return;
        }
    }
    
    try {
        // Check duplicate code
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = :code");
        $stmt->execute(['code' => trim($input['code'])]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Course code already exists']);
            return;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO courses (name, code, description, credits) 
            VALUES (:name, :code, :description, :credits)
        ");
        
        $stmt->execute([
            'name' => trim($input['name']),
            'code' => trim($input['code']),
            'description' => isset($input['description']) ? trim($input['description']) : null,
            'credits' => isset($input['credits']) && is_numeric($input['credits']) ? (int)$input['credits'] : null
        ]);
        
        $id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $course = $stmt->fetch();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create course']);
    }
}

function updateCourse($pdo, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        $required = ['name', 'code'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                http_response_code(400);
                echo json_encode(['error' => ucfirst($field) . ' is required']);
                return;
            }
        }
        
        // Check duplicate code
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = :code AND id != :id");
        $stmt->execute(['code' => trim($input['code']), 'id' => $id]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Course code already exists']);
            return;
        }
        
        $stmt = $pdo->prepare("
            UPDATE courses 
            SET name = :name, code = :code, description = :description, credits = :credits
            WHERE id = :id
        ");
        
        $stmt->execute([
            'name' => trim($input['name']),
            'code' => trim($input['code']),
            'description' => isset($input['description']) ? trim($input['description']) : null,
            'credits' => isset($input['credits']) && is_numeric($input['credits']) ? (int)$input['credits'] : null,
            'id' => $id
        ]);
        
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $course = $stmt->fetch();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update course']);
    }
}

function deleteCourse($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete course']);
    }
}

function exportCoursesCSV($pdo, $query, $params) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="courses_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Code', 'Description', 'Credits', 'Created At']);
        
        foreach ($courses as $course) {
            fputcsv($output, [
                $course['id'],
                $course['name'],
                $course['code'],
                $course['description'],
                $course['credits'],
                $course['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to export courses']);
    }
}
