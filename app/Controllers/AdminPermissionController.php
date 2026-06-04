<?php

require_once "../app/Core/Controller.php";
require_once "../app/Models/User.php";
require_once "../app/Models/Permission.php";

class AdminPermissionController extends Controller
{
    private function adminGuard()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('admin');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $this->adminGuard();

        $userModel = new User();

        $users = $userModel->getPermissionAssignableUsers();

        $this->view('admin/permissions/index', [
            'users' => $users
        ]);
    }

    public function edit($id)
    {
        $this->adminGuard();

        $userModel = new User();
        $permissionModel = new Permission();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] === 'admin') {
            http_response_code(403);
            echo "Admin permissions cannot be edited.";
            exit;
        }

        $permissions = $permissionModel->getAll();
        $userPermissions = $permissionModel->getUserPermissions($id);

        $selectedPermissionIds = array_column(
            $userPermissions,
            'id'
        );

        $this->view('admin/permissions/edit', [
            'user' => $user,
            'permissions' => $permissions,
            'selectedPermissionIds' => $selectedPermissionIds
        ]);
    }

    public function update($id)
    {
        Csrf::verify();

        $this->adminGuard();

        $userModel = new User();
        $permissionModel = new Permission();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] === 'admin') {
            http_response_code(403);
            echo "Admin permissions cannot be edited.";
            exit;
        }

        $permissionIds = $_POST['permissions'] ?? [];

        if (!is_array($permissionIds)) {
            $permissionIds = [];
        }

        $permissionIds = array_map(
            'intval',
            $permissionIds
        );

        $permissionModel->syncUserPermissions(
            $id,
            $permissionIds,
            $_SESSION['auth_user_id']
        );

        $_SESSION['success'] = "Permissions updated successfully.";

        header(
            "Location: " .
            BASE_URL .
            "/admin/permissions"
        );

        exit;
    }
}