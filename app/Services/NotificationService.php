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
        $isAdminAgent = (int)($_SESSION['is_admin_agent'] ?? 0) === 1;
        $isAdmin = ($role === 'admin' || $isAdminAgent);
        $isAgent = ($role === 'agent' && !$isAdminAgent);
        $isCustomer = ($role === 'user');

        if (!$userId) {
            return [
                'notifications' => [],
                'unreadCount' => 0
            ];
        }

        $db = Database::connect();
        $notifications = [];

        // 1. TICKET ASSIGNMENT NOTIFICATIONS (For Support Agents)
        if ($isAgent) {
            try {
                $sqlAssigned = "
                    SELECT tickets.id, tickets.ticket_no, tickets.subject, tickets.updated_at, organizations.name AS org_name
                    FROM tickets
                    LEFT JOIN organizations ON organizations.id = tickets.organization_id
                    WHERE tickets.assigned_agent_id = ? AND tickets.status NOT IN ('resolved', 'closed')
                    ORDER BY tickets.updated_at DESC LIMIT 3
                ";
                $stmt = $db->prepare($sqlAssigned);
                $stmt->execute([$userId]);
                $assigned = $stmt->fetchAll();

                foreach ($assigned as $t) {
                    $notifications[] = [
                        'type' => 'assignment',
                        'title' => 'Assigned: ' . ($t['ticket_no'] ?? 'Ticket'),
                        'message' => 'Assigned to ' . mb_strimwidth($t['subject'] ?? 'Ticket', 0, 30, '...'),
                        'time' => $t['updated_at'] ?? date('Y-m-d H:i:s'),
                        'link' => BASE_URL . "/agent/tickets/show/" . $t['id'],
                        'icon' => 'bi-person-check-fill',
                        'color' => 'primary'
                    ];
                }
            } catch (Throwable $e) {
                error_log("Notification Assignment Error: " . $e->getMessage());
            }
        }

        // 2. SLA OVERDUE ALERTS (Bifurcated by role)
        try {
            $ticketModel = new Ticket();
            $targetOrgId = $isCustomer ? $organizationId : null;
            $overdueTickets = $ticketModel->getOverdueSlaTickets($targetOrgId);

            if ($isAgent) {
                $overdueTickets = array_filter($overdueTickets, function($ot) use ($userId) {
                    return empty($ot['assigned_agent_id']) || (int)$ot['assigned_agent_id'] === (int)$userId;
                });
            }

            foreach (array_slice($overdueTickets, 0, 3) as $ot) {
                $link = $isCustomer 
                    ? (BASE_URL . "/tickets/show/" . $ot['id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $ot['id']);

                $notifications[] = [
                    'type' => 'alert',
                    'title' => 'SLA Overdue: ' . ($ot['ticket_no'] ?? 'Ticket'),
                    'message' => 'Priority ' . ucfirst($ot['priority'] ?? 'medium') . ' open for ' . ($ot['days_open'] ?? 0) . ' days',
                    'time' => $ot['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $link,
                    'icon' => 'bi-exclamation-triangle-fill',
                    'color' => 'danger'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification SLA Error: " . $e->getMessage());
        }

        // 3. NEW TICKETS (Bifurcated by role)
        try {
            $sqlNew = "
                SELECT tickets.id, tickets.ticket_no, tickets.subject, tickets.created_at, tickets.user_id, organizations.name AS org_name
                FROM tickets
                LEFT JOIN organizations ON organizations.id = tickets.organization_id
                WHERE 1=1
            ";
            $paramsNew = [];

            if ($isCustomer) {
                if ($organizationId) {
                    $sqlNew .= " AND tickets.organization_id = ?";
                    $paramsNew[] = $organizationId;
                } else {
                    $sqlNew .= " AND tickets.user_id = ?";
                    $paramsNew[] = $userId;
                }
            }

            $sqlNew .= " ORDER BY tickets.created_at DESC LIMIT 3";
            $stmtNew = $db->prepare($sqlNew);
            $stmtNew->execute($paramsNew);
            $newTickets = $stmtNew->fetchAll();

            foreach ($newTickets as $nt) {
                $link = $isCustomer 
                    ? (BASE_URL . "/tickets/show/" . $nt['id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $nt['id']);

                $title = $isCustomer ? ('Ticket Created: ' . $nt['ticket_no']) : ('New Ticket: ' . $nt['ticket_no']);
                $msg = !empty($nt['org_name']) ? ($nt['org_name'] . ' - ' . mb_strimwidth($nt['subject'], 0, 25, '...')) : mb_strimwidth($nt['subject'], 0, 35, '...');

                $notifications[] = [
                    'type' => 'new_ticket',
                    'title' => $title,
                    'message' => $msg,
                    'time' => $nt['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $link,
                    'icon' => 'bi-ticket-perforated-fill',
                    'color' => 'info'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification New Ticket Error: " . $e->getMessage());
        }

        // 4. REPLIES (Excluding own replies, bifurcated by role)
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

            if ($isCustomer) {
                if ($organizationId) {
                    $sqlReplies .= " AND tickets.organization_id = ?";
                    $paramsReplies[] = $organizationId;
                } else {
                    $sqlReplies .= " AND tickets.user_id = ?";
                    $paramsReplies[] = $userId;
                }
            } elseif ($isAgent) {
                $sqlReplies .= " AND (tickets.assigned_agent_id = ? OR tickets.assigned_agent_id IS NULL)";
                $paramsReplies[] = $userId;
            }

            $sqlReplies .= " ORDER BY ticket_replies.created_at DESC LIMIT 3";
            $stmtReplies = $db->prepare($sqlReplies);
            $stmtReplies->execute($paramsReplies);
            $replies = $stmtReplies->fetchAll();

            foreach ($replies as $r) {
                $link = $isCustomer 
                    ? (BASE_URL . "/tickets/show/" . $r['ticket_id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $r['ticket_id']);

                $notifications[] = [
                    'type' => 'reply',
                    'title' => 'New Reply on ' . ($r['ticket_no'] ?? 'Ticket'),
                    'message' => ($r['sender_name'] ?? 'User') . ': ' . mb_strimwidth($r['message'] ?? '', 0, 30, '...'),
                    'time' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $link,
                    'icon' => 'bi-chat-left-text-fill',
                    'color' => 'success'
                ];
            }
        } catch (Throwable $e) {
            error_log("Notification Reply Error: " . $e->getMessage());
        }

        // 5. STATUS UPDATES (Bifurcated by role)
        try {
            $sqlStatus = "
                SELECT ticket_status_history.id, ticket_status_history.ticket_id, ticket_status_history.old_status,
                       ticket_status_history.new_status, ticket_status_history.created_at, tickets.ticket_no
                FROM ticket_status_history
                JOIN tickets ON tickets.id = ticket_status_history.ticket_id
                WHERE 1=1
            ";
            $paramsStatus = [];

            if ($isCustomer) {
                if ($organizationId) {
                    $sqlStatus .= " AND tickets.organization_id = ?";
                    $paramsStatus[] = $organizationId;
                } else {
                    $sqlStatus .= " AND tickets.user_id = ?";
                    $paramsStatus[] = $userId;
                }
            } elseif ($isAgent) {
                $sqlStatus .= " AND (tickets.assigned_agent_id = ? OR tickets.assigned_agent_id IS NULL)";
                $paramsStatus[] = $userId;
            }

            $sqlStatus .= " ORDER BY ticket_status_history.created_at DESC LIMIT 3";
            $stmtStatus = $db->prepare($sqlStatus);
            $stmtStatus->execute($paramsStatus);
            $statusHistories = $stmtStatus->fetchAll();

            foreach ($statusHistories as $sh) {
                $link = $isCustomer 
                    ? (BASE_URL . "/tickets/show/" . $sh['ticket_id']) 
                    : (BASE_URL . "/agent/tickets/show/" . $sh['ticket_id']);

                $notifications[] = [
                    'type' => 'status_update',
                    'title' => 'Status Change: ' . ($sh['ticket_no'] ?? 'Ticket'),
                    'message' => 'Updated to ' . ucfirst(str_replace('_', ' ', $sh['new_status'] ?? '')),
                    'time' => $sh['created_at'] ?? date('Y-m-d H:i:s'),
                    'link' => $link,
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
