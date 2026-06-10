<?php

require_once ROOT_PATH . "/app/Models/Permission.php";

class PermissionHelper
{
    public static function has($permissionKey)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['auth_user_id'])) {
            return false;
        }

        /*
        Admin has every permission automatically
        */

        if (
            isset($_SESSION['auth_user_role']) &&
            $_SESSION['auth_user_role'] === 'admin'
        ) {
            return true;
        }

        $permissionModel = new Permission();

        return $permissionModel->userHasPermission(
            $_SESSION['auth_user_id'],
            $permissionKey
        );
    }

    public static function require($permissionKey)
    {
        if (!self::has($permissionKey)) {

            http_response_code(403);

            require_once ROOT_PATH . "/app/Views/errors/403.php";

            exit;
        }
    }
}
