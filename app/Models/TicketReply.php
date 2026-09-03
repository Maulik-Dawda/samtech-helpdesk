<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class TicketReply extends Model
{
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ticket_replies
                (ticket_id, user_id, message, attachment_path)
                VALUES (?, ?, ?, ?)
            ");

            $created = $stmt->execute([
                $data['ticket_id'],
                $data['user_id'],
                $data['message'],
                $data['attachment_path'] ?? null
            ]);

            if (!$created) {
                error_log("Ticket Reply Insert Failed: " . print_r($stmt->errorInfo(), true));
                return false;
            }

            return $this->db->lastInsertId();

        } catch (Throwable $e) {
            error_log("Ticket Reply Error: " . $e->getMessage());
            return false;
        }
    }

    public function ensureEditColumnsExist()
    {
        try {
            $this->db->exec("ALTER TABLE `ticket_replies` ADD COLUMN `edit_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0");
        } catch (Throwable $e) {
            // Ignored if column exists
        }

        try {
            $this->db->exec("ALTER TABLE `ticket_replies` ADD COLUMN `edited_at` DATETIME NULL DEFAULT NULL");
        } catch (Throwable $e) {
            // Ignored if column exists
        }
    }

    public function getByTicketId($ticketId)
    {
        $this->ensureEditColumnsExist();
        $stmt = $this->db->prepare("
            SELECT ticket_replies.*, users.full_name, users.role
            FROM ticket_replies
            JOIN users ON users.id = ticket_replies.user_id
            WHERE ticket_replies.ticket_id = ?
            AND (users.email IS NULL OR users.email != 'maulik@septixtechnologies.com')
            ORDER BY ticket_replies.created_at ASC
        ");

        $stmt->execute([$ticketId]);

        return $stmt->fetchAll();
    }

    public function findById($replyId)
    {
        $this->ensureEditColumnsExist();
        $stmt = $this->db->prepare("
            SELECT ticket_replies.*, users.full_name, users.role
            FROM ticket_replies
            JOIN users ON users.id = ticket_replies.user_id
            WHERE ticket_replies.id = ?
            LIMIT 1
        ");

        $stmt->execute([$replyId]);
        return $stmt->fetch();
    }

    public function updateReplyMessage($replyId, $newMessage)
    {
        $this->ensureEditColumnsExist();
        $stmt = $this->db->prepare("
            UPDATE ticket_replies
            SET message = ?,
                edit_count = edit_count + 1,
                edited_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$newMessage, $replyId]);
    }
}