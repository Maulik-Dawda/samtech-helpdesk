<?php

require_once ROOT_PATH . "/app/Core/Database.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";

class NotificationService
{
    public static function getHeaderNotifications()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['auth_user_id'] ?? null;
        $role = $_SESSION['auth_user_role'] ?? '';
        $organizationId = $_SESSION['auth_organization_id'] ?? null;

        if (!$userId) {
            return [
                'notifications' => [],
                'unreadCount' => 0
            ];
        }

        $db = Database::getInstance()->getConnection();
        $notifications = [];

        // 1. SLA OVERDUE ALERTS
        try {
            $ticketModel = new Ticket();
            $overdueTickets = $ticketModel->getOverdueSlaTickets(($role === 'user') ? $organizationId : null);
            foreach (array_slice($overdueTickets, 0, 3) as $ot) {
                $ticketLink = ($role === 'user') 
                    ? (BASE_URL . "/tickets/show/" . $ot['id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $ot['id']);

                $notifications[] = [
                    'type' => 'alert',
                    'title' => 'SLA Overdue: ' . ($ot['ticket_no'] ?? 'Ticket'),
                    'message' => 'Priority ' . ucfirst($ot['priority'] ?? 'medium') . ' ticket open for ' . ($ot['days_open'] ?? 0) . ' days',
                    'time' => $ot['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $ticketLink,
                    'icon' => 'bi-exclamation-triangle-fill',
                    'color' => 'danger'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification SLA Error: " . $e->getMessage());
        }

        // 2. NEW TICKETS
        try {
            $sqlNew = "
                SELECT tickets.id, tickets.ticket_no, tickets.subject, tickets.created_at, tickets.priority
                FROM tickets
                WHERE 1=1
            ";
            $paramsNew = [];
            if ($role === 'user' && $organizationId) {
                $sqlNew .= " AND tickets.organization_id = ?";
                $paramsNew[] = $organizationId;
            }
            $sqlNew .= " ORDER BY tickets.created_at DESC LIMIT 4";

            $stmtNew = $db->prepare($sqlNew);
            $stmtNew->execute($paramsNew);
            $newTickets = $stmtNew->fetchAll();

            foreach ($newTickets as $nt) {
                $ticketLink = ($role === 'user') 
                    ? (BASE_URL . "/tickets/show/" . $nt['id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $nt['id']);

                $notifications[] = [
                    'type' => 'new_ticket',
                    'title' => 'New Ticket: ' . ($nt['ticket_no'] ?? 'Ticket'),
                    'message' => mb_strimwidth($nt['subject'] ?? 'Untitled Ticket', 0, 35, '...'),
                    'time' => $nt['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $ticketLink,
                    'icon' => 'bi-ticket-perforated-fill',
                    'color' => 'primary'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification New Ticket Error: " . $e->getMessage());
        }

        // 3. REPLIES
        try {
            $sqlReplies = "
                SELECT ticket_replies.id, ticket_replies.ticket_id, ticket_replies.message, ticket_replies.created_at,
                       users.full_name AS sender_name, tickets.ticket_no
                FROM ticket_replies
                JOIN tickets ON tickets.id = ticket_replies.ticket_id
                JOIN users ON users.id = ticket_replies.user_id
                WHERE ticket_replies.user_id != ?
            ";
            $paramsReplies = [$userId];

            if ($role === 'user' && $organizationId) {
                $sqlReplies .= " AND tickets.organization_id = ?";
                $paramsReplies[] = $organizationId;
            }

            $sqlReplies .= " ORDER BY ticket_replies.created_at DESC LIMIT 4";

            $stmtReplies = $db->prepare($sqlReplies);
            $stmtReplies->execute($paramsReplies);
            $replies = $stmtReplies->fetchAll();

            foreach ($replies as $r) {
                $ticketLink = ($role === 'user') 
                    ? (BASE_URL . "/tickets/show/" . $r['ticket_id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $r['ticket_id']);

                $notifications[] = [
                    'type' => 'reply',
                    'title' => 'Reply on ' . ($r['ticket_no'] ?? 'Ticket'),
                    'message' => ($r['sender_name'] ?? 'User') . ': ' . mb_strimwidth($r['message'] ?? '', 0, 35, '...'),
                    'time' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $ticketLink,
                    'icon' => 'bi-chat-left-text-fill',
                    'color' => 'info'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification Reply Error: " . $e->getMessage());
        }

        // 4. STATUS UPDATES
        try {
            $sqlStatus = "
                SELECT ticket_status_history.id, ticket_status_history.ticket_id, ticket_status_history.old_status,
                       ticket_status_history.new_status, ticket_status_history.created_at, tickets.ticket_no
                FROM ticket_status_history
                JOIN tickets ON tickets.id = ticket_status_history.ticket_id
                ORDER BY ticket_status_history.created_at DESC LIMIT 4
            ";
            $stmtStatus = $db->prepare($sqlStatus);
            $stmtStatus->execute();
            $statusHistories = $stmtStatus->fetchAll();

            foreach ($statusHistories as $sh) {
                $ticketLink = ($role === 'user') 
                    ? (BASE_URL . "/tickets/show/" . $sh['ticket_id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $sh['ticket_id']);

                $notifications[] = [
                    'type' => 'status_update',
                    'title' => 'Status Updated: ' . ($sh['ticket_no'] ?? 'Ticket'),
                    'message' => 'Changed to ' . ucfirst(str_replace('_', ' ', $sh['new_status'] ?? '')),
                    'time' => $sh['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $ticketLink,
                    'icon' => 'bi-arrow-repeat',
                    'color' => 'warning'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification Status Update Error: " . $e->getMessage());
        }

        // Sort all notifications by date/time descending
        usort($notifications, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        $finalNotifications = array_slice($notifications, 0, 8);

        return [
            'notifications' => $finalNotifications,
            'unreadCount' => count($finalNotifications)
        ];
    }
}
