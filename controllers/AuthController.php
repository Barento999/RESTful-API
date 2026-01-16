<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../jwt.php';

class AuthController {
    private $model;
    
    public function __construct($pdo) {
        $this->model = new Admin($pdo);
    }
    
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['email']) || !isset($input['password'])) {
            $this->jsonResponse(400, ['error' => 'Email and password are required']);
            return;
        }
        
        $email = trim($input['email']);
        $password = $input['password'];
        
        $admin = $this->model->getByEmail($email);
        
        if (!$admin || !$this->model->verifyPassword($admin['password'], $password)) {
            $this->jsonResponse(401, ['error' => 'Invalid credentials']);
            return;
        }
        
        $payload = [
            'admin_id' => $admin['id'],
            'email' => $admin['email'],
            'name' => $admin['name']
        ];
        
        $token = JWT::encode($payload);
        
        $this->jsonResponse(200, [
            'success' => true,
            'token' => $token,
            'admin' => [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email']
            ]
        ]);
    }
    
    private function jsonResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data);
    }
}
