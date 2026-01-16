<?php

class Student {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll($search = null, $page = 1, $limit = 10) {
        $query = "SELECT * FROM students WHERE 1=1";
        $params = [];
        
        if ($search) {
            $search = '%' . trim($search) . '%';
            $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params['search'] = $search;
        }
        
        $countStmt = $this->pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $query));
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        $offset = ($page - 1) * $limit;
        $query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO students (first_name, last_name, email, phone, address, dob, gender) 
            VALUES (:first_name, :last_name, :email, :phone, :address, :dob, :gender)
        ");
        
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? null
        ]);
        
        return $this->getById($this->pdo->lastInsertId());
    }
    
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE students 
            SET first_name = :first_name, last_name = :last_name, email = :email, 
                phone = :phone, address = :address, dob = :dob, gender = :gender
            WHERE id = :id
        ");
        
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? null,
            'id' => $id
        ]);
        
        return $this->getById($id);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT id FROM students WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
}
