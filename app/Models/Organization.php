<?php

require_once "../app/Core/Model.php";

class Organization extends Model
{
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM organizations
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function getAllActive()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM organizations
            WHERE is_active = 1
            ORDER BY name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMaxUsers($organizationId)
    {
        $stmt = $this->db->prepare("
        SELECT max_users
        FROM organizations
        WHERE id = ?
        LIMIT 1
    ");

        $stmt->execute([$organizationId]);

        $result = $stmt->fetch();

        return $result ? (int)$result['max_users'] : 3;
    }
    public function getAll()
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM organizations
        ORDER BY created_at DESC
    ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO organizations
        (name, email, phone, address, max_users, is_active)
        VALUES (?, ?, ?, ?, ?, 1)
    ");

        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['max_users']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
        UPDATE organizations
        SET
            name = ?,
            email = ?,
            phone = ?,
            address = ?,
            max_users = ?,
            is_active = ?
        WHERE id = ?
    ");

        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['max_users'],
            $data['is_active'],
            $id
        ]);
    }

    public function toggleStatus($id, $status)
    {
        $stmt = $this->db->prepare("
        UPDATE organizations
        SET is_active = ?
        WHERE id = ?
    ");

        return $stmt->execute([
            $status,
            $id
        ]);
    }

    public function nameExists($name, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare("
            SELECT id
            FROM organizations
            WHERE name = ?
            AND id != ?
            LIMIT 1
        ");

            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->db->prepare("
            SELECT id
            FROM organizations
            WHERE name = ?
            LIMIT 1
        ");

            $stmt->execute([$name]);
        }

        return $stmt->fetch() ? true : false;
    }
    public function countAll()
{
    $stmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM organizations
    ");

    $stmt->execute();

    $result = $stmt->fetch();

    return (int)$result['total'];
}
}
