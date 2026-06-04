<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public static function send($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        try {

            $mail->isSMTP();

            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;

            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = MAIL_PORT;

            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                MAIL_FROM_EMAIL,
                MAIL_FROM_NAME
            );

            $mail->addAddress($to);

            $mail->isHTML(true);

            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();
        } catch (Exception $e) {

            die('<pre>' .
                'PHPMailer Error: ' . $mail->ErrorInfo . PHP_EOL .
                'Exception: ' . $e->getMessage() .
                '</pre>');
        }
    }
}
