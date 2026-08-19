<?php

require_once ROOT_PATH . "/app/Services/MailService.php";
require_once ROOT_PATH . "/app/Models/User.php";

class TicketNotificationService
{
    /*
    |--------------------------------------------------------------------------
    | Ticket Created
    |--------------------------------------------------------------------------
    |
    | Sends:
    | - Confirmation to ticket creator
    | - Notification to all active agents
    |
    */

    public static function ticketCreated(array $ticket, array $creator): void
    {
        $userUrl = BASE_URL . "/tickets/show/" . $ticket['id'];
        $agentUrl = BASE_URL . "/agent/tickets/show/" . $ticket['id'];

        $ticketNo = $ticket['ticket_no'] ?? '-';
        $subject = $ticket['subject'] ?? '-';
        $priority = ucfirst($ticket['priority'] ?? 'medium');
        $status = self::formatStatus($ticket['status'] ?? 'open');
        $organization = $creator['organization_name'] ?? '-';
        $createdAt = $ticket['created_at'] ?? date('Y-m-d H:i:s');

        if (!empty($creator['email'])) {
            $mailSubject = "Ticket Created: {$ticketNo}";

            $mailMessage =
                "Hello " . ($creator['full_name'] ?? 'User') . ",\n\n" .
                "Your support ticket has been created successfully.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Priority: {$priority}\n" .
                "Status: {$status}\n" .
                "Organization: {$organization}\n" .
                "Created On: {$createdAt}\n\n" .
                "View and track your ticket here:\n" .
                $userUrl;

            self::sendSafely(
                $creator['email'],
                $mailSubject,
                $mailMessage,
                'ticket_created_creator',
                $ticket['id'] ?? null
            );
        }

        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }

            $mailSubject =
                "New Support Ticket: {$ticketNo} - {$subject}";

            $mailMessage =
                "Hello " . ($agent['full_name'] ?? 'Agent') . ",\n\n" .
                "A new support ticket has been created and requires attention.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Created By: " . ($creator['full_name'] ?? '-') . "\n" .
                "Organization: {$organization}\n" .
                "Subject: {$subject}\n" .
                "Priority: {$priority}\n" .
                "Status: {$status}\n" .
                "Created On: {$createdAt}\n\n" .
                "Open the ticket here:\n" .
                $agentUrl;

