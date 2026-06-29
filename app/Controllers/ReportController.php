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

    private function getFilters()
    {
        return [
            'organization_id' => trim($_GET['organization_id'] ?? ''),
            'user_id' => trim($_GET['user_id'] ?? ''),
            'agent_id' => trim($_GET['agent_id'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'priority' => trim($_GET['priority'] ?? ''),
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? '')
        ];
    }

    private function getAppliedFilters($filters)
    {
        $applied = [];

        if (!empty($filters['organization_id'])) {
            $organizationModel = new Organization();
            $organization = $organizationModel->findById($filters['organization_id']);
            $applied['Organization'] = $organization['name'] ?? $filters['organization_id'];
        }

        if (!empty($filters['user_id'])) {
            $userModel = new User();
            $user = $userModel->findById($filters['user_id']);
            $applied['User'] = $user['full_name'] ?? $filters['user_id'];
        }

        if (!empty($filters['agent_id'])) {
            $userModel = new User();
            $agent = $userModel->findById($filters['agent_id']);
            $applied['Agent'] = $agent['full_name'] ?? $filters['agent_id'];
        }

        if (!empty($filters['status'])) {
            $applied['Status'] = ucwords(str_replace('_', ' ', $filters['status']));
        }

        if (!empty($filters['priority'])) {
            $applied['Priority'] = ucfirst($filters['priority']);
        }

        if (!empty($filters['date_from'])) {
            $applied['From Date'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $applied['To Date'] = $filters['date_to'];
        }

        return $applied;
    }

    public function tickets()
    {
        $this->reportGuard();

        $filters = $this->getFilters();

        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();

        $tickets = $ticketModel->getReportTickets($filters);

        $this->view('reports/tickets', [
            'tickets' => $tickets,
            'filters' => $filters,
            'organizations' => $organizationModel->getAllActive(),
            'users' => $userModel->getUsersByRole('user'),
            'agents' => $userModel->getUsersByRole('agent')
        ]);
    }

    public function filterTickets()
    {
        $this->reportGuard();

        $filters = $this->getFilters();

        $ticketModel = new Ticket();
        $tickets = $ticketModel->getReportTickets($filters);

        require ROOT_PATH . "/app/Views/reports/partials/ticket-table.php";
        exit;
    }

    public function printTickets()
    {
        $this->reportGuard();

        $filters = $this->getFilters();

        $ticketModel = new Ticket();
        $tickets = $ticketModel->getReportTickets($filters);
        $appliedFilters = $this->getAppliedFilters($filters);

        $this->view('reports/print-tickets', [
            'tickets' => $tickets,
            'filters' => $filters,
            'appliedFilters' => $appliedFilters
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