<?php

require_once ROOT_PATH . "/app/Core/Controller.php";

require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Organization.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";
require_once ROOT_PATH . "/app/Models/TicketStatusHistory.php";
require_once ROOT_PATH . "/app/Models/Attachment.php";

class ReportController extends Controller
{
    private function reportGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['auth_user_role'] ?? '';

        if (!in_array($role, ['admin', 'agent'])) {
            http_response_code(403);
            require_once ROOT_PATH . "/app/Views/errors/403.php";
            exit;
        }
    }

    public function tickets()
    {
        $this->reportGuard();

        $filters = [
            'organization_id' => $_GET['organization_id'] ?? '',
            'user_id' => $_GET['user_id'] ?? '',
            'agent_id' => $_GET['agent_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();

        $tickets = $ticketModel->getReportTickets($filters);
        $organizations = $organizationModel->getAllActive();
        $users = $userModel->getUsersByRole('user');
        $agents = $userModel->getUsersByRole('agent');

        $this->view('reports/tickets', [
            'tickets' => $tickets,
            'filters' => $filters,
            'organizations' => $organizations,
            'users' => $users,
            'agents' => $agents
        ]);
    }

    public function printTickets()
    {
        $this->reportGuard();

        $filters = [
            'organization_id' => $_GET['organization_id'] ?? '',
            'user_id' => $_GET['user_id'] ?? '',
            'agent_id' => $_GET['agent_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $ticketModel = new Ticket();

        $tickets = $ticketModel->getReportTickets($filters);

        $this->view('reports/print-tickets', [
            'tickets' => $tickets,
            'filters' => $filters
        ]);
    }

    public function filterTickets()
    {
        $this->reportGuard();

        $filters = [
            'organization_id' => $_GET['organization_id'] ?? '',
            'user_id' => $_GET['user_id'] ?? '',
            'agent_id' => $_GET['agent_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $ticketModel = new Ticket();

        $tickets = $ticketModel->getReportTickets($filters);

        $this->view('reports/partials/ticket-table', [
            'tickets' => $tickets
        ]);
    }

    public function ticketDetail()
    {
        $this->reportGuard();

        $ticketId = $_GET['ticket_id'] ?? '';

        $ticket = null;
        $replies = [];
        $statusHistory = [];
        $attachments = [];
        $replyAttachments = [];

        $ticketModel = new Ticket();

        if (!empty($ticketId)) {
            $ticket = $ticketModel->findByIdForReport($ticketId);

            if ($ticket) {
                $replyModel = new TicketReply();
                $historyModel = new TicketStatusHistory();
                $attachmentModel = new Attachment();

                $replies = $replyModel->getByTicketId($ticket['id']);
                $statusHistory = $historyModel->getByTicketId($ticket['id']);
                $attachments = $attachmentModel->getTicketAttachments($ticket['id']);
                $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);
            }
        }

        $tickets = $ticketModel->getReportTickets();

        $this->view('reports/ticket-detail', [
            'tickets' => $tickets,
            'ticketId' => $ticketId,
            'ticket' => $ticket,
            'replies' => $replies,
            'statusHistory' => $statusHistory,
            'attachments' => $attachments,
            'replyAttachments' => $replyAttachments
        ]);
    }
}