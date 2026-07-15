<?php

use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    /*
    |--------------------------------------------------------------------------
    | Build PHPMailer
    |--------------------------------------------------------------------------
    |
    | security = OTP, password reset and MFA recovery emails
    | ticket   = ticket creation, replies and status notifications
    |
    */

    private static function baseMailer(string $type = 'security'): PHPMailer
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();

        /*
        |--------------------------------------------------------------------------
        | SMTP Debugging
        |--------------------------------------------------------------------------
        |
        | MAIL_DEBUG=0 for production
        | MAIL_DEBUG=2 temporarily for troubleshooting
        |
        | Debug output is written only to the PHP error log.
        |
        */

        $mail->SMTPDebug = defined('MAIL_DEBUG')
            ? (int) MAIL_DEBUG
            : 0;

        $mail->Debugoutput = static function (
            string $message,
            int $level
        ): void {
            error_log(
                "SMTP Debug Level {$level}: {$message}"
            );
        };

        /*
        |--------------------------------------------------------------------------
        | Namecheap Private Email SMTP
        |--------------------------------------------------------------------------
        */

        $mail->Host = MAIL_HOST;
        $mail->Port = (int) MAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        /*
        |--------------------------------------------------------------------------
        | Select Mailbox
        |--------------------------------------------------------------------------
        */

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
        | Message Configuration
        |--------------------------------------------------------------------------
        */

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->isHTML(true);

        $mail->setFrom(
            $fromEmail,
            $fromName
        );

        /*
        |--------------------------------------------------------------------------
        | Reply-To
        |--------------------------------------------------------------------------
        |
        | Ticket notification replies go to the helpdesk mailbox.
        |
        */

        if ($type === 'ticket') {
            $mail->addReplyTo(
                TICKET_FROM_EMAIL,
                TICKET_FROM_NAME
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hide PHPMailer Version
        |--------------------------------------------------------------------------
        */

        $mail->XMailer = 'Samtech Mail System';

        return $mail;
    }

    /*
    |--------------------------------------------------------------------------
    | Generic Send Method
    |--------------------------------------------------------------------------
    */

    public static function send(
        string $to,
        string $subject,
        string $body,
        string $type = 'security'
    ): bool {
        $mail = null;

        try {
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                error_log(
                    "MailService: Invalid recipient email: {$to}"
                );

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
                    "MailService: Email sent successfully | " .
                        "Recipient: {$to} | Subject: {$subject}"
                );
            }

            return $sent;
        } catch (Throwable $e) {
            $mailerError = '';

            if ($mail instanceof PHPMailer) {
                $mailerError = $mail->ErrorInfo;
            }

            error_log(
                "MailService Error | " .
                    "Recipient: {$to} | " .
                    "Subject: {$subject} | " .
                    "PHPMailer: {$mailerError} | " .
                    "Exception: {$e->getMessage()}"
            );

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Login OTP
    |--------------------------------------------------------------------------
    */

    public static function sendLoginOtp(
        string $to,
        $otp
    ): bool {
        $subject =
            "Your Samtech Helpdesk Login Verification Code";

        $body = self::otpTemplate(
            "Login Verification",
            "Use this verification code to securely complete your login.",
            $otp,
            "login"
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot Password OTP
    |--------------------------------------------------------------------------
    */

    public static function sendForgotPasswordOtp(
        string $to,
        $otp
    ): bool {
        $subject =
            "Reset Your Samtech Helpdesk Password";

        $body = self::otpTemplate(
            "Password Reset",
            "Use this verification code to continue resetting your password.",
            $otp,
            "password"
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MFA Recovery OTP
    |--------------------------------------------------------------------------
    */

    public static function sendMfaRecoveryOtp(
        string $to,
        $otp
    ): bool {
        $subject =
            "Samtech Helpdesk Authenticator Recovery Code";

        $body = self::otpTemplate(
            "Authenticator Recovery",
            "Use this verification code to recover access to your authenticator setup.",
            $otp,
            "recovery"
        );

        return self::send(
            $to,
            $subject,
            $body,
            'security'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket Notification
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | HTML to Plain Text
    |--------------------------------------------------------------------------
    */

    private static function htmlToPlainText(
        string $html
    ): string {
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

        $html = preg_replace(
            '/<\/div>/i',
            "\n",
            $html
        );

        $html = preg_replace(
            '/<\/td>/i',
            " ",
            $html
        );

        $text = strip_tags($html);

        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace(
            "/[ \t]+/",
            " ",
            $text
        );

        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );

        return trim($text);
    }

    /*
    |--------------------------------------------------------------------------
    | OTP Email Template
    |--------------------------------------------------------------------------
    */

    private static function otpTemplate(
        string $title,
        string $message,
        $otp,
        string $type = 'login'
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

        $safeBaseUrl = htmlspecialchars(
            BASE_URL,
            ENT_QUOTES,
            'UTF-8'
        );

        $preheader = match ($type) {
            'password' =>
            "Use this code to reset your Samtech Helpdesk password.",
            'recovery' =>
            "Use this code to recover your authenticator access.",
            default =>
            "Use this code to securely complete your login."
        };

        $safePreheader = htmlspecialchars(
            $preheader,
            ENT_QUOTES,
            'UTF-8'
        );

        return '
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >

            <title>' . $safeTitle . '</title>
        </head>

        <body
            style="
                margin:0;
                padding:0;
                background:#f3f6f1;
                font-family:Arial,Helvetica,sans-serif;
                color:#111827;
            "
        >

            <div
                style="
                    display:none;
                    max-height:0;
                    overflow:hidden;
                    opacity:0;
                    color:transparent;
                "
            >
                ' . $safePreheader . '
            </div>

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    width:100%;
                    background:#f3f6f1;
                    padding:28px 12px;
                "
            >

                <tr>
                    <td align="center">

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                width:100%;
                                max-width:580px;
                                background:#ffffff;
                                border:1px solid #e5e7eb;
                                border-radius:18px;
                                overflow:hidden;
                                box-shadow:0 12px 34px rgba(15,23,42,.08);
                            "
                        >

                            <tr>
                                <td
                                    style="
                                        height:6px;
                                        background:#b1e96f;
                                        font-size:0;
                                        line-height:0;
                                    "
                                >
                                    &nbsp;
                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:28px 28px 20px;
                                        background:#111827;
                                    "
                                >

                                    <img
    src="' . htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') . '/assets/images/samtech-icon.png"
    alt="Samtech"
    width="52"
    height="52"
    style="
        display:block;
        width:52px;
        height:52px;
        border-radius:12px;
    "
>


                                    <div
                                        style="
                                            color:#ffffff;
                                            font-size:23px;
                                            line-height:30px;
                                            font-weight:800;
                                        "
                                    >
                                        Samtech Verification
                                    </div>

                                    <div
                                        style="
                                            color:#cbd5e1;
                                            font-size:13px;
                                            line-height:20px;
                                            margin-top:5px;
                                        "
                                    >
                                        Secure access to Samtech Helpdesk
                                    </div>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:34px 30px 20px;
                                    "
                                >

                                    <h1
                                        style="
                                            margin:0;
                                            color:#111827;
                                            font-size:24px;
                                            line-height:32px;
                                            font-weight:800;
                                        "
                                    >
                                        ' . $safeTitle . '
                                    </h1>

                                    <p
                                        style="
                                            margin:12px auto 0;
                                            max-width:430px;
                                            color:#4b5563;
                                            font-size:15px;
                                            line-height:24px;
                                        "
                                    >
                                        ' . $safeMessage . '
                                    </p>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:4px 30px 22px;
                                    "
                                >

                                    <table
                                        role="presentation"
                                        cellspacing="0"
                                        cellpadding="0"
                                        border="0"
                                        style="
                                            margin:0 auto;
                                        "
                                    >

                                        <tr>
                                            <td
                                                align="center"
                                                style="
                                                    background:#effbdc;
                                                    border:1px solid #c9ee9c;
                                                    border-radius:14px;
                                                    padding:18px 26px;
                                                    color:#111827;
                                                    font-size:32px;
                                                    line-height:38px;
                                                    font-weight:900;
                                                    letter-spacing:8px;
                                                "
                                            >
                                                ' . $safeOtp . '
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:0 30px 30px;
                                    "
                                >

                                    <table
                                        role="presentation"
                                        width="100%"
                                        cellspacing="0"
                                        cellpadding="0"
                                        border="0"
                                        style="
                                            width:100%;
                                            background:#f8fafc;
                                            border:1px solid #e5e7eb;
                                            border-radius:12px;
                                        "
                                    >

                                        <tr>
                                            <td
                                                style="
                                                    padding:16px 18px;
                                                    color:#475569;
                                                    font-size:13px;
                                                    line-height:21px;
                                                    text-align:center;
                                                "
                                            >
                                                This verification code expires in
                                                <strong style="color:#111827;">
                                                    ' . $expiryMinutes . ' minutes
                                                </strong>.
                                                <br>
                                                Samtech representatives will never ask
                                                you to share this code.
                                            </td>
                                        </tr>

                                    </table>

                                    <p
                                        style="
                                            margin:20px 0 0;
                                            color:#64748b;
                                            font-size:12px;
                                            line-height:20px;
                                        "
                                    >
                                        If you did not request this verification code,
                                        no action is required.
                                    </p>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:19px 26px;
                                        background:#f8fafc;
                                        border-top:1px solid #e5e7eb;
                                    "
                                >

                                    <div
                                        style="
                                            color:#475569;
                                            font-size:12px;
                                            line-height:20px;
                                        "
                                    >
                                        Samtech Helpdesk Security Notification
                                    </div>

                                    <div
                                        style="
                                            margin-top:4px;
                                            color:#94a3b8;
                                            font-size:11px;
                                            line-height:18px;
                                        "
                                    >
                                        © ' . date('Y') . '
                                        Samtech Solutions. All rights reserved.
                                    </div>

                                    <div
                                        style="
                                            margin-top:6px;
                                            font-size:11px;
                                            line-height:18px;
                                        "
                                    >
                                        <a
                                            href="' . $safeBaseUrl . '"
                                            style="
                                                color:#4f772d;
                                                text-decoration:none;
                                            "
                                        >
                                            Open Samtech Helpdesk
                                        </a>
                                    </div>

                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>

            </table>

        </body>
        </html>';
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket Email Template
    |--------------------------------------------------------------------------
    */

    private static function ticketTemplate(
        string $title,
        string $message
    ): string {
        $safeTitle = htmlspecialchars(
            $title,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeBaseUrl = htmlspecialchars(
            BASE_URL,
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
            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >

            <title>' . $safeTitle . '</title>
        </head>

        <body
            style="
                margin:0;
                padding:0;
                background:#f3f6f1;
                font-family:Arial,Helvetica,sans-serif;
                color:#111827;
            "
        >

            <div
                style="
                    display:none;
                    max-height:0;
                    overflow:hidden;
                    opacity:0;
                    color:transparent;
                "
            >
                A new update is available in Samtech Helpdesk.
            </div>

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    width:100%;
                    background:#f3f6f1;
                    padding:28px 12px;
                "
            >

                <tr>
                    <td align="center">

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                width:100%;
                                max-width:640px;
                                background:#ffffff;
                                border:1px solid #e5e7eb;
                                border-radius:18px;
                                overflow:hidden;
                                box-shadow:0 12px 34px rgba(15,23,42,.08);
                            "
                        >

                            <tr>
                                <td
                                    style="
                                        height:6px;
                                        background:#b1e96f;
                                        font-size:0;
                                        line-height:0;
                                    "
                                >
                                    &nbsp;
                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:26px 30px;
                                        background:#111827;
                                    "
                                >

                                    <table
                                        role="presentation"
                                        width="100%"
                                        cellspacing="0"
                                        cellpadding="0"
                                        border="0"
                                    >

                                        <tr>
                                            <td
                                                width="58"
                                                valign="middle"
                                            >

                                                <img
    src="' . htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') . '/assets/images/samtech-icon.png"
    alt="Samtech"
    width="52"
    height="52"
    style="
        display:block;
        width:52px;
        height:52px;
        border-radius:12px;
    "
>

                                            </td>

                                            <td
                                                valign="middle"
                                                style="
                                                    padding-left:12px;
                                                "
                                            >

                                                <div
                                                    style="
                                                        color:#ffffff;
                                                        font-size:22px;
                                                        line-height:29px;
                                                        font-weight:800;
                                                    "
                                                >
                                                    Samtech Helpdesk
                                                </div>

                                                <div
                                                    style="
                                                        color:#cbd5e1;
                                                        font-size:12px;
                                                        line-height:19px;
                                                        margin-top:3px;
                                                    "
                                                >
                                                    Support ticket notification
                                                </div>

                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:32px 30px 20px;
                                    "
                                >

                                    <div
                                        style="
                                            display:inline-block;
                                            padding:6px 11px;
                                            border-radius:999px;
                                            background:#effbdc;
                                            color:#4f772d;
                                            font-size:11px;
                                            line-height:16px;
                                            font-weight:800;
                                            text-transform:uppercase;
                                            letter-spacing:.06em;
                                        "
                                    >
                                        Ticket Notification
                                    </div>

                                    <h1
                                        style="
                                            margin:14px 0 0;
                                            color:#111827;
                                            font-size:23px;
                                            line-height:32px;
                                            font-weight:800;
                                        "
                                    >
                                        ' . $safeTitle . '
                                    </h1>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:0 30px 26px;
                                    "
                                >

                                    <table
                                        role="presentation"
                                        width="100%"
                                        cellspacing="0"
                                        cellpadding="0"
                                        border="0"
                                        style="
                                            width:100%;
                                            background:#f8fafc;
                                            border:1px solid #e5e7eb;
                                            border-radius:14px;
                                        "
                                    >

                                        <tr>
                                            <td
                                                style="
                                                    padding:22px;
                                                    color:#334155;
                                                    font-size:14px;
                                                    line-height:23px;
                                                    word-break:break-word;
                                                "
                                            >
                                                ' . $safeMessage . '
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:0 30px 34px;
                                    "
                                >

                                    <table
                                        role="presentation"
                                        cellspacing="0"
                                        cellpadding="0"
                                        border="0"
                                    >

                                        <tr>
                                            <td
                                                align="center"
                                                bgcolor="#b1e96f"
                                                style="
                                                    border-radius:10px;
                                                "
                                            >

                                                <a
                                                    href="' . $safeBaseUrl . '"
                                                    style="
                                                        display:inline-block;
                                                        padding:13px 24px;
                                                        color:#111827;
                                                        font-size:14px;
                                                        line-height:20px;
                                                        font-weight:800;
                                                        text-decoration:none;
                                                        border-radius:10px;
                                                    "
                                                >
                                                    Open Samtech Helpdesk
                                                </a>

                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding:19px 26px;
                                        background:#f8fafc;
                                        border-top:1px solid #e5e7eb;
                                    "
                                >

                                    <div
                                        style="
                                            color:#475569;
                                            font-size:12px;
                                            line-height:20px;
                                        "
                                    >
                                        This is an automated notification from
                                        Samtech Helpdesk.
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            color:#94a3b8;
                                            font-size:11px;
                                            line-height:18px;
                                        "
                                    >
                                        Reply to this email to contact the
                                        Samtech Helpdesk mailbox.
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            color:#94a3b8;
                                            font-size:11px;
                                            line-height:18px;
                                        "
                                    >
                                        © ' . date('Y') . '
                                        Samtech Solutions. All rights reserved.
                                    </div>

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
