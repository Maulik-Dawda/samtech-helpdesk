<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class Quotation extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    public function ensureTableExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS quotations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                quotation_no VARCHAR(50) NOT NULL UNIQUE,
                company_name VARCHAR(255) NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                quotation_date DATE NOT NULL,
                validity_date DATE DEFAULT NULL,
                status ENUM('draft', 'sent', 'accepted', 'rejected', 'expired') DEFAULT 'draft',
                grand_total DECIMAL(12,2) DEFAULT 0.00,
                proposal_data LONGTEXT NOT NULL,
                created_by INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_quotation_company (company_name),
                INDEX idx_quotation_product (product_name),
                INDEX idx_quotation_date (quotation_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->exec($sql);
        } catch (Throwable $e) {
            error_log("Failed to ensure quotations table exists: " . $e->getMessage());
        }
    }

    public function generateQuotationNo(): string
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) AS total FROM quotations WHERE DATE(created_at) = CURDATE()");
            $row = $stmt->fetch();
            $seq = (int)($row['total'] ?? 0) + 1;
            return 'QTN-' . date('Ymd') . '-' . sprintf('%03d', $seq);
        } catch (Throwable $e) {
            return 'QTN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        }
    }

    public function getAll(string $search = '', string $status = '', int $limit = 50, int $offset = 0): array
    {
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($search)) {
            $whereSql .= " AND (q.quotation_no LIKE ? OR q.company_name LIKE ? OR q.product_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $whereSql .= " AND q.status = ?";
            $params[] = $status;
        }

        $sql = "
            SELECT 
                q.*,
                u.full_name AS created_by_name,
                u.email AS created_by_email
            FROM quotations q
            LEFT JOIN users u ON u.id = q.created_by
            {$whereSql}
            ORDER BY q.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function countAll(string $search = '', string $status = ''): int
    {
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($search)) {
            $whereSql .= " AND (quotation_no LIKE ? OR company_name LIKE ? OR product_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $whereSql .= " AND status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM quotations {$whereSql}");
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.*,
                u.full_name AS created_by_name,
                u.email AS created_by_email
            FROM quotations q
            LEFT JOIN users u ON u.id = q.created_by
            WHERE q.id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByQuotationNo(string $quotationNo): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.*,
                u.full_name AS created_by_name,
                u.email AS created_by_email
            FROM quotations q
            LEFT JOIN users u ON u.id = q.created_by
            WHERE q.quotation_no = ?
            LIMIT 1
        ");

        $stmt->execute([$quotationNo]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO quotations
            (
                quotation_no,
                company_name,
                product_name,
                quotation_date,
                validity_date,
                status,
                grand_total,
                proposal_data,
                created_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $data['quotation_no'],
            $data['company_name'],
            $data['product_name'],
            $data['quotation_date'],
            !empty($data['validity_date']) ? $data['validity_date'] : null,
            $data['status'] ?? 'draft',
            (float)($data['grand_total'] ?? 0.0),
            is_string($data['proposal_data']) ? $data['proposal_data'] : json_encode($data['proposal_data']),
            !empty($data['created_by']) ? (int)$data['created_by'] : null
        ]);

        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE quotations
            SET
                quotation_no = ?,
                company_name = ?,
                product_name = ?,
                quotation_date = ?,
                validity_date = ?,
                status = ?,
                grand_total = ?,
                proposal_data = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['quotation_no'],
            $data['company_name'],
            $data['product_name'],
            $data['quotation_date'],
            !empty($data['validity_date']) ? $data['validity_date'] : null,
            $data['status'] ?? 'draft',
            (float)($data['grand_total'] ?? 0.0),
            is_string($data['proposal_data']) ? $data['proposal_data'] : json_encode($data['proposal_data']),
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM quotations WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
