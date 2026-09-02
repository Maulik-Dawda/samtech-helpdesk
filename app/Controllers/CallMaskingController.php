<?php

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Services/CallMaskingService.php';

class CallMaskingController extends Controller
{
    /**
     * Display the Call Owner / Scanner page.
     */
    public function show($code = 'SCN-85F08B')
    {
        $data = [
            'scannerCode' => htmlspecialchars((string)$code, ENT_QUOTES, 'UTF-8'),
            'didNumber' => defined('CALL_MASKING_DID') ? CALL_MASKING_DID : '+97148007268',
        ];

        $this->view('scanner/call-owner', $data);
    }

    /**
     * API endpoint to submit caller phone number and plate_last_4, store in DB, and get DID dialer url.
     */
    public function submitApi()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
            exit;
        }

        $plateLast4 = trim($_POST['plate_last_4'] ?? '');
        $callerPhone = trim($_POST['caller_phone'] ?? '');
        $scannerCode = trim($_POST['scanner_code'] ?? 'DEFAULT');

        $result = CallMaskingService::initiate($scannerCode, $plateLast4, $callerPhone);

        echo json_encode($result);
        exit;
    }
}
