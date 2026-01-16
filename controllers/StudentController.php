<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../config.php';

class StudentController {
    private $model;
    
    public function __construct($pdo) {
        $this->model = new Student($pdo);
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
        $student = $this->model->getById($id);
        
        if (!$student) {
            $this->jsonResponse(404, ['error' => 'Student not found']);
            return;
        }
        
        $this->jsonResponse(200, ['success' => true, 'data' => $student]);
    }
    
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $validation = $this->validateStudent($input);
        if ($validation !== true) {
            $this->jsonResponse(400, ['error' => $validation]);
            return;
        }
        
        if ($this->model->emailExists($input['email'])) {
            $this->jsonResponse(409, ['error' => 'Email already exists']);
            return;
        }
        
        $student = $this->model->create($input);
        
        $this->jsonResponse(201, [
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ]);
    }
    
    public function update($id) {
        $student = $this->model->getById($id);
        if (!$student) {
            $this->jsonResponse(404, ['error' => 'Student not found']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $validation = $this->validateStudent($input);
        if ($validation !== true) {
            $this->jsonResponse(400, ['error' => $validation]);
            return;
        }
        
        if ($this->model->emailExists($input['email'], $id)) {
            $this->jsonResponse(409, ['error' => 'Email already exists']);
            return;
        }
        
        $student = $this->model->update($id, $input);
        
        $this->jsonResponse(200, [
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }
    
    public function destroy($id) {
        $student = $this->model->getById($id);
        if (!$student) {
            $this->jsonResponse(404, ['error' => 'Student not found']);
            return;
        }
        
        $this->model->delete($id);
        
        $this->jsonResponse(200, [
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }
    
    private function validateStudent($data) {
        $required = ['first_name', 'last_name', 'email'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }
        
        return true;
    }
    
    private function jsonResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data);
    }
}
