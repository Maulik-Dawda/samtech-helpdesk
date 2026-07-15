<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";
require_once ROOT_PATH . "/app/Models/TicketStatusHistory.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Attachment.php";
require_once ROOT_PATH . "/app/Services/UploadService.php";
require_once ROOT_PATH . "/app/Services/MailService.php";

class TicketController extends Controller
{
    public function create()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        $this->view('tickets/create');
    }

    public function store()
    {
        Csrf::verify();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';

        if (empty($subject) || empty($description)) {
            $_SESSION['error'] = "Subject and description are required.";
            header("Location: " . BASE_URL . "/tickets/create");
            exit;
        }

        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
            $_SESSION['error'] = "Invalid priority selected.";
            header("Location: " . BASE_URL . "/tickets/create");
            exit;
        }

        $userModel = new User();

        $user = $userModel->findWithOrganization(
            $_SESSION['auth_user_id']
        );

        if (!$user) {
            $_SESSION['error'] = "User account not found.";
            header("Location: " . BASE_URL . "/tickets/create");
            exit;
        }

        if (empty($user['organization_id'])) {
            $_SESSION['error'] = "Your account is not linked with any organization. Please contact admin.";
            header("Location: " . BASE_URL . "/tickets/create");
            exit;
        }

        $ticketModel = new Ticket();

        $ticketNo = $ticketModel->generateTicketNo();

        $created = $ticketModel->create([
            'ticket_no' => $ticketNo,
            'user_id' => $user['id'],
            'organization_id' => $user['organization_id'],
            'created_by' => $user['id'],
            'created_by_role' => $user['role'],
            'subject' => $subject,
            'description' => $description,
            'priority' => $priority,
            'status' => 'open'
        ]);

        if (!$created) {
            $_SESSION['error'] = "Unable to create ticket. Please try again.";
            header("Location: " . BASE_URL . "/tickets/create");
            exit;
        }

        $ticket = $ticketModel->findByTicketNo($ticketNo);

        if (!$ticket) {
            $_SESSION['error'] = "Ticket created but unable to attach files.";
            header("Location: " . BASE_URL . "/tickets");
            exit;
        }

        try {
            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadedFiles = UploadService::uploadMultiple(
                    $_FILES['attachments'],
                    'tickets'
                );

                $attachmentModel = new Attachment();

                foreach ($uploadedFiles as $file) {
                    $attachmentModel->createTicketAttachment([
                        'ticket_id' => $ticket['id'],
                        'uploaded_by' => $user['id'],
                        'original_name' => $file['original_name'],
                        'stored_name' => $file['stored_name'],
                        'file_path' => $file['file_path'],
                        'file_type' => $file['file_type'],
                        'file_size' => $file['file_size']
                    ]);
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Ticket created, but attachment failed: " . $e->getMessage();
            header("Location: " . BASE_URL . "/tickets/show/" . $ticket['id']);
            exit;
        }
        /*
|--------------------------------------------------------------------------
| Send Ticket Created Notifications
|--------------------------------------------------------------------------
|
| 1. Confirmation email to the user who created the ticket.
| 2. Notification email to every active agent.
| 3. Ticket creation remains successful even if any email fails.
|
*/

        $ticketViewUrl = BASE_URL . "/tickets/show/" . $ticket['id'];
        $agentTicketUrl = BASE_URL . "/agent/tickets/show/" . $ticket['id'];

        $userMailSubject = "Ticket Created: " . $ticketNo;

        $userMailMessage =
            "Hello " . $user['full_name'] . ",\n\n" .
            "Your support ticket has been created successfully.\n\n" .
            "Ticket Number: " . $ticketNo . "\n" .
            "Subject: " . $subject . "\n" .
            "Priority: " . ucfirst($priority) . "\n" .
            "Status: Open\n" .
            "Created On: " . date('d M Y, h:i A') . "\n\n" .
            "You can view and track your ticket here:\n" .
            $ticketViewUrl . "\n\n" .
            "You will receive another notification when an agent replies or changes the ticket status.";

        $userMailSent = MailService::sendTicketMail(
            $user['email'],
            $userMailSubject,
            $userMailMessage
        );

        if (!$userMailSent) {
            error_log(
                "Ticket-created user email failed. Ticket ID: " .
                    $ticket['id'] .
                    ", Recipient: " .
                    $user['email']
            );
        }

        /*
|--------------------------------------------------------------------------
| Notify All Active Agents
|--------------------------------------------------------------------------
*/

        $agents = $userModel->getAgents();

        $agentMailSubject =
            "New Support Ticket: " . $ticketNo . " - " . $subject;

        $agentMailFailures = 0;

        foreach ($agents as $agent) {

            if (empty($agent['email'])) {
                continue;
            }

            $agentMailMessage =
                "Hello " . $agent['full_name'] . ",\n\n" .
                "A new support ticket has been created and requires attention.\n\n" .
                "Ticket Number: " . $ticketNo . "\n" .
                "Created By: " . $user['full_name'] . "\n" .
                "Organization: " . ($user['organization_name'] ?? '-') . "\n" .
                "Subject: " . $subject . "\n" .
                "Priority: " . ucfirst($priority) . "\n" .
                "Status: Open\n" .
                "Created On: " . date('d M Y, h:i A') . "\n\n" .
                "Open the ticket here:\n" .
                $agentTicketUrl;

            $agentMailSent = MailService::sendTicketMail(
                $agent['email'],
                $agentMailSubject,
                $agentMailMessage
            );

            if (!$agentMailSent) {
                $agentMailFailures++;

                error_log(
                    "New-ticket agent email failed. Ticket ID: " .
                        $ticket['id'] .
                        ", Agent ID: " .
                        $agent['id'] .
                        ", Recipient: " .
                        $agent['email']
                );
            }
        }

        if ($userMailSent && $agentMailFailures === 0) {
            $_SESSION['success'] =
                "Ticket created successfully. Confirmation and agent notifications were sent.";
        } elseif ($userMailSent) {
            $_SESSION['success'] =
                "Ticket created successfully. Your confirmation email was sent, but some agent notifications failed.";
        } else {
            $_SESSION['success'] =
                "Ticket created successfully, but one or more email notifications could not be sent.";
        }

        header("Location: " . BASE_URL . "/tickets");
        exit;
    }

    public function index()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['auth_user_id']);

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        if (!$user || empty($user['organization_id'])) {

            $tickets = [];
            $totalRecords = 0;
            $totalPages = 1;
        } else {

            $ticketModel = new Ticket();

            $totalRecords = $ticketModel->countOrganizationTickets(
                $user['organization_id']
            );

            $totalPages = max(
                1,
                ceil($totalRecords / $perPage)
            );

            $tickets = $ticketModel->getOrganizationTicketsPaginated(
                $user['organization_id'],
                $perPage,
                $offset
            );
        }

        $this->view('tickets/index', [
            'tickets' => $tickets,
            'page' => $page,
            'perPage' => $perPage,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages
        ]);
    }

    public function show($id)
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['auth_user_id']);

        if (!$user || empty($user['organization_id'])) {
            http_response_code(403);
            echo "Access denied. Your account is not linked to any organization.";
            exit;
        }

        $ticketModel = new Ticket();

        $ticket = $ticketModel->findOrganizationTicket(
            $id,
            $user['organization_id']
        );

        if (!$ticket) {
            http_response_code(404);
            echo "Ticket not found or access denied.";
            exit;
        }

        $replyModel = new TicketReply();
        $replies = $replyModel->getByTicketId($ticket['id']);

        $historyModel = new TicketStatusHistory();
        $statusHistory = $historyModel->getByTicketId($ticket['id']);

        $attachmentModel = new Attachment();
        $attachments = $attachmentModel->getTicketAttachments($ticket['id']);

        $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);

        $this->view('tickets/show', [
            'ticket' => $ticket,
            'replies' => $replies,
            'statusHistory' => $statusHistory,
            'attachments' => $attachments,
            'replyAttachments' => $replyAttachments
        ]);
    }

    public function storeReply($id)
    {
        Csrf::verify();

        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            $_SESSION['error'] = "Reply message is required.";
            header("Location: " . BASE_URL . "/tickets/show/" . $id);
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['auth_user_id']);

        if (!$user || empty($user['organization_id'])) {
            http_response_code(403);
            echo "Access denied. Your account is not linked to any organization.";
            exit;
        }

        $ticketModel = new Ticket();

        $ticket = $ticketModel->findOrganizationTicket(
            $id,
            $user['organization_id']
        );

        if (!$ticket) {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }

        if ($ticket['status'] === 'closed') {
            $_SESSION['error'] = "This ticket is closed. Replies are disabled.";
            header("Location: " . BASE_URL . "/tickets/show/" . $id);
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
            $_SESSION['error'] = "Unable to add reply.";
            header("Location: " . BASE_URL . "/tickets/show/" . $id);
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
            $_SESSION['error'] = "Reply added, but attachment failed: " . $e->getMessage();
            header("Location: " . BASE_URL . "/tickets/show/" . $id);
            exit;
        }

        $_SESSION['success'] = "Reply added successfully.";

        header("Location: " . BASE_URL . "/tickets/show/" . $id);
        exit;
    }
}
