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

        $userModel = new User();

        $organizationModel = new Organization();

        $tickets = $ticketModel->getReportTickets($filters);

        $this->view('reports/print-tickets', [
            'tickets' => $tickets,
            'filters' => $filters,
            'organizations' => $organizationModel->getAllActive(),
            'users' => $userModel->getUsersByRole('user'),
            'agents' => $userModel->getUsersByRole('agent')
        ]);
    }
    public function searchTickets()
    {
        $this->reportGuard();

        $keyword = trim($_GET['q'] ?? '');

        $ticketModel = new Ticket();

        $tickets = $ticketModel->searchTicketsForReport($keyword);

        require ROOT_PATH . "/app/Views/reports/partials/ticket-search-results.php";

        exit;
    }

    public function printTicketDetail($id)
    {
        $this->reportGuard();

        $ticketModel = new Ticket();

        $ticket = $ticketModel->findByIdForReport($id);

        if (!$ticket) {
            http_response_code(404);
            exit('Ticket not found');
        }

        $replyModel = new TicketReply();
        $historyModel = new TicketStatusHistory();
        $attachmentModel = new Attachment();

        $replies = $replyModel->getByTicketId($ticket['id']);

        $statusHistory = $historyModel->getByTicketId($ticket['id']);

        $attachments = $attachmentModel->getTicketAttachments($ticket['id']);

        $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);

        $this->view('reports/print-ticket-detail', [
            'ticket' => $ticket,
            'replies' => $replies,
            'statusHistory' => $statusHistory,
            'attachments' => $attachments,
            'replyAttachments' => $replyAttachments
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

    public function downloadTicketsPdf()
    {
        $this->reportGuard();

        $filters = $this->getFilters();
        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();

        $tickets = $ticketModel->getReportTickets($filters);

        ob_start();
        $this->view('reports/print-tickets', [
            'tickets' => $tickets,
            'filters' => $filters,
            'organizations' => $organizationModel->getAllActive(),
            'users' => $userModel->getUsersByRole('user'),
            'agents' => $userModel->getUsersByRole('agent'),
            'isPdfDownload' => true
        ]);
        $html = ob_get_clean();

        $randomNum = rand(100000, 900000);
        $filename = "Samtech-Helpdesk-" . $randomNum . ".pdf";

        if (class_exists('Dompdf\Dompdf')) {
            try {
                $options = new \Dompdf\Options();
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('defaultFont', 'sans-serif');
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                while (ob_get_level()) {
                    ob_end_clean();
                }

                $dompdf->stream($filename, ["Attachment" => true]);
                exit;
            } catch (\Throwable $e) {
                error_log("Dompdf error: " . $e->getMessage());
            }
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Downloading PDF...</title>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>';
        echo '</head><body><div id="pdfContent">' . $html . '</div>';
        echo '<script>
            window.onload = function() {
                var element = document.getElementById("pdfContent");
                var opt = {
                    margin: [8, 8, 8, 8],
                    filename: "' . $filename . '",
                    image: { type: "jpeg", quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, logging: false },
                    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
                };
                html2pdf().set(opt).from(element).save();
            };
        </script></body></html>';
        exit;
    }

    public function downloadTicketDetailPdf($id)
    {
        $this->reportGuard();

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findByIdForReport($id);

        if (!$ticket) {
            http_response_code(404);
            exit('Ticket not found');
        }

        $replyModel = new TicketReply();
        $historyModel = new TicketStatusHistory();
        $attachmentModel = new Attachment();

        $replies = $replyModel->getByTicketId($ticket['id']);
        $statusHistory = $historyModel->getByTicketId($ticket['id']);
        $attachments = $attachmentModel->getTicketAttachments($ticket['id']);
        $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);

        ob_start();
        $this->view('reports/print-ticket-detail', [
            'ticket' => $ticket,
            'replies' => $replies,
            'statusHistory' => $statusHistory,
            'attachments' => $attachments,
            'replyAttachments' => $replyAttachments,
            'isPdfDownload' => true
        ]);
        $html = ob_get_clean();

        $ticketIdentifier = !empty($ticket['ticket_no']) ? $ticket['ticket_no'] : $ticket['id'];
        $filename = "Samtech-Helpdesk-" . $ticketIdentifier . ".pdf";

        if (class_exists('Dompdf\Dompdf')) {
            try {
                $options = new \Dompdf\Options();
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('defaultFont', 'sans-serif');
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                while (ob_get_level()) {
                    ob_end_clean();
                }

                $dompdf->stream($filename, ["Attachment" => true]);
                exit;
            } catch (\Throwable $e) {
                error_log("Dompdf error: " . $e->getMessage());
            }
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Downloading PDF...</title>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>';
        echo '</head><body><div id="pdfContent">' . $html . '</div>';
        echo '<script>
            window.onload = function() {
                var element = document.getElementById("pdfContent");
                var opt = {
                    margin: [8, 8, 8, 8],
                    filename: "' . $filename . '",
                    image: { type: "jpeg", quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, logging: false },
                    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
                };
                html2pdf().set(opt).from(element).save();
            };
        </script></body></html>';
        exit;
    }
}
