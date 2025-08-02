<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/Database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Load Response utility early to be available for global error handling
require_once __DIR__ . '/../app/utils/Response.php';

try {
$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Main endpoints
$endpoint = $uri[2] ?? '';
$param = $uri[3] ?? null;

// Load controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/PermissionController.php';

// Initialize controllers
$authController = new AuthController();
$userController = new UserController();
$permissionController = new PermissionController();

// Routing
switch ($endpoint) {
    case '':
        if ($requestMethod == 'GET') {
            Response::json(['message' => 'Welcome to the Perizinan API']);
        } else {
            Response::error('Method Not Allowed', 405);
        }
        break;
    case 'auth':
        if ($requestMethod == 'POST') {
            if ($uri[3] == 'register') {
                $authController->register();
            } elseif ($uri[3] == 'login') {
                $authController->login();
            } elseif ($uri[3] == 'logout') {
                $authController->logout();
            }
        }
        break;
        
    case 'users':
        $action = $uri[3] ?? null;
        $id = $uri[4] ?? null;

        if ($requestMethod == 'GET') {
            if ($action == 'profile') {
                $userController->getProfile();
            } elseif ($action == 'verified') {
                $userController->getVerifiedUsers();
            } elseif (empty($action)) {
                $userController->getAllUsers();
            }
        } elseif ($requestMethod == 'POST' && empty($action)) {
            $userController->createVerifikator();
        } elseif ($requestMethod == 'PUT') {
            if ($action == 'profile') {
                $userController->updateProfile();
            } elseif ($action == 'role' && isset($id)) {
                $userController->updateUserRole($id);
            } elseif ($action == 'verify' && isset($id)) {
                $userController->verifyUser($id);
            } elseif ($action == 'password' && isset($id)) {
                $userController->resetPassword($id);
            } elseif ($action == 'password') {
                $userController->updateOwnPassword();
            }
        }
        break;
        
    case 'permissions':
        $id = $uri[3] ?? null;
        $action = $uri[4] ?? null;

        if ($requestMethod == 'GET') {
            if (isset($id) && $id == 'all') {
                $permissionController->getAllPermissions();
            } elseif (isset($id)) {
                $permissionController->getPermissionDetail($id);
            } else {
                $permissionController->getUserPermissions();
            }
        } elseif ($requestMethod == 'POST') {
            if (isset($id) && $action == 'cancel') {
                $permissionController->cancelPermission($id);
            } elseif (empty($id)) {
                $permissionController->createPermission();
            }
        } elseif ($requestMethod == 'PUT') {
            if (isset($id) && $action == 'status') {
                $permissionController->updatePermissionStatus($id);
            } elseif (isset($id)) {
                $permissionController->updatePermission($id);
            }
        } elseif ($requestMethod == 'DELETE' && isset($id)) {
            $permissionController->deletePermission($id);
        }
        break;
        
    default:
        Response::error('Endpoint not found', 404);
        break;
}

if ($requestMethod == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

} catch (PDOException $e) {
    // In a production environment, you should log the error instead of showing details.
    // error_log('Database Error: ' . $e->getMessage());
    Response::error('A database error occurred. Please try again later.', 500);
} catch (Exception $e) {
    // Catch any other unexpected errors.
    // error_log('General Error: ' . $e->getMessage());
    Response::error('An internal server error occurred.', 500);
}