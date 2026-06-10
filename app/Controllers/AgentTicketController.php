<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";
require_once ROOT_PATH . "/app/Models/TicketStatusHistory.php";
require_once ROOT_PATH . "/app/Models/Attachment.php";
require_once ROOT_PATH . "/app/Services/UploadService.php";

class AgentTicketController extends Controller
{
    public function index()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('agent');

        $ticketModel = new Ticket();

        $tickets = $ticketModel->getAllTicketsForAgent();

        $this->view('agent/tickets/index', [
            'tickets' => $tickets
        ]);
    }

    public function show($id)
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('agent');

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findForAgent($id);

        if (!$ticket) {
            http_response_code(404);
            echo "Ticket not found.";
            exit;
        }

        $replyModel = new TicketReply();
        $replies = $replyModel->getByTicketId($ticket['id']);

        $historyModel = new TicketStatusHistory();
        $statusHistory = $historyModel->getByTicketId($ticket['id']);

        $attachmentModel = new Attachment();
        $attachments = $attachmentModel->getTicketAttachments($ticket['id']);

        $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);

        $this->view('agent/tickets/show', [
            'ticket' => $ticket,
            'replies' => $replies,
            'statusHistory' => $statusHistory,
            'attachments' => $attachments,
            'replyAttachments' => $replyAttachments
        ]);
    }
    public function reply($id)
    {
        Csrf::verify();

        AuthMiddleware::timeout();
        AuthMiddleware::check('agent');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            $_SESSION['error'] = "Reply message is required.";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        $ticketModel = new Ticket();

        $ticket = $ticketModel->findForAgent($id);

        if (!$ticket) {
            http_response_code(404);
            echo "Ticket not found.";
            exit;
        }

        $replyModel = new TicketReply();

        $replyId = $replyModel->create([
            'ticket_id' => $ticket['id'],
            'user_id' => $_SESSION['auth_user_id'],
            'message' => $message,
            'attachment_path' => null
        ]);

        if (!$replyId) {
            $_SESSION['error'] = "Unable to send reply.";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        try {
            if (!empty($_FILES['attachments']['name'][0])) {

                $uploadedFiles = UploadService::uploadMultiple(
                    $_FILES['attachments'],
                    'replies'
                );

                $attachmentModel = new Attachment();

                foreach ($uploadedFiles as $file) {
                    $attachmentModel->createReplyAttachment([
                        'reply_id' => $replyId,
                        'ticket_id' => $ticket['id'],
                        'uploaded_by' => $_SESSION['auth_user_id'],
                        'original_name' => $file['original_name'],
                        'stored_name' => $file['stored_name'],
                        'file_path' => $file['file_path'],
                        'file_type' => $file['file_type'],
                        'file_size' => $file['file_size']
                    ]);
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Reply sent, but attachment failed: " . $e->getMessage();
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        $_SESSION['success'] = "Reply sent successfully.";

        header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
        exit;
    }

    public function updateStatus($id)
    {
        Csrf::verify();

        AuthMiddleware::timeout();
        AuthMiddleware::check('agent');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $status = $_POST['status'] ?? '';
        $resolutionMessage = trim($_POST['resolution_message'] ?? '');

        $allowedStatuses = [
            'open',
            'in_progress',
            'pending',
            'resolved',
            'closed'
        ];

        if (!in_array($status, $allowedStatuses)) {
            $_SESSION['error'] = "Invalid status selected.";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findForAgent($id);

        if (!$ticket) {
            http_response_code(404);
            echo "Ticket not found.";
            exit;
        }

        if ($ticket['status'] === 'closed') {
            $_SESSION['error'] = "This ticket is already closed.";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        if ($ticket['status'] === $status) {
            $_SESSION['error'] = "Ticket is already marked as " . ucwords(str_replace('_', ' ', $status)) . ".";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        if ($status === 'closed' && empty($resolutionMessage)) {
            $_SESSION['error'] = "Resolution message is required before closing the ticket.";
            header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
            exit;
        }

        if ($status === 'closed') {
            $replyModel = new TicketReply();

            $replyModel->create([
                'ticket_id' => $ticket['id'],
                'user_id' => $_SESSION['auth_user_id'],
                'message' => "[Resolution] " . $resolutionMessage,
                'attachment_path' => null
            ]);
        }

        $historyModel = new TicketStatusHistory();

        $historyModel->create(
            $ticket['id'],
            $ticket['status'],
            $status,
            $_SESSION['auth_user_id']
        );

        $ticketModel->updateStatusByAgent(
            $ticket['id'],
            $status,
            $_SESSION['auth_user_id']
        );

        $_SESSION['success'] = "Ticket status updated successfully.";

        header("Location: " . BASE_URL . "/agent/tickets/show/" . $id);
        exit;
    }
}
