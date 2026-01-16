<?php

class Course {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll($search = null, $page = 1, $limit = 10) {
        $query = "SELECT * FROM courses WHERE 1=1";
        $params = [];
        
        if ($search) {
            $search = '%' . trim($search) . '%';
            $query .= " AND (name LIKE :search OR code LIKE :search)";
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
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO courses (name, code, description, credits) 
            VALUES (:name, :code, :description, :credits)
        ");
        
        $stmt->execute([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'credits' => $data['credits'] ?? null
        ]);
        
        return $this->getById($this->pdo->lastInsertId());
    }
    
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE courses 
            SET name = :name, code = :code, description = :description, credits = :credits
            WHERE id = :id
        ");
        
        $stmt->execute([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'credits' => $data['credits'] ?? null,
            'id' => $id
        ]);
        
        return $this->getById($id);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function codeExists($code, $excludeId = null) {
        $query = "SELECT id FROM courses WHERE code = :code";
        $params = ['code' => $code];
        
        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
}
