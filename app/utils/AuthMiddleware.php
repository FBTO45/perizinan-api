<?php

require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/Response.php';
class AuthMiddleware {
    public static function authenticate() {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            Response::error('Unauthorized', 401);
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // The secret key must match the one used for encoding in AuthController.
            // It's highly recommended to store this in a secure configuration file.
            // Example: Load from an environment variable or a config file.
            // This should be the same key used in AuthController.php
            $secretKey = getenv('JWT_SECRET') ?: 'your-super-secret-key-that-is-long-and-random';
            $decoded = \JWT::decode($token);
            return $decoded;
        } catch (Exception $e) {
            Response::error('Invalid token: ' . $e->getMessage(), 401);
        }
    }

    public static function authorize($roles) {
        $user = self::authenticate();
        
        if (!in_array($user->role, $roles)) {
            Response::error('Forbidden', 403);
        }
        
        return $user;
    }
}