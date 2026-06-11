<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private static function baseMailer($type = 'security')
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();

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

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom($fromEmail, $fromName);

        $mail->isHTML(true);

        return $mail;
    }

    public static function send($to, $subject, $body, $type = 'security')
    {
        try {
            $mail = self::baseMailer($type);

            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            return $mail->send();
        } catch (Exception $e) {
            error_log("Mail Error: " . $mail->ErrorInfo . " | " . $e->getMessage());
            return false;
        }
    }

    public static function sendLoginOtp($to, $otp)
    {
        $subject = "Your Samtech Helpdesk Login OTP";

        $body = self::otpTemplate(
            "Login Verification",
            "Use this OTP to complete your login.",
            $otp
        );

        return self::send($to, $subject, $body, 'security');
    }

    public static function sendForgotPasswordOtp($to, $otp)
    {
        $subject = "Reset Your Samtech Helpdesk Password";

        $body = self::otpTemplate(
            "Password Reset Verification",
            "Use this OTP to reset your password.",
            $otp
        );

        return self::send($to, $subject, $body, 'security');
    }

    public static function sendMfaRecoveryOtp($to, $otp)
    {
        $subject = "Samtech Helpdesk MFA Recovery OTP";

        $body = self::otpTemplate(
            "MFA Recovery Verification",
            "Use this OTP to recover your authenticator setup.",
            $otp
        );

        return self::send($to, $subject, $body, 'security');
    }

    public static function sendTicketMail($to, $subject, $message)
    {
        $body = self::ticketTemplate($subject, $message);

        return self::send($to, $subject, $body, 'ticket');
    }

    private static function otpTemplate($title, $message, $otp)
    {
        return '
        <div style="margin:0;padding:0;background:#f4f7f4;font-family:Arial,sans-serif;">
            <div style="max-width:560px;margin:30px auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                
                <div style="background:#111827;padding:22px;text-align:center;">
                    <h2 style="margin:0;color:#ffffff;font-size:22px;">
                        Samtech Helpdesk
                    </h2>
                </div>

                <div style="padding:30px;text-align:center;">
                    <h3 style="margin:0 0 12px;color:#111827;">
                        ' . htmlspecialchars($title) . '
                    </h3>

                    <p style="color:#4b5563;font-size:15px;margin:0 0 24px;">
                        ' . htmlspecialchars($message) . '
                    </p>

                    <div style="display:inline-block;background:#b1e96f;color:#111827;font-size:32px;font-weight:800;letter-spacing:8px;padding:14px 24px;border-radius:12px;">
                        ' . htmlspecialchars($otp) . '
                    </div>

                    <p style="color:#6b7280;font-size:14px;margin-top:24px;">
                        This OTP is valid for 10 minutes.
                    </p>

                    <p style="color:#991b1b;font-size:13px;margin-top:10px;">
                        Do not share this code with anyone.
                    </p>
                </div>

                <div style="background:#f9fafb;padding:16px;text-align:center;color:#6b7280;font-size:12px;">
                    © ' . date('Y') . ' Samtech Solutions. All rights reserved.
                </div>

            </div>
        </div>';
    }

    private static function ticketTemplate($title, $message)
    {
        return '
        <div style="margin:0;padding:0;background:#f4f7f4;font-family:Arial,sans-serif;">
            <div style="max-width:560px;margin:30px auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                
                <div style="background:#111827;padding:22px;text-align:center;">
                    <h2 style="margin:0;color:#ffffff;font-size:22px;">
                        Samtech Helpdesk
                    </h2>
                </div>

                <div style="padding:30px;">
                    <h3 style="margin:0 0 12px;color:#111827;">
                        ' . htmlspecialchars($title) . '
                    </h3>

                    <p style="color:#4b5563;font-size:15px;line-height:1.6;">
                        ' . nl2br(htmlspecialchars($message)) . '
                    </p>

                    <div style="margin-top:24px;text-align:center;">
                        <a href="' . BASE_URL . '"
                           style="background:#b1e96f;color:#111827;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:700;display:inline-block;">
                            Open Helpdesk
                        </a>
                    </div>
                </div>

                <div style="background:#f9fafb;padding:16px;text-align:center;color:#6b7280;font-size:12px;">
                    © ' . date('Y') . ' Samtech Solutions. All rights reserved.
                </div>

            </div>
        </div>';
    }
}
