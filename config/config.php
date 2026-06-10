<?php

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

define('DB_HOST', 'localhost');
define('DB_NAME', 'samvrudd_helpdesk_system');
define('DB_USER', 'samvrudd_helpdesk_developer');
define('DB_PASS', '^huYovuXmO)R)@r7');


/*
|--------------------------------------------------------------------------
| Application Configuration
|--------------------------------------------------------------------------
*/

define('ROOT_PATH', dirname(__DIR__));

define('BASE_URL', 'https://support.samtech.ae');

define('APP_NAME', 'Samtech Helpdesk');

define('SESSION_TIMEOUT', 1800); // 30 Minutes


/*
|--------------------------------------------------------------------------
| Mail Configuration
|--------------------------------------------------------------------------
| Brevo SMTP
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', 'smtp.office365.com');

define('MAIL_PORT', 587);

define('MAIL_USERNAME', 'samtech-verification@outlook.com');

define('MAIL_PASSWORD', 'qtcvdsppefpyylwa');

define('MAIL_ENCRYPTION', 'tls');

define('MAIL_FROM_EMAIL', 'samtech-verification@outlook.com');

define('MAIL_FROM_NAME', 'Samtech Helpdesk Security');


define('TICKET_MAIL_USERNAME', 'samtech-helpdesk@outlook.com');

define('TICKET_MAIL_PASSWORD', 'dtbgdjvvcussmwit');

define('TICKET_FROM_EMAIL', 'samtech-helpdesk@outlook.com');

define('TICKET_FROM_NAME', 'Samtech Helpdesk');



/*
|--------------------------------------------------------------------------
| Security Configuration
|--------------------------------------------------------------------------
*/

define('LOGIN_MAX_ATTEMPTS', 5);

define('LOGIN_LOCKOUT_MINUTES', 15);

define('OTP_EXPIRY_MINUTES', 10);

define('PASSWORD_MIN_LENGTH', 8);