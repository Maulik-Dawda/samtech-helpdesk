#!/usr/bin/php -q
<?php

/**
 * Real-Time cPanel Email Pipe Script
 *
 * Configured in cPanel -> Forwarders -> Pipe to a Program:
 * |/usr/bin/php -q /path/to/samtech-helpdesk/cron/pipe_email.php
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/app/Core/Database.php';
require_once ROOT_PATH . '/app/Services/IncomingMailService.php';

// Read raw email from standard input
$fd = fopen("php://stdin", "r");
$rawEmail = "";
if ($fd) {
    while (!feof($fd)) {
        $rawEmail .= fread($fd, 1024);
    }
    fclose($fd);
}

if (!empty($rawEmail)) {
    IncomingMailService::processRawEmail($rawEmail);
}
