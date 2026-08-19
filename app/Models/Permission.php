<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class Permission extends Model
{
    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM permissions
            ORDER BY module_name ASC, permission_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUserPermissions($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                permissions.*
            FROM user_permissions
            JOIN permissions 
                ON permissions.id = user_permissions.permission_id
            WHERE user_permissions.user_id = ?
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function userHasPermission($userId, $permissionKey)
    {
        $stmt = $this->db->prepare("
            SELECT permissions.id
            FROM user_permissions
            JOIN permissions 
                ON permissions.id = user_permissions.permission_id
            WHERE user_permissions.user_id = ?
            AND permissions.permission_key = ?
            LIMIT 1
        ");

        $stmt->execute([
            $userId,
            $permissionKey
        ]);

        return $stmt->fetch() ? true : false;
    }

    public function syncUserPermissions($userId, $permissionIds, $grantedBy)
    {
        $deleteStmt = $this->db->prepare("
            DELETE FROM user_permissions
            WHERE user_id = ?
        ");

        $deleteStmt->execute([$userId]);

        if (empty($permissionIds)) {
            return true;
        }

        $insertStmt = $this->db->prepare("
            INSERT INTO user_permissions
            (user_id, permission_id, granted_by)
            VALUES (?, ?, ?)
        ");

        foreach ($permissionIds as $permissionId) {
            $insertStmt->execute([
                $userId,
                $permissionId,
                $grantedBy
            ]);
        }

        return true;
    }
}