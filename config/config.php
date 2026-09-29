<?php

define('ROOT_PATH', dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Load .env
|--------------------------------------------------------------------------
*/

$envPath = ROOT_PATH . '/.env';

if (!file_exists($envPath)) {
    die('.env file not found.');
}

$env = [];

foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {

    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    if (!str_contains($line, '=')) {
        continue;
    }

    list($key, $value) = explode('=', $line, 2);

    $key = trim($key);
    $value = trim($value);

    if (
        (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
        (str_starts_with($value, "'") && str_ends_with($value, "'"))
    ) {
        $value = substr($value, 1, -1);
    }

    $env[$key] = $value;
}


/*
|--------------------------------------------------------------------------
| Application Timezone
|--------------------------------------------------------------------------
*/

$appTimezone = $env['APP_TIMEZONE'] ?? 'Asia/Dubai';

if (!in_array($appTimezone, timezone_identifiers_list(), true)) {
    $appTimezone = 'Asia/Dubai';
}

date_default_timezone_set($appTimezone);

define('APP_TIMEZONE', $appTimezone);

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/

define('DB_HOST', $env['DB_HOST'] ?? '');
define('DB_NAME', $env['DB_NAME'] ?? '');
define('DB_USER', $env['DB_USER'] ?? '');
define('DB_PASS', $env['DB_PASS'] ?? '');

/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
*/

define('BASE_URL', $env['BASE_URL'] ?? '');

define('APP_NAME', $env['APP_NAME'] ?? 'Samtech Helpdesk');

define('SESSION_TIMEOUT', (int)($env['SESSION_TIMEOUT'] ?? 2700));

/*
|--------------------------------------------------------------------------
| Mail Configuration
|--------------------------------------------------------------------------
|
| Verification / Security Mailbox
|
*/

define('MAIL_HOST', $env['MAIL_HOST'] ?? '');

define('MAIL_PORT', (int)($env['MAIL_PORT'] ?? 587));

define('MAIL_ENCRYPTION', strtolower($env['MAIL_ENCRYPTION'] ?? 'tls'));

define('MAIL_USERNAME', $env['MAIL_USERNAME'] ?? '');

define('MAIL_PASSWORD', $env['MAIL_PASSWORD'] ?? '');

define('MAIL_FROM_EMAIL', !empty($env['MAIL_FROM_EMAIL']) ? $env['MAIL_FROM_EMAIL'] : ($env['MAIL_USERNAME'] ?? ''));

define('MAIL_FROM_NAME', $env['MAIL_FROM_NAME'] ?? 'Samtech Verification');

/*
|--------------------------------------------------------------------------
| Ticket Mailbox
|--------------------------------------------------------------------------
*/

define('TICKET_MAIL_HOST', $env['TICKET_MAIL_HOST'] ?? MAIL_HOST);

define('TICKET_MAIL_PORT', (int)($env['TICKET_MAIL_PORT'] ?? MAIL_PORT));

define('TICKET_MAIL_ENCRYPTION', strtolower($env['TICKET_MAIL_ENCRYPTION'] ?? MAIL_ENCRYPTION));

define('TICKET_MAIL_USERNAME', !empty($env['TICKET_MAIL_USERNAME']) ? $env['TICKET_MAIL_USERNAME'] : MAIL_USERNAME);

define('TICKET_MAIL_PASSWORD', !empty($env['TICKET_MAIL_PASSWORD']) ? $env['TICKET_MAIL_PASSWORD'] : MAIL_PASSWORD);

define('TICKET_FROM_EMAIL', !empty($env['TICKET_FROM_EMAIL']) ? $env['TICKET_FROM_EMAIL'] : MAIL_FROM_EMAIL);

define('TICKET_FROM_NAME', !empty($env['TICKET_FROM_NAME']) ? $env['TICKET_FROM_NAME'] : MAIL_FROM_NAME);

/*
|--------------------------------------------------------------------------
| SMTP Debug
|--------------------------------------------------------------------------
|
| 0 = Production
| 2 = Full SMTP Debug
|
*/

define(
    'MAIL_DEBUG',
    (int)($env['MAIL_DEBUG'] ?? 0)
);

/*
|--------------------------------------------------------------------------
| Security
|--------------------------------------------------------------------------
*/

define(
    'LOGIN_MAX_ATTEMPTS',
    (int)($env['LOGIN_MAX_ATTEMPTS'] ?? 5)
);

define(
    'LOGIN_LOCKOUT_MINUTES',
    (int)($env['LOGIN_LOCKOUT_MINUTES'] ?? 15)
);

define(
    'OTP_EXPIRY_MINUTES',
    (int)($env['OTP_EXPIRY_MINUTES'] ?? 5)
);

define(
    'PASSWORD_MIN_LENGTH',
    (int)($env['PASSWORD_MIN_LENGTH'] ?? 8)
);