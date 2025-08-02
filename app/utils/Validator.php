<?php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    public static function validatePermissionData($data) {
        $required = ['title', 'type', 'description'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}