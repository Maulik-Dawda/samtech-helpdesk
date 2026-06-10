<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class Attachment extends Model
{
    public function createTicketAttachment($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO ticket_attachments
            (
                ticket_id,
                uploaded_by,
                original_name,
                stored_name,
                file_path,
                file_type,
                file_size
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['ticket_id'],
            $data['uploaded_by'],
            $data['original_name'],
            $data['stored_name'],
            $data['file_path'],
            $data['file_type'],
            $data['file_size']
        ]);
    }

    public function createReplyAttachment($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO reply_attachments
            (
                reply_id,
                ticket_id,
                uploaded_by,
                original_name,
                stored_name,
                file_path,
                file_type,
                file_size
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['reply_id'],
            $data['ticket_id'],
            $data['uploaded_by'],
            $data['original_name'],
            $data['stored_name'],
            $data['file_path'],
            $data['file_type'],
            $data['file_size']
        ]);
    }

    public function getTicketAttachments($ticketId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM ticket_attachments
            WHERE ticket_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$ticketId]);

        return $stmt->fetchAll();
    }

    public function getReplyAttachments($replyId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM reply_attachments
            WHERE reply_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$replyId]);

        return $stmt->fetchAll();
    }
    public function findTicketAttachment($id)
    {
        $stmt = $this->db->prepare("
        SELECT 
            ticket_attachments.*,
            tickets.organization_id
        FROM ticket_attachments
        JOIN tickets ON tickets.id = ticket_attachments.ticket_id
        WHERE ticket_attachments.id = ?
        LIMIT 1
    ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function findReplyAttachment($id)
    {
        $stmt = $this->db->prepare("
        SELECT 
            reply_attachments.*,
            tickets.organization_id
        FROM reply_attachments
        JOIN tickets ON tickets.id = reply_attachments.ticket_id
        WHERE reply_attachments.id = ?
        LIMIT 1
    ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function getReplyAttachmentsByTicketId($ticketId)
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM reply_attachments
        WHERE ticket_id = ?
        ORDER BY created_at ASC
    ");

        $stmt->execute([$ticketId]);

        $attachments = $stmt->fetchAll();

        $grouped = [];

        foreach ($attachments as $attachment) {
            $grouped[$attachment['reply_id']][] = $attachment;
        }

        return $grouped;
    }
}
