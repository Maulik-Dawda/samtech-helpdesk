<?php

require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";
require_once ROOT_PATH . "/app/Models/Attachment.php";
require_once ROOT_PATH . "/app/Services/TicketNotificationService.php";

class IncomingMailService
{
    private static $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'js', 'exe', 'bat', 'cmd', 'sh', 'msi', 'html', 'htm'
    ];

    /**
     * Fetch unread emails from IMAP mailbox and process them into tickets or replies.
     */
    public static function processImapEmails(): array
    {
        $results = [
            'processed' => 0,
            'ignored' => 0,
            'unread_found' => 0,
            'connection' => 'not_attempted',
            'log' => [],
            'errors' => []
        ];

        if (!function_exists('imap_open')) {
            $errorMsg = "IMAP PHP extension is not installed or enabled on this server.";
            error_log("IncomingMailService Error: " . $errorMsg);
            $results['errors'][] = $errorMsg;
            return $results;
        }

        $host = defined('INCOMING_MAIL_HOST') ? INCOMING_MAIL_HOST : '';
        $port = defined('INCOMING_MAIL_PORT') ? INCOMING_MAIL_PORT : 993;
        $encryption = defined('INCOMING_MAIL_ENCRYPTION') ? INCOMING_MAIL_ENCRYPTION : 'ssl';
        $username = defined('INCOMING_MAIL_USERNAME') ? INCOMING_MAIL_USERNAME : '';
        $password = defined('INCOMING_MAIL_PASSWORD') ? INCOMING_MAIL_PASSWORD : '';

        if (empty($host) || empty($username) || empty($password)) {
            $errorMsg = "Incoming mail credentials (INCOMING_MAIL_HOST, INCOMING_MAIL_USERNAME, INCOMING_MAIL_PASSWORD) are not configured in .env";
            error_log("IncomingMailService Error: " . $errorMsg);
            $results['errors'][] = $errorMsg;
            return $results;
        }

        // Build IMAP connection string
        $encFlag = strtolower($encryption) === 'ssl' ? '/imap/ssl/novalidate-cert' : (strtolower($encryption) === 'tls' ? '/imap/tls/novalidate-cert' : '/imap/novalidate-cert');
        $mailbox = "{" . $host . ":" . $port . $encFlag . "}INBOX";

        $results['log'][] = "Connecting to IMAP mailbox: {$mailbox} with user: {$username}";

        $inbox = @imap_open($mailbox, $username, $password);

        if (!$inbox) {
            $imapError = imap_last_error();
            $errorMsg = "Failed to connect to IMAP server {$mailbox}: " . $imapError;
            error_log("IncomingMailService Error: " . $errorMsg);
            $results['connection'] = 'failed';
            $results['errors'][] = $errorMsg;
            return $results;
        }

        $results['connection'] = 'connected';
        $emails = imap_search($inbox, 'UNSEEN');

        if (!$emails) {
            $results['log'][] = "No unread (UNSEEN) emails found in INBOX.";
            imap_close($inbox);
            return $results;
        }

        $results['unread_found'] = count($emails);
        $results['log'][] = "Found " . count($emails) . " unread email(s).";

        foreach ($emails as $msgNumber) {
            try {
                $header = imap_headerinfo($inbox, $msgNumber);

                if (!$header || empty($header->from[0])) {
                    $results['log'][] = "Msg #{$msgNumber}: Skipped (No header/from address)";
                    continue;
                }

                $senderMailbox = $header->from[0]->mailbox ?? '';
                $senderHost = $header->from[0]->host ?? '';
                $senderEmail = strtolower(trim($senderMailbox . '@' . $senderHost));

                // Verify user existence in database
                $userModel = new User();
                $user = $userModel->findByEmail($senderEmail);

                if (!$user || (int)($user['is_active'] ?? 0) !== 1) {
                    // Ignore email from unregistered or inactive sender
                    $results['log'][] = "Msg #{$msgNumber} from {$senderEmail}: Ignored (Sender email not found or inactive in database)";
                    error_log("IncomingMailService: Ignored email from unregistered/inactive sender: {$senderEmail}");
                    $results['ignored']++;
                    // Mark message as seen so it's not repeatedly checked
                    imap_setflag_full($inbox, (string)$msgNumber, "\\Seen");
                    continue;
                }

                // Decode Subject
                $rawSubject = $header->subject ?? 'No Subject';
                $subject = self::decodeMimeHeader($rawSubject);

                // Extract Body & Attachments
                $parsedData = self::parseImapMessage($inbox, $msgNumber);
                $body = !empty($parsedData['html']) ? $parsedData['html'] : (!empty($parsedData['plain']) ? nl2br(htmlspecialchars($parsedData['plain'])) : 'No content');
                $attachments = $parsedData['attachments'] ?? [];

                // Process parsed email data for this verified user
                $success = self::handleParsedEmail($user, $subject, $body, $attachments);

                if ($success) {
                    $results['processed']++;
                    $results['log'][] = "Msg #{$msgNumber} from {$senderEmail}: Successfully processed into ticket/reply!";
                    imap_setflag_full($inbox, (string)$msgNumber, "\\Seen");
                } else {
                    $results['log'][] = "Msg #{$msgNumber} from {$senderEmail}: Failed to save ticket in database.";
                }
            } catch (Throwable $e) {
                error_log("IncomingMailService Error processing message #{$msgNumber}: " . $e->getMessage());
                $results['errors'][] = "Msg #{$msgNumber}: " . $e->getMessage();
            }
        }

        imap_close($inbox);
        return $results;
    }

    /**
     * Process a raw email stream (used by cPanel email pipe script).
     */
    public static function processRawEmail(string $rawContent): bool
    {
        if (empty($rawContent)) {
            return false;
        }

        try {
            $parsed = self::parseRawMimeStream($rawContent);

            $senderEmail = strtolower(trim($parsed['from'] ?? ''));

            if (empty($senderEmail)) {
                return false;
            }

            $userModel = new User();
            $user = $userModel->findByEmail($senderEmail);

            if (!$user || (int)($user['is_active'] ?? 0) !== 1) {
                error_log("IncomingMailService Pipe: Ignored email from unregistered/inactive sender: {$senderEmail}");
                return false;
            }

            $subject = !empty($parsed['subject']) ? self::decodeMimeHeader($parsed['subject']) : 'No Subject';
            $body = !empty($parsed['html']) ? $parsed['html'] : (!empty($parsed['plain']) ? nl2br(htmlspecialchars($parsed['plain'])) : 'No content');
            $attachments = $parsed['attachments'] ?? [];

            return self::handleParsedEmail($user, $subject, $body, $attachments);
        } catch (Throwable $e) {
            error_log("IncomingMailService Pipe Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle email for a verified user: creates ticket or reply and attaches files.
     */
    private static function handleParsedEmail(array $user, string $subject, string $body, array $attachments): bool
    {
        $userModel = new User();
        $fullUser = $userModel->findWithOrganization($user['id']);

        $orgId = !empty($fullUser['organization_id']) ? (int)$fullUser['organization_id'] : null;

        // Check if subject references an existing ticket (e.g. TKT-20261006-XXXXXX)
        $existingTicket = self::findTicketFromSubject($subject);

        if ($existingTicket) {
            // Create Ticket Reply
            $ticketReplyModel = new TicketReply();
            $replyId = $ticketReplyModel->create([
                'ticket_id' => $existingTicket['id'],
                'user_id' => $user['id'],
                'user_role' => $user['role'] ?? 'user',
                'message' => $body
            ]);

            if ($replyId) {
                // Save attachments for reply
                $attachmentModel = new Attachment();
                foreach ($attachments as $att) {
                    $savedFile = self::saveAttachmentFile($att, 'replies');
                    if ($savedFile) {
                        $attachmentModel->createReplyAttachment([
                            'reply_id' => $replyId,
                            'ticket_id' => $existingTicket['id'],
                            'uploaded_by' => $user['id'],
                            'original_name' => $savedFile['original_name'],
                            'stored_name' => $savedFile['stored_name'],
                            'file_path' => $savedFile['file_path'],
                            'file_type' => $savedFile['file_type'],
                            'file_size' => $savedFile['file_size']
                        ]);
                    }
                }

                // Notify about new reply
                TicketNotificationService::replyAdded([
                    'ticket_id' => $existingTicket['id'],
                    'ticket_no' => $existingTicket['ticket_no'],
                    'reply_user_name' => $user['full_name'] ?? $user['email'],
                    'reply_message' => $body,
                    'user_id' => $existingTicket['user_id'],
                    'assigned_agent_id' => $existingTicket['assigned_agent_id'] ?? null
                ]);

                return true;
            }
        } else {
            // Create New Ticket
            $ticketModel = new Ticket();
            $ticketNo = $ticketModel->generateTicketNo();

            $created = $ticketModel->create([
                'ticket_no' => $ticketNo,
                'user_id' => $user['id'],
                'organization_id' => $orgId,
                'branch_id' => null,
                'assigned_agent_id' => null,
                'created_by' => $user['id'],
                'created_by_role' => $user['role'] ?? 'user',
                'subject' => $subject,
                'description' => $body,
                'priority' => 'medium',
                'status' => 'open'
            ]);

            if ($created) {
                $ticket = $ticketModel->findByTicketNo($ticketNo);
                if ($ticket) {
                    $attachmentModel = new Attachment();
                    foreach ($attachments as $att) {
                        $savedFile = self::saveAttachmentFile($att, 'tickets');
                        if ($savedFile) {
                            $attachmentModel->createTicketAttachment([
                                'ticket_id' => $ticket['id'],
                                'uploaded_by' => $user['id'],
                                'original_name' => $savedFile['original_name'],
                                'stored_name' => $savedFile['stored_name'],
                                'file_path' => $savedFile['file_path'],
                                'file_type' => $savedFile['file_type'],
                                'file_size' => $savedFile['file_size']
                            ]);
                        }
                    }

                    // Notify creator and agents
                    TicketNotificationService::ticketCreated($ticket, $fullUser ?: $user);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if subject contains a ticket number reference like TKT-YYYYMMDD-XXXXXX
     */
    private static function findTicketFromSubject(string $subject): ?array
    {
        if (preg_match('/TKT-\d{8}-[A-Za-z0-9]+/i', $subject, $matches)) {
            $ticketNo = strtoupper($matches[0]);
            $ticketModel = new Ticket();
            $ticket = $ticketModel->findByTicketNo($ticketNo);
            if ($ticket) {
                return $ticket;
            }
        }
        return null;
    }

    /**
     * Decode MIME header encoded text (e.g. =?UTF-8?B?...)
     */
    private static function decodeMimeHeader(string $text): string
    {
        $elements = imap_mime_header_decode($text);
        $decoded = '';
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $decoded .= $element->text;
            }
        } else {
            $decoded = $text;
        }
        return trim($decoded);
    }

    /**
     * Parse IMAP message structure, extract text, HTML body and attachments.
     */
    private static function parseImapMessage($inbox, int $msgNumber): array
    {
        $structure = imap_fetchstructure($inbox, $msgNumber);
        $result = [
            'plain' => '',
            'html' => '',
            'attachments' => []
        ];

        if (!$structure) {
            return $result;
        }

        if (empty($structure->parts)) {
            self::getPartData($inbox, $msgNumber, $structure, '', $result);
        } else {
            foreach ($structure->parts as $index => $part) {
                self::getPartData($inbox, $msgNumber, $part, (string)($index + 1), $result);
            }
        }

        return $result;
    }

    /**
     * Recursive helper for parsing message structure parts.
     */
    private static function getPartData($inbox, int $msgNumber, $part, string $partNum, array &$result): void
    {
        // Check for attachment
        $filename = '';
        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) === 'filename' || strtolower($object->attribute) === 'name') {
                    $filename = $object->value;
                }
            }
        }

        if (empty($filename) && $part->ifparameters) {
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) === 'name' || strtolower($object->attribute) === 'filename') {
                    $filename = $object->value;
                }
            }
        }

        $isAttachment = false;
        if ($part->ifdisposition && strtolower($part->disposition) === 'attachment') {
            $isAttachment = true;
        }

        if (!empty($filename) || $isAttachment) {
            $data = !empty($partNum) ? imap_fetchbody($inbox, $msgNumber, $partNum) : imap_body($inbox, $msgNumber);

            if ($part->encoding === 3) {
                $data = base64_decode($data);
            } elseif ($part->encoding === 4) {
                $data = quoted_printable_decode($data);
            }

            $result['attachments'][] = [
                'name' => self::decodeMimeHeader($filename ?: 'attachment_' . time()),
                'data' => $data,
                'size' => strlen($data)
            ];
            return;
        }

        // Sub-parts
        if (!empty($part->parts)) {
            foreach ($part->parts as $index => $subPart) {
                $prefix = !empty($partNum) ? $partNum . '.' : '';
                self::getPartData($inbox, $msgNumber, $subPart, $prefix . ($index + 1), $result);
            }
            return;
        }

        // Text body
        if ($part->type === 0) {
            $data = !empty($partNum) ? imap_fetchbody($inbox, $msgNumber, $partNum) : imap_body($inbox, $msgNumber);

            if ($part->encoding === 3) {
                $data = base64_decode($data);
            } elseif ($part->encoding === 4) {
                $data = quoted_printable_decode($data);
            }

            if (strtolower($part->subtype) === 'plain') {
                $result['plain'] .= $data;
            } elseif (strtolower($part->subtype) === 'html') {
                $result['html'] .= $data;
            }
        }
    }

    /**
     * Parse raw MIME email stream (for email piping).
     */
    private static function parseRawMimeStream(string $raw): array
    {
        $result = [
            'from' => '',
            'subject' => '',
            'plain' => '',
            'html' => '',
            'attachments' => []
        ];

        // Split headers and body
        $parts = explode("\r\n\r\n", $raw, 2);
        if (count($parts) < 2) {
            $parts = explode("\n\n", $raw, 2);
        }

        $headersRaw = $parts[0] ?? '';
        $bodyRaw = $parts[1] ?? '';

        // Extract From header
        if (preg_match('/^From:\s*(.*)$/mi', $headersRaw, $matches)) {
            $fromLine = trim($matches[1]);
            if (preg_match('/<([^>]+)>/', $fromLine, $emailMatches)) {
                $result['from'] = $emailMatches[1];
            } else {
                $result['from'] = $fromLine;
            }
        }

        // Extract Subject header
        if (preg_match('/^Subject:\s*(.*)$/mi', $headersRaw, $matches)) {
            $result['subject'] = trim($matches[1]);
        }

        // Parse body content simply or plain text
        $result['plain'] = strip_tags($bodyRaw);
        $result['html'] = $bodyRaw;

        return $result;
    }

    /**
     * Save an extracted attachment binary to disk and return file metadata.
     */
    private static function saveAttachmentFile(array $att, string $subfolder): ?array
    {
        $originalName = $att['name'] ?? ('file_' . time());
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (empty($extension) || in_array($extension, self::$blockedExtensions)) {
            return null;
        }

        $storedName = bin2hex(random_bytes(16)) . "_" . time() . "." . $extension;
        $targetDir = ROOT_PATH . "/storage/uploads/" . $subfolder;

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . "/" . $storedName;

        if (file_put_contents($targetPath, $att['data']) !== false) {
            $mimeType = mime_content_type($targetPath) ?: ('application/' . $extension);
            return [
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'file_path' => "uploads/" . $subfolder . "/" . $storedName,
                'file_type' => $mimeType,
                'file_size' => strlen($att['data'])
            ];
        }

        return null;
    }
}
