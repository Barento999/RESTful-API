<?php

class Admin {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    public function verifyPassword($hashedPassword, $password) {
        return password_verify($password, $hashedPassword);
    }
}
