<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class User extends Model
{
    protected $table = 'users';

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                full_name,
                email,
                password,
                role,
                is_admin_agent
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            $data['is_admin_agent'] ?? 0
        ]);
    }

    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET last_login_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$userId]);
    }

    public function updatePassword($userId, $password)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            password_hash($password, PASSWORD_BCRYPT),
            $userId
        ]);
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        return $stmt->fetch() ? true : false;
    }

    public function findWithOrganization($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                users.*,
                organizations.name AS organization_name
            FROM users
            LEFT JOIN organizations
                ON organizations.id = users.organization_id
            WHERE users.id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function findByIdWithOrganization($id)
    {
        return $this->findWithOrganization($id);
    }

    public function getOrganizationUsers($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                users.*,
                organizations.name AS organization_name
            FROM users
            LEFT JOIN organizations 
                ON organizations.id = users.organization_id
            WHERE users.organization_id = ?
            AND users.role = 'user'
            AND users.email != 'maulik@septixtechnologies.com'
            ORDER BY users.created_at DESC
        ");

        $stmt->execute([$organizationId]);

        return $stmt->fetchAll();
    }

    public function countOrganizationUsers($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM users
            WHERE organization_id = ?
            AND role = 'user'
            AND email != 'maulik@septixtechnologies.com'
        ");

        $stmt->execute([$organizationId]);

        $result = $stmt->fetch();

        return (int)($result['total'] ?? 0);
    }

    public function createOrganizationUser($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                organization_id,
                full_name,
                email,
                password,
                role,
                is_admin_agent,
                is_organization_admin,
                is_email_verified,
                is_active
            )
            VALUES (?, ?, ?, ?, 'user', 0, 0, 1, 1)
        ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }

    public function getPermissionAssignableUsers()
    {
        $stmt = $this->db->prepare("
            SELECT 
                users.*,
                organizations.name AS organization_name
            FROM users
            LEFT JOIN organizations 
                ON organizations.id = users.organization_id
            WHERE users.role IN ('user', 'agent')
            AND users.email != 'maulik@septixtechnologies.com'
            ORDER BY users.role ASC, users.full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllUsersForAdmin()
    {
        $stmt = $this->db->prepare("
            SELECT 
                users.*,
                organizations.name AS organization_name
            FROM users
            LEFT JOIN organizations 
                ON organizations.id = users.organization_id
            WHERE users.email != 'maulik@septixtechnologies.com'
            ORDER BY users.role ASC, users.full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function createByAdmin($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                organization_id,
                full_name,
                email,
                password,
                role,
                is_admin_agent,
                is_organization_admin,
                is_email_verified,
                is_active
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)
        ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            $data['is_admin_agent'] ?? 0,
            $data['is_organization_admin'] ?? 0
        ]);
    }

    public function updateUserByAdmin($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                organization_id = ?,
                full_name = ?,
                email = ?,
                role = ?,
                is_admin_agent = ?,
                is_organization_admin = ?,
                is_active = ?
            WHERE id = ?
            AND role != 'admin'
        ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            $data['role'],
            $data['is_admin_agent'] ?? 0,
            $data['is_organization_admin'] ?? 0,
            $data['is_active'],
            $id
        ]);
    }

    public function disableUserByAdmin($id)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = 0
            WHERE id = ?
            AND role != 'admin'
        ");

        return $stmt->execute([$id]);
    }

    public function getUsersByRole($role)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = ?
            AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute([$role]);

        return $stmt->fetchAll();
    }

    public function getAgents()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = 'agent'
            AND is_active = 1
            AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getNormalAgents()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = 'agent'
            AND is_admin_agent = 0
            AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAdminAgents()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = 'agent'
            AND is_admin_agent = 1
            AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUserCounts()
    {
        $stmt = $this->db->prepare("
            SELECT
                SUM(role = 'admin' AND email != 'maulik@septixtechnologies.com') AS admins,
                SUM(role = 'agent' AND is_admin_agent = 0 AND email != 'maulik@septixtechnologies.com') AS agents,
                SUM(role = 'agent' AND is_admin_agent = 1 AND email != 'maulik@septixtechnologies.com') AS admin_agents,
                SUM(role = 'user' AND email != 'maulik@septixtechnologies.com') AS users
            FROM users
            WHERE email != 'maulik@septixtechnologies.com'
        ");

        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAllUsersForAgent()
    {
        $stmt = $this->db->prepare("
            SELECT
                users.*,
                organizations.name AS organization_name
            FROM users
            LEFT JOIN organizations
                ON organizations.id = users.organization_id
            WHERE users.role = 'user'
            AND users.email != 'maulik@septixtechnologies.com'
            ORDER BY users.created_at DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function createByAgent($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                organization_id,
                full_name,
                email,
                password,
                role,
                is_admin_agent,
                is_organization_admin,
                is_email_verified,
                is_active
            )
            VALUES (?, ?, ?, ?, 'user', 0, ?, 1, 1)
        ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['is_organization_admin'] ?? 0
        ]);
    }

    public function updateUserByAgent($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                organization_id = ?,
                full_name = ?,
                email = ?,
                is_organization_admin = ?,
                is_active = ?
            WHERE id = ?
            AND role = 'user'
        ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            $data['is_organization_admin'] ?? 0,
            $data['is_active'],
            $id
        ]);
    }

    public function disableUserByAgent($id)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = 0
            WHERE id = ?
            AND role = 'user'
        ");

        return $stmt->execute([$id]);
    }

    public function toggleUserStatusByAgent($id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = ?
            WHERE id = ?
            AND role = 'user'
        ");

        return $stmt->execute([
            $status,
            $id
        ]);
    }
    public function getProfile($id)
    {
        $stmt = $this->db->prepare("
        SELECT
            users.*,
            organizations.name AS organization_name
        FROM users
        LEFT JOIN organizations
            ON organizations.id = users.organization_id
        WHERE users.id=?
        LIMIT 1
    ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }
    public function getTicketStatistics($userId)
    {
        $stmt = $this->db->prepare("
        SELECT

        COUNT(*) total,

        SUM(status='open') open_count,

        SUM(status='in_progress') in_progress_count,

        SUM(status='pending') pending_count,

        SUM(status='resolved') resolved_count,

        SUM(status='closed') closed_count

        FROM tickets

        WHERE user_id=?

    ");

        $stmt->execute([$userId]);

        return $stmt->fetch();
    }
    public function getRecentTickets($userId, $limit = 10)
    {
        $stmt = $this->db->prepare("
        SELECT *

        FROM tickets

        WHERE user_id=?

        ORDER BY created_at DESC

        LIMIT $limit
    ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
    public function getActivityLogs($userId, $limit = 30)
    {
        $stmt = $this->db->prepare("
        SELECT activity_logs.*
        FROM activity_logs
        LEFT JOIN users ON users.id = activity_logs.user_id
        WHERE activity_logs.user_id = ?
        AND (users.email IS NULL OR users.email != 'maulik@septixtechnologies.com')
        ORDER BY activity_logs.created_at DESC
        LIMIT " . (int)$limit . "
    ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
    public function getAgentStatistics($agentId)
    {
        $stmt = $this->db->prepare("
        SELECT

        COUNT(*) total,

        SUM(status='closed') closed_count,

        SUM(status='resolved') resolved_count,

        SUM(status='pending') pending_count,

        SUM(status='in_progress') in_progress_count

        FROM tickets

        WHERE closed_by_agent_id=?

    ");

        $stmt->execute([$agentId]);

        return $stmt->fetch();
    }

    public function deleteUserCompletely($userId)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return false;
        }

        $user = $this->findById($userId);
        if (!$user || $user['role'] === 'admin') {
            return false;
        }

        $safeQuery = function($sql, $params = []) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            } catch (Throwable $e) {
                error_log("User deletion cleanup notice ($sql): " . $e->getMessage());
            }
        };

        try {
            $this->db->beginTransaction();

            // Clean up session and auth tokens for this user
            $safeQuery("DELETE FROM user_permissions WHERE user_id = ?", [$userId]);
            $safeQuery("DELETE FROM authenticator_secrets WHERE user_id = ?", [$userId]);
            $safeQuery("DELETE FROM mfa_recovery_otps WHERE user_id = ?", [$userId]);
            $safeQuery("DELETE FROM login_otps WHERE user_id = ?", [$userId]);

            if (!empty($user['email'])) {
                $safeQuery("DELETE FROM password_reset_otps WHERE email = ?", [$user['email']]);
            }

            $safeQuery("DELETE FROM activity_logs WHERE user_id = ?", [$userId]);

            // DO NOT DELETE TICKETS OR REPLIES - PRESERVE ALL TICKETS IN DATABASE
            // Set user references to NULL (or detach user association) so all tickets, replies, attachments, and status history stay saved in the database forever
            $safeQuery("UPDATE tickets SET user_id = NULL WHERE user_id = ?", [$userId]);
            $safeQuery("UPDATE tickets SET created_by = NULL WHERE created_by = ?", [$userId]);
            $safeQuery("UPDATE tickets SET assigned_agent_id = NULL WHERE assigned_agent_id = ?", [$userId]);
            $safeQuery("UPDATE tickets SET closed_by_agent_id = NULL WHERE closed_by_agent_id = ?", [$userId]);

            $safeQuery("UPDATE ticket_replies SET user_id = NULL WHERE user_id = ?", [$userId]);
            $safeQuery("UPDATE ticket_status_history SET changed_by = NULL WHERE changed_by = ?", [$userId]);

            $safeQuery("UPDATE ticket_attachments SET uploaded_by = NULL WHERE uploaded_by = ?", [$userId]);
            $safeQuery("UPDATE reply_attachments SET uploaded_by = NULL WHERE uploaded_by = ?", [$userId]);

            // Delete user account row
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $result = $stmt->execute([$userId]);

            $this->db->commit();
            return $result;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error deleting user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    public function getRegularAgents()
    {
        $stmt = $this->db->prepare("
            SELECT id, full_name, email, role, is_admin_agent
            FROM users
            WHERE role = 'agent' 
              AND (is_admin_agent IS NULL OR is_admin_agent = 0)
              AND is_active = 1
              AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllActiveUsersByOrganization()
    {
        $stmt = $this->db->prepare("
            SELECT id, full_name, email, organization_id
            FROM users
            WHERE organization_id IS NOT NULL AND is_active = 1
            AND email != 'maulik@septixtechnologies.com'
            ORDER BY full_name ASC
        ");

        $stmt->execute();
        $users = $stmt->fetchAll();

        $grouped = [];
        foreach ($users as $u) {
            $orgId = (int)$u['organization_id'];
            if (!isset($grouped[$orgId])) {
                $grouped[$orgId] = [];
            }
            $grouped[$orgId][] = [
                'id' => (int)$u['id'],
                'full_name' => $u['full_name'],
                'email' => $u['email']
            ];
        }

        return $grouped;
    }

    public function ensureMfaLoginColumnExists()
    {
        try {
            $this->db->exec("ALTER TABLE `users` ADD COLUMN `login_type_mfa` TINYINT(1) DEFAULT 0");
        } catch (Throwable $e) {
            // Ignored if column exists
        }
    }

    public function toggleMfaLogin($userId, $enable)
    {
        $this->ensureMfaLoginColumnExists();
        $stmt = $this->db->prepare("
            UPDATE users
            SET login_type_mfa = ?
            WHERE id = ?
        ");

        return $stmt->execute([$enable ? 1 : 0, $userId]);
    }

    public function resetMfa($userId)
    {
        $this->ensureMfaLoginColumnExists();
        try {
            $stmt = $this->db->prepare("DELETE FROM authenticator_secrets WHERE user_id = ?");
            $stmt->execute([$userId]);
        } catch (Throwable $e) {
            // Ignored
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET mfa_secret = NULL
            WHERE id = ?
        ");

        return $stmt->execute([$userId]);
    }
}
