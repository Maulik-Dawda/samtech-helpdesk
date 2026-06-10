<?php

class AuthMiddleware
{
    public static function check($role = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['auth_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        if ($role !== null && $_SESSION['auth_user_role'] !== $role) {
            http_response_code(403);

            require_once ROOT_PATH . "/app/Views/errors/403.php";

            exit;
        }
    }

    public static function guest()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['auth_user_id'])) {
            $role = $_SESSION['auth_user_role'];

            if ($role === 'admin') {
                header("Location: " . BASE_URL . "/admin-dashboard");
                exit;
            }

            if ($role === 'agent') {
                header("Location: " . BASE_URL . "/agent-dashboard");
                exit;
            }

            header("Location: " . BASE_URL . "/user-dashboard");
            exit;
        }
    }

    public static function timeout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $timeout = 1800; // 30 minutes

        if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
            session_unset();
            session_destroy();

            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}
