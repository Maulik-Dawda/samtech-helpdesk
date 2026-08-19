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

        // Admin has every permission
        if (self::isAdmin()) {
            return true;
        }

        // Admin Agent has every permission except system administration
        if (
            self::isAdminAgent() &&
            !in_array($permissionKey, [
                'manage_admins',
                'manage_permissions',
                'system_configuration'
            ])
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

            require ROOT_PATH . "/app/Views/errors/403.php";

            exit;
        }
    }

    public static function isAdmin()
    {
        return ($_SESSION['auth_user_role'] ?? '') === 'admin';
    }

    public static function isAgent()
    {
        return ($_SESSION['auth_user_role'] ?? '') === 'agent';
    }

    public static function isAdminAgent()
    {
        return self::isAgent()
            && (($_SESSION['is_admin_agent'] ?? 0) == 1);
    }

    public static function isNormalAgent()
    {
        return self::isAgent()
            && (($_SESSION['is_admin_agent'] ?? 0) == 0);
    }

    public static function canManageUsers()
    {
        return self::isAdmin() || self::isAdminAgent();
    }

    public static function canManageAgents()
    {
        return self::isAdmin() || self::isAdminAgent();
    }

    public static function canManageOrganizations()
    {
        return self::isAdmin() || self::isAdminAgent();
    }

    public static function canViewActivityLogs()
    {
        return self::isAdmin() || self::isAdminAgent();
    }

    public static function canPrintReports()
    {
        return self::isAdmin() || self::isAdminAgent();
    }

    public static function canManageAdmins()
    {
        return self::isAdmin();
    }

    public static function canManagePermissions()
    {
        return self::isAdmin();
    }

    public static function canManageSystem()
    {
        return self::isAdmin();
    }
}