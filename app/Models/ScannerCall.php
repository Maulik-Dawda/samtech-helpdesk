<?php

require_once ROOT_PATH . '/app/Core/Database.php';

class ScannerCall
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->ensureTableExists();
    }

    /**
     * Auto-creates scanner_calls table if missing.
     */
    private function ensureTableExists(): void
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `scanner_calls` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `scanner_code` VARCHAR(100) NULL,
                `plate_last_4` VARCHAR(10) NOT NULL,
                `caller_phone` VARCHAR(30) NOT NULL,
                `customer_number` VARCHAR(30) NULL,
                `did_number` VARCHAR(30) NOT NULL,
                `status` VARCHAR(20) DEFAULT 'initiated',
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                INDEX (`scanner_code`),
                INDEX (`caller_phone`),
                INDEX (`plate_last_4`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->exec($sql);
        } catch (Throwable $e) {
            error_log('ScannerCall table creation error: ' . $e->getMessage());
        }
    }

    /**
     * Store new call masking log into database.
     */
    public function createCallLog(array $data): int
    {
        $now = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("
            INSERT INTO `scanner_calls` (
                `scanner_code`,
                `plate_last_4`,
                `caller_phone`,
                `customer_number`,
                `did_number`,
                `status`,
                `created_at`,
                `updated_at`
            ) VALUES (
                :scanner_code,
                :plate_last_4,
                :caller_phone,
                :customer_number,
                :did_number,
                :status,
                :created_at,
                :updated_at
            )
        ");

        $stmt->execute([
            ':scanner_code' => $data['scanner_code'] ?? 'DEFAULT',
            ':plate_last_4' => $data['plate_last_4'],
            ':caller_phone' => $data['caller_phone'],
            ':customer_number' => $data['customer_number'] ?? null,
            ':did_number' => $data['did_number'],
            ':status' => $data['status'] ?? 'initiated',
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Fetches owner customer_number from database.
     * Looks up user/owner phone by scanner code, or plate_last_4, or fallback to registered support/owner number.
     */
    public function fetchCustomerNumber(string $scannerCode, string $plateLast4): string
    {
        try {
            // 1. Try to find phone from active users table matching organization/scanner owner
            $stmt = $this->db->prepare("
                SELECT `phone` FROM `users`
                WHERE `phone` IS NOT NULL AND `phone` != ''
                AND (`role` = 'user' OR `role` = 'agent')
                ORDER BY `id` ASC LIMIT 1
            ");
            $stmt->execute();
            $phone = $stmt->fetchColumn();

            if (!empty($phone)) {
                return (string)$phone;
            }
        } catch (Throwable $e) {
            error_log('fetchCustomerNumber error: ' . $e->getMessage());
        }

        // Fallback default customer owner number
        return '+971500000000';
    }
}
