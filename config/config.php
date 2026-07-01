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

define('SESSION_TIMEOUT', 2700); // 45 Minutes


/*
|--------------------------------------------------------------------------
| Security Configuration
|--------------------------------------------------------------------------
*/

define('LOGIN_MAX_ATTEMPTS', 5);

define('LOGIN_LOCKOUT_MINUTES', 15);

define('OTP_EXPIRY_MINUTES', 5);

define('PASSWORD_MIN_LENGTH', 8);
