<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class VerificationAttempt extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS verification_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                type VARCHAR(50) NOT NULL,
                is_success TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_verification_email_ip (email, ip_address),
                INDEX idx_verification_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Failed to ensure verification_attempts table exists: " . $e->getMessage());
        }
    }

    public function record($email, $ipAddress, $type, $isSuccess)
    {
        $stmt = $this->db->prepare("
            INSERT INTO verification_attempts
            (email, ip_address, type, is_success)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $email,
            $ipAddress,
            $type,
            $isSuccess ? 1 : 0
        ]);
    }

    public function countRecentFailed($email, $ipAddress, $minutes = 15)
    {
        $minutes = (int)$minutes;
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM verification_attempts
            WHERE (email = ? OR ip_address = ?)
            AND is_success = 0
            AND created_at >= DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)
        ");

        $stmt->execute([$email, $ipAddress]);
        $result = $stmt->fetch();

        return (int)($result['total'] ?? 0);
    }

    public function clearAttempts($email)
    {
        $stmt = $this->db->prepare("
            DELETE FROM verification_attempts
            WHERE email = ?
        ");

        return $stmt->execute([$email]);
    }
}
