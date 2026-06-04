<?php

require_once "../app/Core/Controller.php";
require_once "../app/Models/Attachment.php";
require_once "../app/Models/User.php";

class AttachmentController extends Controller
{
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function downloadTicket($id)
    {
        $this->startSession();

        AuthMiddleware::timeout();

        if (!isset($_SESSION['auth_user_id'])) {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }

        $attachmentModel = new Attachment();
        $attachment = $attachmentModel->findTicketAttachment($id);

        if (!$attachment) {
            http_response_code(404);
            echo "Attachment not found.";
            exit;
        }

        $this->authorizeAndDownload($attachment);
    }

    public function downloadReply($id)
    {
        $this->startSession();

        AuthMiddleware::timeout();

        if (!isset($_SESSION['auth_user_id'])) {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }

        $attachmentModel = new Attachment();
        $attachment = $attachmentModel->findReplyAttachment($id);

        if (!$attachment) {
            http_response_code(404);
            echo "Attachment not found.";
            exit;
        }

        $this->authorizeAndDownload($attachment);
    }

    private function authorizeAndDownload($attachment)
    {
        $role = $_SESSION['auth_user_role'] ?? '';

        if ($role === 'admin' || $role === 'agent') {
            $this->downloadFile($attachment);
        }

        if ($role === 'user') {
            $userModel = new User();
            $user = $userModel->findById($_SESSION['auth_user_id']);

            if (
                !$user ||
                empty($user['organization_id']) ||
                (int)$user['organization_id'] !== (int)$attachment['organization_id']
            ) {
                http_response_code(403);
                echo "Access denied.";
                exit;
            }

            $this->downloadFile($attachment);
        }

        http_response_code(403);
        echo "Access denied.";
        exit;
    }

    private function downloadFile($attachment)
    {
        $fullPath = realpath(__DIR__ . "/../../" . $attachment['file_path']);

        $storagePath = realpath(__DIR__ . "/../../storage/uploads");

        if (!$fullPath || !$storagePath || strpos($fullPath, $storagePath) !== 0) {
            http_response_code(404);
            echo "File not found.";
            exit;
        }

        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo "File not found.";
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $attachment['file_type']);
        header('Content-Disposition: attachment; filename="' . basename($attachment['original_name']) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');

        readfile($fullPath);
        exit;
    }
}