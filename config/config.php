<?php

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

define('DB_HOST', 'localhost');
define('DB_NAME', 'helpdesk_system');
define('DB_USER', 'root');
define('DB_PASS', '');


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

define('MAIL_HOST', 'smtp-relay.brevo.com');

define('MAIL_PORT', 587);

define('MAIL_USERNAME', 'ac4d3e001@smtp-brevo.com');

define('MAIL_PASSWORD', 'bskjXrZQ1olM54e');

define('MAIL_FROM_EMAIL', 'samtech-verification@outlook.com');

define('MAIL_FROM_NAME', 'Samtech Helpdesk');


/*
|--------------------------------------------------------------------------
| Security Configuration
|--------------------------------------------------------------------------
*/

define('LOGIN_MAX_ATTEMPTS', 5);

define('LOGIN_LOCKOUT_MINUTES', 15);

define('OTP_EXPIRY_MINUTES', 10);

define('PASSWORD_MIN_LENGTH', 8);