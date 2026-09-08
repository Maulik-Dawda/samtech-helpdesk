<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class Contract extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    public function ensureTableExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS contracts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                organization_id INT NOT NULL,
                contract_name VARCHAR(255) NOT NULL,
                contract_type ENUM('annual', 'half_yearly', 'quarterly', 'monthly', 'custom') DEFAULT 'annual',
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                created_by INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_contracts_org (organization_id),
                INDEX idx_contracts_dates (start_date, end_date),
                CONSTRAINT fk_contracts_organization FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Failed to ensure contracts table exists: " . $e->getMessage());
        }
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO contracts
            (organization_id, contract_name, contract_type, start_date, end_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $data['organization_id'],
            $data['contract_name'],
            $data['contract_type'],
            $data['start_date'],
            $data['end_date'],
            $data['created_by'] ?? null
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, o.name AS organization_name, o.email AS organization_email, o.phone AS organization_phone
            FROM contracts c
            JOIN organizations o ON o.id = c.organization_id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByOrganizationId($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.full_name AS created_by_name
            FROM contracts c
            LEFT JOIN users u ON u.id = c.created_by
            WHERE c.organization_id = ?
            ORDER BY c.start_date DESC, c.id DESC
        ");
        $stmt->execute([$organizationId]);
        return $stmt->fetchAll();
    }

    public function getLatestContractForOrganization($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*
            FROM contracts c
            WHERE c.organization_id = ?
            ORDER BY c.start_date DESC, c.id DESC
            LIMIT 1
        ");
        $stmt->execute([$organizationId]);
        return $stmt->fetch();
    }

    public function getAllWithOrganizations()
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id AS organization_id,
                o.name AS organization_name,
                o.email AS organization_email,
                o.phone AS organization_phone,
                COUNT(c.id) AS total_contracts,
                MAX(c.end_date) AS latest_end_date
            FROM contracts c
            JOIN organizations o ON o.id = c.organization_id
            GROUP BY o.id, o.name, o.email, o.phone
            ORDER BY o.name ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach ($results as &$row) {
            $latest = $this->getLatestContractForOrganization($row['organization_id']);
            $row['latest_contract'] = $latest;
            $row['status_info'] = $latest ? $this->calculateContractStatus($latest) : null;
        }

        return $results;
    }

    public function getTicketsForContractTimeframe($organizationId, $startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT 
                t.*,
                u.full_name AS customer_name,
                u.email AS customer_email,
                agent.full_name AS assigned_agent_name,
                o.name AS organization_name
            FROM tickets t
            LEFT JOIN users u ON u.id = t.user_id
            LEFT JOIN users agent ON agent.id = t.assigned_agent_id
            LEFT JOIN organizations o ON o.id = t.organization_id
            WHERE t.organization_id = ?
            AND DATE(t.created_at) >= ?
            AND DATE(t.created_at) <= ?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$organizationId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Calculate contract expiration status and return color card info.
     *
     * Rules:
     * - Annual (12 months): 0-10 months -> Green, 10-12 months -> Orange, Expired -> Red
     * - Half Yearly (6 months): 0-5 months -> Green, 5-6 months -> Orange, Expired -> Red
     * - Quarterly (3 months): 0-2.5 months -> Green, 2.5-3 months -> Orange, Expired -> Red
     * - Monthly (1 month): 0-20 days -> Green, 20-30 days -> Orange, Expired -> Red
     * - Custom: Remaining > 20% total days -> Green, <= 20% total days -> Orange, Expired -> Red
     */
    public function calculateContractStatus($contract)
    {
        if (!$contract) {
            return [
                'color' => 'gray',
                'status_label' => 'No Contract',
                'badge_class' => 'bg-secondary',
                'card_class' => 'border-secondary bg-light text-dark',
                'days_left' => 0,
                'is_expired' => true
            ];
        }

        $today = new DateTime(date('Y-m-d'));
        $start = new DateTime($contract['start_date']);
        $end = new DateTime($contract['end_date']);
        $type = strtolower($contract['contract_type'] ?? 'annual');

        if ($today > $end) {
            return [
                'color' => 'red',
                'status_label' => 'Expired',
                'badge_class' => 'bg-danger text-white',
                'card_class' => 'border-danger bg-danger-subtle text-danger-emphasis',
                'days_left' => 0,
                'is_expired' => true
            ];
        }

        $totalDays = max(1, $start->diff($end)->days);
        $daysPassed = max(0, $start->diff($today)->days);
        $daysLeft = max(0, $today->diff($end)->days);

        $color = 'green';

        if ($type === 'annual') {
            // Annual (approx 365 days): First ~304 days -> Green, last ~61 days -> Orange
            if ($daysLeft <= 60) {
                $color = 'orange';
            }
        } elseif ($type === 'half_yearly') {
            // Half yearly (approx 182 days): First ~152 days -> Green, last ~30 days -> Orange
            if ($daysLeft <= 30) {
                $color = 'orange';
            }
        } elseif ($type === 'quarterly') {
            // Quarterly (approx 90 days): First ~75 days -> Green, last ~15 days -> Orange
            if ($daysLeft <= 15) {
                $color = 'orange';
            }
        } elseif ($type === 'monthly') {
            // Monthly (approx 30 days): First 20 days -> Green, last 10 days -> Orange
            if ($daysLeft <= 10) {
                $color = 'orange';
            }
        } else {
            // Custom: If remaining days <= 20% of total duration or <= 15 days
            if (($daysLeft / $totalDays) <= 0.20 || $daysLeft <= 15) {
                $color = 'orange';
            }
        }

        if ($color === 'orange') {
            return [
                'color' => 'orange',
                'status_label' => 'Expiring Soon',
                'badge_class' => 'bg-warning text-dark',
                'card_class' => 'border-warning bg-warning-subtle text-warning-emphasis',
                'days_left' => $daysLeft,
                'is_expired' => false
            ];
        }

        return [
            'color' => 'green',
            'status_label' => 'Active',
            'badge_class' => 'bg-success text-white',
            'card_class' => 'border-success bg-success-subtle text-success-emphasis',
            'days_left' => $daysLeft,
            'is_expired' => false
        ];
    }
}
