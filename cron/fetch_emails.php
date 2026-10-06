<?php

/**
 * IMAP Background Polling Cron Script
 * 
 * Usage via CLI / cPanel Cron Job:
 * php /path/to/samtech-helpdesk/cron/fetch_emails.php
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/app/Core/Database.php';
require_once ROOT_PATH . '/app/Services/IncomingMailService.php';

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
}

echo "[" . date('Y-m-d H:i:s') . "] Starting incoming email fetch...\n";

$results = IncomingMailService::processImapEmails();

echo "Processed tickets/replies: " . $results['processed'] . "\n";
echo "Ignored emails (unregistered senders): " . $results['ignored'] . "\n";

if (!empty($results['errors'])) {
    echo "Errors:\n";
    foreach ($results['errors'] as $err) {
        echo " - " . $err . "\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Finished.\n";
