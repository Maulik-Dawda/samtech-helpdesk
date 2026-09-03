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

    public function getByTicketId($ticketId)
    {
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
}