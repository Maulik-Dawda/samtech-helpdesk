<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Services/IncomingMailService.php";

class CronController extends Controller
{
    public function fetchEmails()
    {
        header('Content-Type: application/json');
        
        $results = IncomingMailService::processImapEmails();
        
        echo json_encode([
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $results
        ], JSON_PRETTY_PRINT);
        exit;
    }
}
