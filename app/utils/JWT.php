<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static $secretKey = 'your-secret-key';
    private static $algorithm = 'HS256';

    public static function encode($payload) {
        return FirebaseJWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    public static function decode($token) {
        try {
            return FirebaseJWT::decode($token, new Key(self::$secretKey, self::$algorithm));
        } catch (Exception $e) {
            return null;
        }
    }
}