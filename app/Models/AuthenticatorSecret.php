<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class AuthenticatorSecret extends Model
{
    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM authenticator_secrets
            WHERE user_id = ?
            LIMIT 1
        ");

        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function createSecret($userId, $secretKey)
    {
        $stmt = $this->db->prepare("
            INSERT INTO authenticator_secrets
            (user_id, secret_key, is_enabled)
            VALUES (?, ?, 0)
        ");

        return $stmt->execute([$userId, $secretKey]);
    }

    public function enableSecret($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE authenticator_secrets
            SET is_enabled = 1
            WHERE user_id = ?
        ");

        return $stmt->execute([$userId]);
    }

    public function resetMfa($userId, $newSecret)
    {
        $stmt = $this->db->prepare("
            UPDATE authenticator_secrets
            SET secret_key = ?, is_enabled = 0
            WHERE user_id = ?
        ");

        return $stmt->execute([$newSecret, $userId]);
    }
}