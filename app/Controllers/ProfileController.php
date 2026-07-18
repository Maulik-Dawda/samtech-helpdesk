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
    public function changePassword()
    {
        $this->authGuard();

        $this->view('profile/change-password');
    }
    public function updatePassword()
    {
        $this->authGuard();

        Csrf::verify();

        $currentPassword = trim($_POST['current_password'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (
            empty($currentPassword) ||
            empty($password) ||
            empty($confirmPassword)
        ) {
            $_SESSION['error'] = 'All fields are required.';
            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        if ($password !== $confirmPassword) {

            $_SESSION['error'] = 'New passwords do not match.';
            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        if (
            !preg_match(
                '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                $password
            )
        ) {

            $_SESSION['error'] =
                'Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.';

            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        $userModel = new User();

        $user = $userModel->findById($_SESSION['auth_user_id']);

        if (!$user) {

            $_SESSION['error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        if (!password_verify($currentPassword, $user['password'])) {

            $_SESSION['error'] = 'Current password is incorrect.';
            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        if (password_verify($password, $user['password'])) {

            $_SESSION['error'] =
                'New password cannot be the same as your current password.';

            header('Location: ' . BASE_URL . '/profile/change-password');
            exit;
        }

        $userModel->updatePassword(
            $_SESSION['auth_user_id'],
            $password
        );

        MailService::sendPasswordChangedNotification(
            $user['email'],
            $user['full_name'],
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );

        $activityLog = new ActivityLog();

        $activityLog->create(
            $_SESSION['auth_user_id'],
            'Changed account password',
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $_SESSION['success'] = 'Password updated successfully.';

        header('Location: ' . BASE_URL . '/profile');
        exit;
    }
}
