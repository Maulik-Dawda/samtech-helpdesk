<?php

require_once ROOT_PATH . '/app/Models/ScannerCall.php';

class CallMaskingService
{
    /**
     * Initiates call masking by fetching owner's customer_number from database,
     * storing caller phone number and plate details into DB,
     * and returning the DID number for direct phone dial-in.
     */
    public static function initiate(string $scannerCode, string $plateLast4, string $callerPhone): array
    {
        $cleanPlate = preg_replace('/\D/', '', $plateLast4);
        if (strlen($cleanPlate) !== 4) {
            return [
                'success' => false,
                'message' => 'Please enter the exact 4-digit number plate code.'
            ];
        }

        $cleanPhone = trim($callerPhone);
        if (empty($cleanPhone) || strlen(preg_replace('/\D/', '', $cleanPhone)) < 7) {
            return [
                'success' => false,
                'message' => 'Please enter a valid phone number for call masking.'
            ];
        }

        $scannerCallModel = new ScannerCall();

        // 1. Fetch owner customer_number from database
        $customerNumber = $scannerCallModel->fetchCustomerNumber($scannerCode, $cleanPlate);

        // 2. Obtain DID number from config
        $didNumber = defined('CALL_MASKING_DID') ? CALL_MASKING_DID : '+97148007268';

        // 3. Store phone number and transaction details into database
        $logId = $scannerCallModel->createCallLog([
            'scanner_code' => $scannerCode,
            'plate_last_4' => $cleanPlate,
            'caller_phone' => $cleanPhone,
            'customer_number' => $customerNumber,
            'did_number' => $didNumber,
            'status' => 'initiated',
        ]);

        $cleanDid = preg_replace('/[^\d+]/', '', $didNumber);

        return [
            'success' => true,
            'log_id' => $logId,
            'did_number' => $didNumber,
            'tel_url' => 'tel:' . $cleanDid,
            'customer_number' => $customerNumber,
            'message' => 'Call masking initiated. Connecting to owner...'
        ];
    }
}
