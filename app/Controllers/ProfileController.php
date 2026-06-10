<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Permission.php";

class ProfileController extends Controller
{
    private function authGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['auth_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }
    }

    public function index()
    {
        $this->authGuard();

        $userModel = new User();
        $permissionModel = new Permission();

        $user = $userModel->findByIdWithOrganization(
            $_SESSION['auth_user_id']
        );

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        $permissions = [];

        if ($user['role'] !== 'admin') {
            $permissions = $permissionModel->getUserPermissions($user['id']);
        }

        $this->view('profile/index', [
            'user' => $user,
            'permissions' => $permissions
        ]);
    }
}