<?php

require_once "../app/Core/Model.php";

class LoginAttempt extends Model
{
    public function record($email, $ipAddress, $isSuccess)
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts
            (email, ip_address, is_success)
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([
            $email,
            $ipAddress,
            $isSuccess ? 1 : 0
        ]);
    }

    public function countRecentFailed($email, $ipAddress)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM login_attempts
            WHERE email = ?
            AND ip_address = ?
            AND is_success = 0
            AND created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");

        $stmt->execute([$email, $ipAddress]);

        $result = $stmt->fetch();

        return (int)$result['total'];
    }
}