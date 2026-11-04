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

        $sentEmails = [];

        // --- Notify the ticket creator ---
        if (!empty($creator['email'])) {
            $creatorEmail = trim($creator['email']);
            $sentEmails[strtolower($creatorEmail)] = true;

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
                $creatorEmail,
                $mailSubject,
                $mailMessage,
                'ticket_created_creator',
                $ticket['id'] ?? null
            );
        }

        // --- Notify all active agents ---
        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }
            $agentEmail = strtolower(trim($agent['email']));
            if (isset($sentEmails[$agentEmail])) {
                continue;
            }
            $sentEmails[$agentEmail] = true;

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

        // --- Notify all admin users ---
        $userModel = new User();
        $admins = $userModel->getAdmins();

        foreach ($admins as $admin) {
            if (empty($admin['email'])) {
                continue;
            }
            $adminEmail = strtolower(trim($admin['email']));
            if (isset($sentEmails[$adminEmail])) {
                continue;
            }
            $sentEmails[$adminEmail] = true;

            $mailSubject = "New Support Ticket: {$ticketNo} - {$subject}";

            $mailMessage =
                "Hello " . ($admin['full_name'] ?? 'Admin') . ",\n\n" .
                "A new support ticket has been submitted.\n\n" .
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
                $admin['email'],
                $mailSubject,
                $mailMessage,
                'ticket_created_admin',
                $ticket['id'] ?? null,
                $admin['id'] ?? null
            );
        }

        // --- Notify all users of the organization ---
        $organizationId = $ticket['organization_id'] ?? ($creator['organization_id'] ?? null);

        if ($organizationId) {
            $orgUsers = $userModel->getOrganizationUsers($organizationId);

            foreach ($orgUsers as $orgUser) {
                if (empty($orgUser['email'])) {
                    continue;
                }
                $userEmail = strtolower(trim($orgUser['email']));
                if (isset($sentEmails[$userEmail])) {
                    continue;
                }
                $sentEmails[$userEmail] = true;

                $mailSubject = "New Ticket Submitted: {$ticketNo}";
                $mailMessage =
                    "Hello " . ($orgUser['full_name'] ?? 'User') . ",\n\n" .
                    "A new support ticket has been submitted for your organization.\n\n" .
                    "Ticket Number: {$ticketNo}\n" .
                    "Submitted By: " . ($creator['full_name'] ?? '-') . "\n" .
                    "Subject: {$subject}\n" .
                    "Priority: {$priority}\n" .
                    "Status: {$status}\n" .
                    "Created On: {$createdAt}\n\n" .
                    "View the ticket here:\n" .
                    $userUrl;

                self::sendSafely(
                    $orgUser['email'],
                    $mailSubject,
                    $mailMessage,
                    'ticket_created_org_user',
                    $ticket['id'] ?? null,
                    $orgUser['id'] ?? null
                );
            }

            // --- Notify additional org emails ---
            require_once ROOT_PATH . "/app/Models/Organization.php";
            $orgModel = new Organization();
            $additionalEmails = $orgModel->getAdditionalEmails($organizationId);

            foreach ($additionalEmails as $extraEmail) {
                $extraEmail = trim($extraEmail);
                if (empty($extraEmail) || !filter_var($extraEmail, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $extraEmailNorm = strtolower($extraEmail);
                if (isset($sentEmails[$extraEmailNorm])) {
                    continue;
                }
                $sentEmails[$extraEmailNorm] = true;

                $mailSubject = "New Ticket Submitted: {$ticketNo}";
                $mailMessage =
                    "Hello,\n\n" .
                    "A new support ticket has been submitted for your organization.\n\n" .
                    "Ticket Number: {$ticketNo}\n" .
                    "Submitted By: " . ($creator['full_name'] ?? '-') . "\n" .
                    "Organization: {$organization}\n" .
                    "Subject: {$subject}\n" .
                    "Priority: {$priority}\n" .
                    "Status: {$status}\n" .
                    "Created On: {$createdAt}\n\n" .
                    "View the ticket here:\n" .
                    $userUrl;

                self::sendSafely(
                    $extraEmail,
                    $mailSubject,
                    $mailMessage,
                    'ticket_created_extra_email',
                    $ticket['id'] ?? null
                );
            }
        }

        MailService::closeTicketMailer();
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
        $organization = $user['organization_name'] ?? $ticket['organization_name'] ?? '-';
        $branch = $ticket['branch_name'] ?? '';
        $repliedBy = $user['full_name'] ?? 'User';

        $ticketDetails = [
            'ticket_no' => $ticketNo,
            'subject' => $subject,
            'status' => $status,
            'replied_by' => $repliedBy,
            'organization' => $organization,
            'branch' => $branch
        ];

        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }

            $mailSubject = "New User Reply: {$ticketNo} - {$subject}";
            $recipientName = $agent['full_name'] ?? 'Agent';
            $introText = "A user has added a new reply to a support ticket.";

            self::sendSafelyReply(
                $agent['email'],
                $mailSubject,
                $recipientName,
                $introText,
                $message,
                $ticketDetails,
                $agentUrl,
                'user_reply_agent',
                $ticket['id'] ?? null,
                $agent['id'] ?? null
            );
        }

        MailService::closeTicketMailer();
    }

    /*
    |--------------------------------------------------------------------------
    | Agent Reply
    |--------------------------------------------------------------------------
    |
    | Sends:
    | - Notification to ticket creator
    | - Notification to all active agents
    | - Notification to additional organization emails
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
        $branch = $ticket['branch_name'] ?? '';

        $organization = '-';
        $organizationId = $ticket['organization_id'] ?? ($ticketCreator['organization_id'] ?? null);

        if (!empty($ticketCreator['organization_name'])) {
            $organization = $ticketCreator['organization_name'];
        } elseif (!empty($organizationId)) {
            require_once ROOT_PATH . "/app/Models/Organization.php";
            $orgModel = new Organization();
            $org = $orgModel->findById($organizationId);
            if ($org) {
                $organization = $org['name'];
            }
        }

        $ticketDetails = [
            'ticket_no' => $ticketNo,
            'subject' => $subject,
            'status' => $status,
            'replied_by' => $agentName,
            'organization' => $organization,
            'branch' => $branch
        ];

        $sentEmails = [];

        if ($ticketCreator && !empty($ticketCreator['email'])) {
            $creatorEmail = strtolower(trim($ticketCreator['email']));
            $sentEmails[$creatorEmail] = true;

            $mailSubject = "New Agent Reply: {$ticketNo} - {$subject}";
            $recipientName = $ticketCreator['full_name'] ?? 'User';
            $introText = "A support agent has replied to your ticket.";

            self::sendSafelyReply(
                $ticketCreator['email'],
                $mailSubject,
                $recipientName,
                $introText,
                $message,
                $ticketDetails,
                $userUrl,
                'agent_reply_creator',
                $ticket['id'] ?? null
            );
        }

        $agents = self::getActiveAgents();

        foreach ($agents as $recipientAgent) {
            if (empty($recipientAgent['email'])) {
                continue;
            }
            $agentEmail = strtolower(trim($recipientAgent['email']));
            if (isset($sentEmails[$agentEmail])) {
                continue;
            }
            $sentEmails[$agentEmail] = true;

            $mailSubject = "Agent Reply Added: {$ticketNo} - {$subject}";
            $recipientName = $recipientAgent['full_name'] ?? 'Agent';
            $introText = "An agent has replied to a support ticket.";

            self::sendSafelyReply(
                $recipientAgent['email'],
                $mailSubject,
                $recipientName,
                $introText,
                $message,
                $ticketDetails,
                $agentUrl,
                'agent_reply_agent',
                $ticket['id'] ?? null,
                $recipientAgent['id'] ?? null
            );
        }

        // Notify additional organization emails
        if ($organizationId) {
            require_once ROOT_PATH . "/app/Models/Organization.php";
            $orgModel = new Organization();
            $additionalEmails = $orgModel->getAdditionalEmails($organizationId);

            foreach ($additionalEmails as $extraEmail) {
                $extraEmail = trim($extraEmail);
                if (empty($extraEmail) || !filter_var($extraEmail, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $extraEmailNorm = strtolower($extraEmail);
                if (isset($sentEmails[$extraEmailNorm])) {
                    continue;
                }
                $sentEmails[$extraEmailNorm] = true;

                $mailSubject = "Agent Reply Added: {$ticketNo} - {$subject}";
                $recipientName = 'Organization Member';
                $introText = "A support agent has replied to a ticket for your organization.";

                self::sendSafelyReply(
                    $extraEmail,
                    $mailSubject,
                    $recipientName,
                    $introText,
                    $message,
                    $ticketDetails,
                    $userUrl,
                    'agent_reply_extra_email',
                    $ticket['id'] ?? null
                );
            }
        }

        MailService::closeTicketMailer();
    }

    /*
    |--------------------------------------------------------------------------
    | Status Updated
    |--------------------------------------------------------------------------
    |
    | Sends status change to:
    | - Ticket creator
    | - All active agents
    | - All admin users
    | - All users in ticket's organization
    | - All additional organization emails
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

        $sentEmails = [];

        // 1. Notify ticket creator
        if ($ticketCreator && !empty($ticketCreator['email'])) {
            $creatorEmail = trim($ticketCreator['email']);
            $sentEmails[strtolower($creatorEmail)] = true;

            $mailSubject = "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";
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
                $creatorEmail,
                $mailSubject,
                $mailMessage,
                'status_update_creator',
                $ticket['id'] ?? null
            );
        }

        // 2. Notify all active agents
        $agents = self::getActiveAgents();

        foreach ($agents as $agent) {
            if (empty($agent['email'])) {
                continue;
            }
            $agentEmail = strtolower(trim($agent['email']));
            if (isset($sentEmails[$agentEmail])) {
                continue;
            }
            $sentEmails[$agentEmail] = true;

            $mailSubject = "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";
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

        // 3. Notify all admin users
        $admins = $userModel->getAdmins();
        foreach ($admins as $admin) {
            if (empty($admin['email'])) {
                continue;
            }
            $adminEmail = strtolower(trim($admin['email']));
            if (isset($sentEmails[$adminEmail])) {
                continue;
            }
            $sentEmails[$adminEmail] = true;

            $mailSubject = "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";
            $mailMessage =
                "Hello " . ($admin['full_name'] ?? 'Admin') . ",\n\n" .
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
                $admin['email'],
                $mailSubject,
                $mailMessage,
                'status_update_admin',
                $ticket['id'] ?? null,
                $admin['id'] ?? null
            );
        }

        // 4. Notify organization users & additional organization emails
        $organizationId = $ticket['organization_id'] ?? ($ticketCreator['organization_id'] ?? null);
        if ($organizationId) {
            $orgUsers = $userModel->getOrganizationUsers($organizationId);
            foreach ($orgUsers as $orgUser) {
                if (empty($orgUser['email'])) {
                    continue;
                }
                $userEmail = strtolower(trim($orgUser['email']));
                if (isset($sentEmails[$userEmail])) {
                    continue;
                }
                $sentEmails[$userEmail] = true;

                $mailSubject = "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";
                $mailMessage =
                    "Hello " . ($orgUser['full_name'] ?? 'User') . ",\n\n" .
                    "The status of a support ticket for your organization has been updated.\n\n" .
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
                    $orgUser['email'],
                    $mailSubject,
                    $mailMessage,
                    'status_update_org_user',
                    $ticket['id'] ?? null,
                    $orgUser['id'] ?? null
                );
            }

            // --- Notify additional organization emails ---
            require_once ROOT_PATH . "/app/Models/Organization.php";
            $orgModel = new Organization();
            $additionalEmails = $orgModel->getAdditionalEmails($organizationId);

            foreach ($additionalEmails as $extraEmail) {
                $extraEmail = trim($extraEmail);
                if (empty($extraEmail) || !filter_var($extraEmail, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $extraEmailNorm = strtolower($extraEmail);
                if (isset($sentEmails[$extraEmailNorm])) {
                    continue;
                }
                $sentEmails[$extraEmailNorm] = true;

                $mailSubject = "Ticket Status Updated: {$ticketNo} - {$newStatusLabel}";
                $mailMessage =
                    "Hello,\n\n" .
                    "The status of a support ticket for your organization has been updated.\n\n" .
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
                    $extraEmail,
                    $mailSubject,
                    $mailMessage,
                    'status_update_extra_email',
                    $ticket['id'] ?? null
                );
            }
        }

        MailService::closeTicketMailer();
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

    private static function sendSafelyReply(
        string $recipient,
        string $subject,
        string $recipientName,
        string $introText,
        string $replyMessage,
        array $ticketDetails,
        string $actionUrl,
        string $event,
        $ticketId = null,
        $recipientUserId = null
    ): bool {
        try {
            $sent = MailService::sendTicketReplyMail(
                $recipient,
                $subject,
                $recipientName,
                $introText,
                $replyMessage,
                $ticketDetails,
                $actionUrl
            );

            if (!$sent) {
                error_log(
                    "Ticket reply notification failed | " .
                    "Event: {$event} | " .
                    "Ticket ID: " . ($ticketId ?? '-') . " | " .
                    "Recipient User ID: " . ($recipientUserId ?? '-') . " | " .
                    "Email: {$recipient}"
                );
            }

            return $sent;

        } catch (Throwable $e) {
            error_log(
                "Ticket reply notification exception | " .
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