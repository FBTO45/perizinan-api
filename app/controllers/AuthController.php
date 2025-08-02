<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

use Firebase\JWT\JWT;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!Validator::validateEmail($data['email'])) {
            Response::error('Invalid email format');
        }

        if (!Validator::validatePassword($data['password'])) {
            Response::error('Password must be at least 6 characters');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            Response::error('Email already exists');
        }

        if ($this->userModel->create($data['name'], $data['email'], $data['password'])) {
            Response::json(['message' => 'User registered successfully'], 201);
        } else {
            Response::error('Registration failed', 500);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::error('Invalid credentials', 401);
        }

        // Generate JWT token
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'exp' => time() + (60 * 60 * 24) // 1 day
        ];

        // It's highly recommended to store your secret key in a secure configuration file or environment variable
        // and not hardcode it in the source code.
        // Example: Load from an environment variable or a config file.
        // For security, this key should be long, random, and kept secret.
        $secretKey = getenv('JWT_SECRET') ?: 'your-super-secret-key-that-is-long-and-random';
        $token = JWT::encode($payload, $secretKey, 'HS256');

        Response::json([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    // Tambahkan method berikut ke AuthController
    public function logout() {
        // For stateless JWT, logout is handled on the client-side by deleting the token.
        Response::json(['message' => 'Logout successful']);
    }
}