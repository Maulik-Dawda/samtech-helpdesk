<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class MfaRecoveryOtp extends Model
{
    public function createOtp($userId, $otpCode, $expiresAt)
    {
        $stmt = $this->db->prepare("
            INSERT INTO mfa_recovery_otps
            (user_id, otp_code, expires_at)
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $otpCode,
            $expiresAt
        ]);
    }

    public function verifyOtp($userId, $otpCode)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM mfa_recovery_otps
            WHERE user_id = ?
            AND otp_code = ?
            AND is_used = 0
            AND expires_at >= NOW()
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->execute([
            $userId,
            $otpCode
        ]);

        return $stmt->fetch();
    }

    public function markUsed($otpId)
    {
        $stmt = $this->db->prepare("
            UPDATE mfa_recovery_otps
            SET is_used = 1
            WHERE id = ?
        ");

        return $stmt->execute([$otpId]);
    }
    public function findValidUnusedByUserId($userId)
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM mfa_recovery_otps
        WHERE user_id = ?
          AND is_used = 0
          AND expires_at > NOW()
        ORDER BY id DESC
        LIMIT 1
    ");

        $stmt->execute([$userId]);

        return $stmt->fetch();
    }
}
