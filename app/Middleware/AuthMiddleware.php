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

        if ($role !== null) {

            $userRole = $_SESSION['auth_user_role'] ?? '';

            // Multiple roles allowed
            if (is_array($role)) {

                if (!in_array($userRole, $role)) {
                    http_response_code(403);
                    require_once ROOT_PATH . "/app/Views/errors/403.php";
                    exit;
                }

            } else {

                // Single role
                if ($userRole !== $role) {
                    http_response_code(403);
                    require_once ROOT_PATH . "/app/Views/errors/403.php";
                    exit;
                }
            }
        }
    }

    public static function guest()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['auth_user_id'])) {
            return;
        }

        self::redirectByRole($_SESSION['auth_user_role']);
    }

    public static function timeout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $timeout = SESSION_TIMEOUT;

        if (
            isset($_SESSION['last_activity']) &&
            (time() - $_SESSION['last_activity']) > $timeout
        ) {

            session_unset();
            session_destroy();

            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Redirect user based on role
     */
    public static function redirectByRole($role)
    {
        switch ($role) {

            case 'admin':
                header("Location: " . BASE_URL . "/admin-dashboard");
                break;

            case 'admin_agent':
                header("Location: " . BASE_URL . "/admin-agent-dashboard");
                break;

            case 'agent':
                header("Location: " . BASE_URL . "/agent-dashboard");
                break;

            case 'user':
            default:
                header("Location: " . BASE_URL . "/user-dashboard");
                break;
        }

        exit;
    }
}