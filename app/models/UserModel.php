<?php
require_once __DIR__ . '/../config/Database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($name, $email, $password, $role = 'user') {
        $query = 'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)';
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed_password);

        return $stmt->execute();
    }

    public function findByEmail($email) {
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = 'SELECT id, name, email, role, is_verified, created_at FROM users';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole($userId, $role) {
        $query = 'UPDATE users SET role = :role WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId);
        
        return $stmt->execute();
    }

    public function verifyUser($userId) {
        $query = 'UPDATE users SET is_verified = TRUE WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        
        return $stmt->execute();
    }

    public function updatePassword($userId, $newPassword) {
        $query = 'UPDATE users SET password = :password WHERE id = :id';
        $stmt = $this->db->prepare($query);
        
        $hashed_password = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $userId);
        
        return $stmt->execute();
    }
}