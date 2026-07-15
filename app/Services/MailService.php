<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private static function baseMailer(string $type = 'security'): PHPMailer
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP: $str");
        };

        $mail->Debugoutput = static function ($message, $level) {
            error_log("SMTP Debug Level {$level}: {$message}");
        };

        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;

        if ($type === 'ticket') {
            $mail->Username = TICKET_MAIL_USERNAME;
            $mail->Password = TICKET_MAIL_PASSWORD;

            $fromEmail = TICKET_FROM_EMAIL;
            $fromName = TICKET_FROM_NAME;
        } else {
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;

            $fromEmail = MAIL_FROM_EMAIL;
            $fromName = MAIL_FROM_NAME;
        }

        /*
        |--------------------------------------------------------------------------
        | Namecheap Private Email SMTP
        |--------------------------------------------------------------------------
        |
        | Host: mail.privateemail.com
        | Port: 465
        | Encryption: SSL / SMTPS
        |
        */

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = (int) MAIL_PORT;

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = 'base64';

        $mail->setFrom($fromEmail, $fromName);

        /*
        |--------------------------------------------------------------------------
        | Reply-To
        |--------------------------------------------------------------------------
        */

        if ($type === 'ticket') {
            $mail->addReplyTo(
                TICKET_FROM_EMAIL,
                TICKET_FROM_NAME
            );
        }

        $mail->isHTML(true);

        return $mail;
    }

    public static function send(
        string $to,
        string $subject,
        string $body,
        string $type = 'security'
    ): bool {
        $mail = null;

        try {
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                error_log("MailService: Invalid recipient email: {$to}");
                return false;
            }

            $mail = self::baseMailer($type);

            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = self::htmlToPlainText($body);

            $sent = $mail->send();

            if ($sent) {
                error_log(
                    "MailService: Email sent successfully to {$to}. Subject: {$subject}"
                );
            }

            return $sent;
        } catch (Throwable $e) {
            $mailerError = '';

            if ($mail instanceof PHPMailer) {
                $mailerError = $mail->ErrorInfo;
            }

            error_log(
                "MailService Error | Recipient: {$to} | Subject: {$subject} | " .
                    "PHPMailer: {$mailerError} | Exception: {$e->getMessage()}"
            );

            echo "<pre>";
            echo "PHPMailer Error:\n";
            echo $mail->ErrorInfo . "\n\n";

            echo "Exception:\n";
            echo $e->getMessage();
            echo "</pre>";
            exit;
        }
    }

    public static function sendLoginOtp(string $to, $otp): bool
    {
        $subject = "Your Samtech Helpdesk Login Verification Code";

        $body = self::otpTemplate(
            "Login Verification",
            "Use the verification code below to complete your login.",
            $otp
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    public static function sendForgotPasswordOtp(string $to, $otp): bool
    {
        $subject = "Reset Your Samtech Helpdesk Password";

        $body = self::otpTemplate(
            "Password Reset Verification",
            "Use the verification code below to reset your password.",
            $otp
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    public static function sendMfaRecoveryOtp(string $to, $otp): bool
    {
        $subject = "Samtech Helpdesk Authenticator Recovery Code";

        $body = self::otpTemplate(
            "Authenticator Recovery",
            "Use the verification code below to recover your authenticator setup.",
            $otp
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    public static function sendTicketMail(
        string $to,
        string $subject,
        string $message
    ): bool {
        $body = self::ticketTemplate(
            $subject,
            $message
        );

        return self::send(
            $to,
            $subject,
            $body,
            'ticket'
        );
    }

    private static function htmlToPlainText(string $html): string
    {
        $html = preg_replace(
            '/<\s*br\s*\/?>/i',
            "\n",
            $html
        );

        $html = preg_replace(
            '/<\/p>/i',
            "\n\n",
            $html
        );

        $text = strip_tags($html);
        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace("/[ \t]+/", " ", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    private static function otpTemplate(
        string $title,
        string $message,
        $otp
    ): string {
        $safeTitle = htmlspecialchars(
            $title,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeMessage = htmlspecialchars(
            $message,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOtp = htmlspecialchars(
            (string) $otp,
            ENT_QUOTES,
            'UTF-8'
        );

        $expiryMinutes = defined('OTP_EXPIRY_MINUTES')
            ? (int) OTP_EXPIRY_MINUTES
            : 5;

        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $safeTitle . '</title>
        </head>

        <body style="margin:0;padding:0;background:#f4f7f4;font-family:Arial,sans-serif;color:#111827;">

            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background:#f4f7f4;padding:24px 12px;">

                <tr>
                    <td align="center">

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="max-width:560px;background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">

                            <tr>
                                <td style="background:#111827;padding:24px;text-align:center;">
                                    <div style="font-size:22px;font-weight:800;color:#ffffff;">
                                        Samtech Helpdesk
                                    </div>

                                    <div style="font-size:12px;color:#d1d5db;margin-top:6px;">
                                        Secure Account Verification
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:32px 28px;text-align:center;">

                                    <h2 style="margin:0 0 12px;font-size:22px;color:#111827;">
                                        ' . $safeTitle . '
                                    </h2>

                                    <p style="margin:0 0 26px;color:#4b5563;font-size:15px;line-height:1.6;">
                                        ' . $safeMessage . '
                                    </p>

                                    <div style="display:inline-block;background:#b1e96f;color:#111827;
                                                font-size:30px;font-weight:800;letter-spacing:8px;
                                                padding:16px 24px;border-radius:12px;">
                                        ' . $safeOtp . '
                                    </div>

                                    <p style="margin:24px 0 0;color:#6b7280;font-size:14px;">
                                        This code expires in ' . $expiryMinutes . ' minutes.
                                    </p>

                                    <p style="margin:12px 0 0;color:#991b1b;font-size:13px;">
                                        Never share this verification code with anyone.
                                    </p>

                                    <p style="margin:24px 0 0;color:#6b7280;font-size:12px;line-height:1.6;">
                                        If you did not request this code, you can safely ignore this email.
                                    </p>

                                </td>
                            </tr>

                            <tr>
                                <td style="background:#f9fafb;padding:16px;text-align:center;
                                           color:#6b7280;font-size:12px;">
                                    © ' . date('Y') . ' Samtech Solutions. All rights reserved.
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>

            </table>

        </body>
        </html>';
    }

    private static function ticketTemplate(
        string $title,
        string $message
    ): string {
        $safeTitle = htmlspecialchars(
            $title,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeMessage = nl2br(
            htmlspecialchars(
                $message,
                ENT_QUOTES,
                'UTF-8'
            )
        );

        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $safeTitle . '</title>
        </head>

        <body style="margin:0;padding:0;background:#f4f7f4;font-family:Arial,sans-serif;color:#111827;">

            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background:#f4f7f4;padding:24px 12px;">

                <tr>
                    <td align="center">

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;
                                      border-radius:16px;overflow:hidden;">

                            <tr>
                                <td style="background:#111827;padding:24px;text-align:center;">
                                    <div style="font-size:22px;font-weight:800;color:#ffffff;">
                                        Samtech Helpdesk
                                    </div>

                                    <div style="font-size:12px;color:#d1d5db;margin-top:6px;">
                                        Ticket Notification
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:32px 28px;">

                                    <h2 style="margin:0 0 16px;font-size:21px;color:#111827;">
                                        ' . $safeTitle . '
                                    </h2>

                                    <div style="color:#4b5563;font-size:15px;line-height:1.7;">
                                        ' . $safeMessage . '
                                    </div>

                                    <div style="margin-top:28px;text-align:center;">
                                        <a href="' . htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') . '"
                                           style="background:#b1e96f;color:#111827;text-decoration:none;
                                                  padding:12px 22px;border-radius:10px;font-weight:700;
                                                  display:inline-block;">
                                            Open Samtech Helpdesk
                                        </a>
                                    </div>

                                </td>
                            </tr>

                            <tr>
                                <td style="background:#f9fafb;padding:16px;text-align:center;
                                           color:#6b7280;font-size:12px;">
                                    This is an automated notification from Samtech Helpdesk.<br>
                                    © ' . date('Y') . ' Samtech Solutions. All rights reserved.
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>

            </table>

        </body>
        </html>';
    }
}
