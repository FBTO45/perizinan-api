<?php
class Database {
    private $host = 'localhost';
    // Make sure to replace 'perizinan_api' with your actual database name.
    private $db_name = 'perizinan_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Re-throw the exception to be handled by a global error handler.
            // This prevents halting the application and makes it more testable.
            throw $e;
        }

        return $this->conn;
    }
}