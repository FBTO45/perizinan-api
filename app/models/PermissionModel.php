<?php
require_once __DIR__ . '/../config/Database.php';

class PermissionModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($userId, $title, $type, $description, $startDate = null, $endDate = null) {
        $query = 'INSERT INTO permissions 
                  (user_id, title, type, description, start_date, end_date) 
                  VALUES (:user_id, :title, :type, :description, :start_date, :end_date)';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function getUserPermissions($userId) {
        $query = 'SELECT * FROM permissions WHERE user_id = :user_id ORDER BY created_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionById($id) {
        $query = 'SELECT * FROM permissions WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $type, $description, $startDate = null, $endDate = null) {
        $query = 'UPDATE permissions SET 
                  title = :title, 
                  type = :type, 
                  description = :description, 
                  start_date = :start_date, 
                  end_date = :end_date 
                  WHERE id = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = 'DELETE FROM permissions WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function updateStatus($id, $status, $comment = null) {
        $query = 'UPDATE permissions SET 
                  status = :status, 
                  admin_comment = :admin_comment 
                  WHERE id = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':admin_comment', $comment);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function getAllPermissions($status = null) {
        $query = 'SELECT p.*, u.name as user_name FROM permissions p 
                  JOIN users u ON p.user_id = u.id';
        
        if ($status) {
            $query .= ' WHERE p.status = :status';
        }
        
        $query .= ' ORDER BY p.created_at DESC';
        
        $stmt = $this->db->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}