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

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                full_name,
                email,
                password,
                role
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role']
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
    public function updatePassword($userId, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
        UPDATE users
        SET password = ?
        WHERE id = ?
    ");

        return $stmt->execute([
            $hashedPassword,
            $userId
        ]);
    }

    public function findByIdWithOrganization($id)
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

        return (int)$result['total'];
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
            is_organization_admin,
            is_email_verified,
            is_active
        )
        VALUES (?, ?, ?, ?, 'user', 0, 1, 1)
    ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
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
            is_organization_admin,
            is_email_verified,
            is_active
        )
        VALUES (?, ?, ?, ?, ?, ?, 1, 1)
    ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            $data['is_organization_admin']
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
            $data['is_organization_admin'],
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
    public function getUserCounts()
    {
        $stmt = $this->db->prepare("
        SELECT
            SUM(role = 'admin') AS admins,
            SUM(role = 'agent') AS agents,
            SUM(role = 'user') AS users
        FROM users
    ");

        $stmt->execute();

        return $stmt->fetch();
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
    public function getAgents()
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM users
        WHERE role = 'agent'
        AND is_active = 1
    ");

        $stmt->execute();

        return $stmt->fetchAll();
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
            is_organization_admin,
            is_email_verified,
            is_active
        )
        VALUES (?, ?, ?, ?, 'user', ?, 1, 1)
    ");

        return $stmt->execute([
            $data['organization_id'],
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['is_organization_admin']
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
            $data['is_organization_admin'],
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
}
