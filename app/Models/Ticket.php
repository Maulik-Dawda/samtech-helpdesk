<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class Ticket extends Model
{
    public function generateTicketNo()
    {
        return 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO tickets
        (
            ticket_no,
            user_id,
            organization_id,
            created_by,
            created_by_role,
            subject,
            description,
            priority,
            status
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

        return $stmt->execute([
            $data['ticket_no'],
            $data['user_id'],
            $data['organization_id'],
            $data['created_by'],
            $data['created_by_role'],
            $data['subject'],
            $data['description'],
            $data['priority'],
            $data['status']
        ]);
    }

    public function getUserTickets($userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function findById($ticketId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$ticketId]);

        return $stmt->fetch();
    }

    public function findUserTicket($ticketId, $userId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            tickets.*,
            closed_agent.full_name AS closed_by_agent_name
        FROM tickets
        LEFT JOIN users AS closed_agent 
            ON closed_agent.id = tickets.closed_by_agent_id
        WHERE tickets.id = ?
        AND tickets.user_id = ?
        LIMIT 1
    ");

        $stmt->execute([
            $ticketId,
            $userId
        ]);

        return $stmt->fetch();
    }
    public function getAllTicketsForAgent()
    {
        $stmt = $this->db->prepare("
        SELECT tickets.*, users.full_name AS customer_name
        FROM tickets
        JOIN users ON users.id = tickets.user_id
        ORDER BY tickets.created_at DESC
    ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findForAgent($ticketId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            tickets.*,
            users.full_name AS customer_name,
            users.email AS customer_email,
            closed_agent.full_name AS closed_by_agent_name
        FROM tickets
        JOIN users ON users.id = tickets.user_id
        LEFT JOIN users AS closed_agent 
            ON closed_agent.id = tickets.closed_by_agent_id
        WHERE tickets.id = ?
        LIMIT 1
    ");

        $stmt->execute([$ticketId]);

        return $stmt->fetch();
    }

    public function updateStatusByAgent($ticketId, $status, $agentId)
    {
        $closedAt = null;
        $resolvedAt = null;
        $closedBy = null;

        if ($status === 'closed') {
            $closedAt = date('Y-m-d H:i:s');
            $closedBy = $agentId;
        }

        if ($status === 'resolved') {
            $resolvedAt = date('Y-m-d H:i:s');
            $closedBy = $agentId;
        }

        $stmt = $this->db->prepare("
        UPDATE tickets
        SET status = ?,
            closed_at = ?,
            resolved_at = ?,
            closed_by_agent_id = ?
        WHERE id = ?
    ");

        return $stmt->execute([
            $status,
            $closedAt,
            $resolvedAt,
            $closedBy,
            $ticketId
        ]);
    }
    public function getOrganizationTickets($organizationId)
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM tickets
        WHERE organization_id = ?
        ORDER BY created_at DESC
    ");

        $stmt->execute([$organizationId]);

        return $stmt->fetchAll();
    }

    public function findOrganizationTicket($ticketId, $organizationId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            tickets.*,
            closed_agent.full_name AS closed_by_agent_name
        FROM tickets
        LEFT JOIN users AS closed_agent
            ON closed_agent.id = tickets.closed_by_agent_id
        WHERE tickets.id = ?
        AND tickets.organization_id = ?
        LIMIT 1
    ");

        $stmt->execute([
            $ticketId,
            $organizationId
        ]);

        return $stmt->fetch();
    }
    public function findByTicketNo($ticketNo)
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM tickets
        WHERE ticket_no = ?
        LIMIT 1
    ");

        $stmt->execute([$ticketNo]);

        return $stmt->fetch();
    }
    public function getReportTickets($filters = [])
    {
        $sql = "
        SELECT 
            tickets.*,
            users.full_name AS customer_name,
            users.email AS customer_email,
            organizations.name AS organization_name,
            closed_agent.full_name AS closed_by_agent_name
        FROM tickets
        LEFT JOIN users ON users.id = tickets.user_id
        LEFT JOIN organizations ON organizations.id = tickets.organization_id
        LEFT JOIN users AS closed_agent ON closed_agent.id = tickets.closed_by_agent_id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($filters['organization_id'])) {
            $sql .= " AND tickets.organization_id = ?";
            $params[] = $filters['organization_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND tickets.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['agent_id'])) {
            $sql .= " AND tickets.closed_by_agent_id = ?";
            $params[] = $filters['agent_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND tickets.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND tickets.priority = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(tickets.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(tickets.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY tickets.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
    public function findByIdForReport($ticketId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            tickets.*,
            users.full_name AS customer_name,
            users.email AS customer_email,
            organizations.name AS organization_name,
            closed_agent.full_name AS closed_by_agent_name
        FROM tickets
        LEFT JOIN users ON users.id = tickets.user_id
        LEFT JOIN organizations ON organizations.id = tickets.organization_id
        LEFT JOIN users AS closed_agent ON closed_agent.id = tickets.closed_by_agent_id
        WHERE tickets.id = ?
        LIMIT 1
    ");

        $stmt->execute([$ticketId]);

        return $stmt->fetch();
    }

    public function getDashboardCounts($organizationId = null)
    {
        $sql = "
        SELECT 
            COUNT(*) AS total,
            SUM(status = 'open') AS open_count,
            SUM(status = 'in_progress') AS in_progress_count,
            SUM(status = 'pending') AS pending_count,
            SUM(status = 'resolved') AS resolved_count,
            SUM(status = 'closed') AS closed_count
        FROM tickets
        WHERE 1=1
    ";

        $params = [];

        if ($organizationId) {
            $sql .= " AND organization_id = ?";
            $params[] = $organizationId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch();
    }

    public function getRecentTickets($limit = 5, $organizationId = null)
    {
        $sql = "
        SELECT 
            tickets.*,
            users.full_name AS customer_name,
            organizations.name AS organization_name
        FROM tickets
        LEFT JOIN users ON users.id = tickets.user_id
        LEFT JOIN organizations ON organizations.id = tickets.organization_id
        WHERE 1=1
    ";

        $params = [];

        if ($organizationId) {
            $sql .= " AND tickets.organization_id = ?";
            $params[] = $organizationId;
        }

        $sql .= " ORDER BY tickets.created_at DESC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
    public function getMonthlyTicketCounts($organizationId = null)
    {
        $sql = "
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            COUNT(*) AS total
        FROM tickets
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    ";

        $params = [];

        if ($organizationId) {
            $sql .= " AND organization_id = ?";
            $params[] = $organizationId;
        }

        $sql .= "
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getOrganizationTicketCounts()
    {
        $stmt = $this->db->prepare("
        SELECT 
            organizations.name,
            COUNT(tickets.id) AS total
        FROM organizations
        LEFT JOIN tickets 
            ON tickets.organization_id = organizations.id
        GROUP BY organizations.id
        ORDER BY total DESC
        LIMIT 8
    ");

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function countOrganizationTickets($organizationId)
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM tickets
        WHERE organization_id = ?
    ");

        $stmt->execute([$organizationId]);

        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    public function getOrganizationTicketsPaginated(
        $organizationId,
        $limit,
        $offset
    ) {
        $stmt = $this->db->prepare(
            "
        SELECT *
        FROM tickets
        WHERE organization_id = ?
        ORDER BY created_at DESC
        LIMIT " . (int)$limit . "
        OFFSET " . (int)$offset
        );

        $stmt->execute([
            $organizationId
        ]);

        return $stmt->fetchAll();
    }
}
