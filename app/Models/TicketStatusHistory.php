<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class TicketStatusHistory extends Model
{
    public function create($ticketId, $oldStatus, $newStatus, $changedBy)
    {
        $stmt = $this->db->prepare("
            INSERT INTO ticket_status_history
            (
                ticket_id,
                old_status,
                new_status,
                changed_by
            )
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $ticketId,
            $oldStatus,
            $newStatus,
            $changedBy
        ]);
    }

    public function getByTicketId($ticketId)
    {
        $stmt = $this->db->prepare("
            SELECT
                ticket_status_history.*,
                users.full_name
            FROM ticket_status_history
            JOIN users
                ON users.id = ticket_status_history.changed_by
            WHERE ticket_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$ticketId]);

        return $stmt->fetchAll();
    }
}