            self::sendSafely(
                $agent['email'],
                $mailSubject,
                $mailMessage,
                'ticket_created_agent',
                $ticket['id'] ?? null,
                $agent['id'] ?? null
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | User Reply
    |--------------------------------------------------------------------------
    |
    | Sends notification to all active agents.
    |
    */

    public static function userReplied(
        array $ticket,
        array $user,
        string $message
    ): void {
        $agentUrl = BASE_URL . "/agent/tickets/show/" . $ticket['id'];

        $ticketNo = $ticket['ticket_no'] ?? '-';
        $subject = $ticket['subject'] ?? '-';
        $status = self::formatStatus($ticket['status'] ?? '');
        $organization = $user['organization_name'] ?? '-';

        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }

            $mailSubject =
                "New User Reply: {$ticketNo} - {$subject}";

            $mailMessage =
                "Hello " . ($agent['full_name'] ?? 'Agent') . ",\n\n" .
                "A user has added a new reply to a support ticket.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Organization: {$organization}\n" .
                "Replied By: " . ($user['full_name'] ?? '-') . "\n" .
                "Current Status: {$status}\n\n" .
                "Reply:\n{$message}\n\n" .
                "Open the ticket here:\n" .
                $agentUrl;

            self::sendSafely(
                $agent['email'],
                $mailSubject,
                $mailMessage,
                'user_reply_agent',
                $ticket['id'] ?? null,
                $agent['id'] ?? null
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Agent Reply
    |--------------------------------------------------------------------------
    |
    | Sends:
    | - Notification to ticket creator
    | - Notification to all active agents
    |
    */

    public static function agentReplied(
        array $ticket,
        array $agent,
        string $message
    ): void {
        $userModel = new User();

        $ticketCreator = $userModel->findById(
            $ticket['user_id'] ?? 0
        );

        $userUrl = BASE_URL . "/tickets/show/" . $ticket['id'];
        $agentUrl = BASE_URL . "/agent/tickets/show/" . $ticket['id'];

        $ticketNo = $ticket['ticket_no'] ?? '-';
        $subject = $ticket['subject'] ?? '-';
        $status = self::formatStatus($ticket['status'] ?? '');
        $agentName = $agent['full_name'] ?? 'Support Agent';

        if ($ticketCreator && !empty($ticketCreator['email'])) {
            $mailSubject =
                "New Agent Reply: {$ticketNo} - {$subject}";

            $mailMessage =
                "Hello " . ($ticketCreator['full_name'] ?? 'User') . ",\n\n" .
                "A support agent has replied to your ticket.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Replied By: {$agentName}\n" .
                "Current Status: {$status}\n\n" .
                "Reply:\n{$message}\n\n" .
                "View and respond to the ticket here:\n" .
                $userUrl;

            self::sendSafely(
                $ticketCreator['email'],
                $mailSubject,
                $mailMessage,
                'agent_reply_creator',
                $ticket['id'] ?? null
            );
        }

        $agents = self::getActiveAgents();

        foreach ($agents as $recipientAgent) {
            if (empty($recipientAgent['email'])) {
                continue;
            }

            $mailSubject =
                "Agent Reply Added: {$ticketNo} - {$subject}";

            $mailMessage =
                "Hello " . ($recipientAgent['full_name'] ?? 'Agent') . ",\n\n" .
                "An agent has replied to a support ticket.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Replied By: {$agentName}\n" .
                "Current Status: {$status}\n\n" .
                "Reply:\n{$message}\n\n" .
                "Open the ticket here:\n" .
                $agentUrl;

            self::sendSafely(
                $recipientAgent['email'],
                $mailSubject,
                $mailMessage,
                'agent_reply_agent',
                $ticket['id'] ?? null,
                $recipientAgent['id'] ?? null
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Status Updated
    |--------------------------------------------------------------------------
    |
    | Sends status change to:
    | - Ticket creator
    | - All active agents
    |
    */

    public static function statusUpdated(
        array $ticket,
        string $oldStatus,
        string $newStatus,
        array $updatedBy,
        string $resolutionMessage = ''
    ): void {
        $userModel = new User();

        $ticketCreator = $userModel->findById(
            $ticket['user_id'] ?? 0
        );

        $userUrl = BASE_URL . "/tickets/show/" . $ticket['id'];
        $agentUrl = BASE_URL . "/agent/tickets/show/" . $ticket['id'];

        $ticketNo = $ticket['ticket_no'] ?? '-';
        $subject = $ticket['subject'] ?? '-';

        $oldStatusLabel = self::formatStatus($oldStatus);
        $newStatusLabel = self::formatStatus($newStatus);
        $updatedByName = $updatedBy['full_name'] ?? 'Support Agent';
        $updatedAt = date('d M Y, h:i A');

        $resolutionSection = '';

        if (
            $newStatus === 'closed' &&
            trim($resolutionMessage) !== ''
        ) {
            $resolutionSection =
                "\nFinal Resolution:\n" .
                trim($resolutionMessage) .
                "\n";
        }

        if ($ticketCreator && !empty($ticketCreator['email'])) {
            $mailSubject =
                "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";

            $mailMessage =
                "Hello " . ($ticketCreator['full_name'] ?? 'User') . ",\n\n" .
                "The status of your support ticket has been updated.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Previous Status: {$oldStatusLabel}\n" .
                "New Status: {$newStatusLabel}\n" .
                "Updated By: {$updatedByName}\n" .
                "Updated On: {$updatedAt}\n" .
                $resolutionSection .
                "\nView the ticket here:\n" .
                $userUrl;

            self::sendSafely(
                $ticketCreator['email'],
                $mailSubject,
                $mailMessage,
                'status_update_creator',
                $ticket['id'] ?? null
            );
        }

        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }

            $mailSubject =
                "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";

            $mailMessage =
                "Hello " . ($agent['full_name'] ?? 'Agent') . ",\n\n" .
                "A support ticket status has been updated.\n\n" .
                "Ticket Number: {$ticketNo}\n" .
                "Subject: {$subject}\n" .
                "Previous Status: {$oldStatusLabel}\n" .
                "New Status: {$newStatusLabel}\n" .
                "Updated By: {$updatedByName}\n" .
                "Updated On: {$updatedAt}\n" .
                $resolutionSection .
                "\nOpen the ticket here:\n" .
                $agentUrl;

            self::sendSafely(
                $agent['email'],
                $mailSubject,
                $mailMessage,
                'status_update_agent',
                $ticket['id'] ?? null,
                $agent['id'] ?? null
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private static function getActiveAgents(): array
    {
        $userModel = new User();

        return $userModel->getAgents();
    }

    private static function sendSafely(
        string $recipient,
        string $subject,
        string $message,
        string $event,
        $ticketId = null,
        $recipientUserId = null
    ): bool {
        try {
            $sent = MailService::sendTicketMail(
                $recipient,
                $subject,
                $message
            );

            if (!$sent) {
                error_log(
                    "Ticket notification failed | " .
                    "Event: {$event} | " .
                    "Ticket ID: " . ($ticketId ?? '-') . " | " .
                    "Recipient User ID: " . ($recipientUserId ?? '-') . " | " .
                    "Email: {$recipient}"
                );
            }

            return $sent;

        } catch (Throwable $e) {
            error_log(
                "Ticket notification exception | " .
                "Event: {$event} | " .
                "Ticket ID: " . ($ticketId ?? '-') . " | " .
                "Recipient User ID: " . ($recipientUserId ?? '-') . " | " .
                "Email: {$recipient} | " .
                "Error: " . $e->getMessage()
            );

            return false;
        }
    }

    private static function formatStatus(string $status): string
    {
        return ucwords(
            str_replace('_', ' ', $status)
        );
    }
}