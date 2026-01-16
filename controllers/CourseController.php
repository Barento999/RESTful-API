<?php
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../config.php';

class CourseController {
    private $model;
    
    public function __construct($pdo) {
        $this->model = new Course($pdo);
    }
    
    public function index() {
        $search = $_GET['search'] ?? null;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? 
                 min(MAX_PAGE_SIZE, max(1, (int)$_GET['limit'])) : DEFAULT_PAGE_SIZE;
        
        $result = $this->model->getAll($search, $page, $limit);
        
        $this->jsonResponse(200, [
            'success' => true,
            'data' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'pages' => $result['pages']
            ]
        ]);
    }
    
    public function show($id) {
        $course = $this->model->getById($id);
        
        if (!$course) {
            $this->jsonResponse(404, ['error' => 'Course not found']);
            return;
        }
        
        $this->jsonResponse(200, ['success' => true, 'data' => $course]);
    }
    
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $validation = $this->validateCourse($input);
        if ($validation !== true) {
            $this->jsonResponse(400, ['error' => $validation]);
            return;
        }
        
        if ($this->model->codeExists($input['code'])) {
            $this->jsonResponse(409, ['error' => 'Course code already exists']);
            return;
        }
        
        $course = $this->model->create($input);
        
        $this->jsonResponse(201, [
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course
        ]);
    }
    
    public function update($id) {
        $course = $this->model->getById($id);
        if (!$course) {
            $this->jsonResponse(404, ['error' => 'Course not found']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $validation = $this->validateCourse($input);
        if ($validation !== true) {
            $this->jsonResponse(400, ['error' => $validation]);
            return;
        }
        
        if ($this->model->codeExists($input['code'], $id)) {
            $this->jsonResponse(409, ['error' => 'Course code already exists']);
            return;
        }
        
        $course = $this->model->update($id, $input);
        
        $this->jsonResponse(200, [
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    }
    
    public function destroy($id) {
        $course = $this->model->getById($id);
        if (!$course) {
            $this->jsonResponse(404, ['error' => 'Course not found']);
            return;
        }
        
        $this->model->delete($id);
        
        $this->jsonResponse(200, [
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
    
    private function validateCourse($data) {
        $required = ['name', 'code'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return ucfirst($field) . ' is required';
            }
        }
        
        return true;
    }
    
    private function jsonResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data);
    }
}
