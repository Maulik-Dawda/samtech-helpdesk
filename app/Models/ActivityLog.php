<?php

require_once "../app/Core/Model.php";

class ActivityLog extends Model
{
    public function create($userId, $action, $ipAddress = null, $userAgent = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs
            (user_id, action, ip_address, user_agent)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $action,
            $ipAddress,
            $userAgent
        ]);
    }
    
    public function getRecent($limit = 8)
{
    $stmt = $this->db->prepare("
        SELECT 
            activity_logs.*,
            users.full_name,
            users.role
        FROM activity_logs
        LEFT JOIN users ON users.id = activity_logs.user_id
        ORDER BY activity_logs.created_at DESC
        LIMIT " . (int)$limit
        );

    $stmt->execute();

    return $stmt->fetchAll();
    }
    public function getFilteredLogs($filters = [])
{
    $sql = "
        SELECT 
            activity_logs.*,
            users.full_name,
            users.email,
            users.role
        FROM activity_logs
        LEFT JOIN users ON users.id = activity_logs.user_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($filters['user_id'])) {
        $sql .= " AND activity_logs.user_id = ?";
        $params[] = $filters['user_id'];
    }

    if (!empty($filters['role'])) {
        $sql .= " AND users.role = ?";
        $params[] = $filters['role'];
    }

    if (!empty($filters['action'])) {
        $sql .= " AND activity_logs.action LIKE ?";
        $params[] = "%" . $filters['action'] . "%";
    }

    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(activity_logs.created_at) >= ?";
        $params[] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(activity_logs.created_at) <= ?";
        $params[] = $filters['date_to'];
    }

    $sql .= " ORDER BY activity_logs.created_at DESC LIMIT 300";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
}