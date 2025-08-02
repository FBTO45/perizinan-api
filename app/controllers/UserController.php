<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/AuthMiddleware.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function getAllUsers() {
        $admin = AuthMiddleware::authorize(['admin']);
        $users = $this->userModel->getAllUsers();
        Response::json($users);
    }

    public function createVerifikator() {
        $admin = AuthMiddleware::authorize(['admin']);
        $data = json_decode(file_get_contents('php://input'), true);

        if ($this->userModel->create($data['name'], $data['email'], $data['password'], 'verifikator')) {
            Response::json(['message' => 'Verifikator created successfully'], 201);
        } else {
            Response::error('Failed to create verifikator', 500);
        }
    }

    public function updateUserRole($userId) {
        $admin = AuthMiddleware::authorize(['admin']);
        $data = json_decode(file_get_contents('php://input'), true);

        if ($this->userModel->updateRole($userId, $data['role'])) {
            Response::json(['message' => 'User role updated successfully']);
        } else {
            Response::error('Failed to update user role', 500);
        }
    }

    public function verifyUser($userId) {
        $verifikator = AuthMiddleware::authorize(['verifikator']);
        
        if ($this->userModel->verifyUser($userId)) {
            Response::json(['message' => 'User verified successfully']);
        } else {
            Response::error('Failed to verify user', 500);
        }
    }

    public function resetPassword($userId) {
        $admin = AuthMiddleware::authorize(['admin']);
        $data = json_decode(file_get_contents('php://input'), true);

        if ($this->userModel->updatePassword($userId, $data['new_password'])) {
            Response::json(['message' => 'Password reset successfully']);
        } else {
            Response::error('Failed to reset password', 500);
        }
    }

    public function updateOwnPassword() {
        $user = AuthMiddleware::authenticate();
        $data = json_decode(file_get_contents('php://input'), true);

        if ($this->userModel->updatePassword($user->id, $data['new_password'])) {
            Response::json(['message' => 'Password updated successfully']);
        } else {
            Response::error('Failed to update password', 500);
        }
    }

    public function getVerifiedUsers() {
        $verifikator = AuthMiddleware::authorize(['verifikator']);
        $verified = isset($_GET['verified']) ? $_GET['verified'] : null;
        
        $users = $this->userModel->getAllUsers();
        
        if ($verified !== null) {
            $verified = filter_var($verified, FILTER_VALIDATE_BOOLEAN);
            $users = array_filter($users, function($user) use ($verified) {
                return $user['is_verified'] === $verified;
            });
        }
        
        Response::json(array_values($users));
    }

    // Tambahkan method berikut ke UserController
    public function getProfile()
    {
        $user = AuthMiddleware::authenticate();
        $userData = $this->userModel->findById($user->id);

        // Hilangkan password dari response
        unset($userData['password']);

        Response::json($userData);
    }

    public function updateProfile()
    {
        $user = AuthMiddleware::authorize(['user']);
        $data = json_decode(file_get_contents('php://input'), true);

        // Pastikan ada method updateName di UserModel Anda
        // yang menangani pembaruan nama pengguna di database.
        if (isset($data['name']) && $this->userModel->updateName($user->id, $data['name'])) {
            Response::json(['message' => 'Profile updated successfully']);
        } else {
            // Berikan pesan error yang lebih spesifik jika data tidak valid atau gagal update
            Response::error('Failed to update profile', 500);
        }
    }

}