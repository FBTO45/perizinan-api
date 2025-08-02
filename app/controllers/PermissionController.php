<?php
require_once __DIR__ . '/../models/PermissionModel.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/AuthMiddleware.php';
require_once __DIR__ . '/../utils/Validator.php';

class PermissionController {
    private $permissionModel;

    public function __construct() {
        $this->permissionModel = new PermissionModel();
    }

    public function createPermission() {
        $user = AuthMiddleware::authorize(['user']);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!Validator::validatePermissionData($data)) {
            Response::error('Missing required fields');
        }

        $permissionId = $this->permissionModel->create(
            $user->id,
            $data['title'],
            $data['type'],
            $data['description'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        );

        if ($permissionId) {
            Response::json(['message' => 'Permission created successfully', 'id' => $permissionId], 201);
        } else {
            Response::error('Failed to create permission', 500);
        }
    }

    public function getUserPermissions() {
        $user = AuthMiddleware::authenticate();
        $permissions = $this->permissionModel->getUserPermissions($user->id);
        Response::json($permissions);
    }

    public function updatePermission($id) {
        $user = AuthMiddleware::authorize(['user']);
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if permission belongs to user
        $permission = $this->permissionModel->getPermissionById($id);
        if (!$permission || $permission['user_id'] != $user->id) {
            Response::error('Permission not found', 404);
        }

        if ($permission['status'] !== 'pending') {
            Response::error('Cannot update non-pending permission');
        }

        if ($this->permissionModel->update(
            $id,
            $data['title'],
            $data['type'],
            $data['description'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        )) {
            Response::json(['message' => 'Permission updated successfully']);
        } else {
            Response::error('Failed to update permission', 500);
        }
    }

    public function deletePermission($id) {
        $user = AuthMiddleware::authorize(['user']);
        
        // Check if permission belongs to user
        $permission = $this->permissionModel->getPermissionById($id);
        if (!$permission || $permission['user_id'] != $user->id) {
            Response::error('Permission not found', 404);
        }

        if ($permission['status'] !== 'pending') {
            Response::error('Cannot delete non-pending permission');
        }

        if ($this->permissionModel->delete($id)) {
            Response::json(['message' => 'Permission deleted successfully']);
        } else {
            Response::error('Failed to delete permission', 500);
        }
    }

    public function getAllPermissions() {
        $verifikator = AuthMiddleware::authorize(['verifikator', 'admin']);
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        $permissions = $this->permissionModel->getAllPermissions($status);
        Response::json($permissions);
    }

    public function updatePermissionStatus($id) {
        $verifikator = AuthMiddleware::authorize(['verifikator']);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!in_array($data['status'], ['approved', 'rejected', 'revised'])) {
            Response::error('Invalid status');
        }

        if ($this->permissionModel->updateStatus($id, $data['status'], $data['comment'] ?? null)) {
            Response::json(['message' => 'Permission status updated successfully']);
        } else {
            Response::error('Failed to update permission status', 500);
        }
    }

    // Tambahkan method berikut ke PermissionController
    public function getPermissionDetail($id)
    {
        $user = AuthMiddleware::authenticate();
        $permission = $this->permissionModel->getPermissionById($id);

        if (!$permission) {
            Response::error('Permission not found', 404);
        }

        // Verifikasi kepemilikan (kecuali admin/verifikator)
        if ($user->role === 'user' && $permission['user_id'] != $user->id) {
            Response::error('Forbidden', 403);
        }

        Response::json($permission);
    }

    public function cancelPermission($id)
    {
        $user = AuthMiddleware::authorize(['user']);
        $permission = $this->permissionModel->getPermissionById($id);

        if (!$permission || $permission['user_id'] != $user->id) {
            Response::error('Permission not found', 404);
        }

        if ($permission['status'] !== 'pending') {
            Response::error('Only pending permissions can be cancelled');
        }

        if ($this->permissionModel->updateStatus($id, 'cancelled')) {
            Response::json(['message' => 'Permission cancelled successfully']);
        } else {
            Response::error('Failed to cancel permission', 500);
        }
    }

}