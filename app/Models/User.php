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
            ORDER BY full_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUserCounts()
    {
        $stmt = $this->db->prepare("
            SELECT
                SUM(role = 'admin') AS admins,
                SUM(role = 'agent' AND is_admin_agent = 0) AS agents,
                SUM(role = 'agent' AND is_admin_agent = 1) AS admin_agents,
                SUM(role = 'user') AS users
            FROM users
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
        SELECT *

        FROM activity_logs

        WHERE user_id=?

        ORDER BY created_at DESC

        LIMIT $limit
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

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM authenticator_secrets WHERE user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM mfa_recovery_otps WHERE user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM login_otps WHERE user_id = ?");
            $stmt->execute([$userId]);

            if (!empty($user['email'])) {
                $stmt = $this->db->prepare("DELETE FROM password_reset_otps WHERE email = ?");
                $stmt->execute([$user['email']]);
            }

            $stmt = $this->db->prepare("DELETE FROM activity_logs WHERE user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM reply_attachments WHERE uploaded_by = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM ticket_attachments WHERE uploaded_by = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM ticket_replies WHERE user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("DELETE FROM ticket_status_history WHERE changed_by = ?");
            $stmt->execute([$userId]);

            $stmt = $this->db->prepare("SELECT id FROM tickets WHERE user_id = ?");
            $stmt->execute([$userId]);
            $ticketIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($ticketIds)) {
                $inClause = implode(',', array_map('intval', $ticketIds));

                $this->db->exec("DELETE FROM reply_attachments WHERE ticket_id IN ($inClause)");
                $this->db->exec("DELETE FROM ticket_attachments WHERE ticket_id IN ($inClause)");
                $this->db->exec("DELETE FROM ticket_replies WHERE ticket_id IN ($inClause)");
                $this->db->exec("DELETE FROM ticket_status_history WHERE ticket_id IN ($inClause)");

                $stmt = $this->db->prepare("DELETE FROM tickets WHERE user_id = ?");
                $stmt->execute([$userId]);
            }

            $stmt = $this->db->prepare("UPDATE tickets SET closed_by_agent_id = NULL WHERE closed_by_agent_id = ?");
            $stmt->execute([$userId]);

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
}
