<?php

date_default_timezone_set($env['APP_TIMEZONE'] ?? 'Asia/Dubai');

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

    $env[trim($key)] = trim($value);
}

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
| Mail
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', $env['MAIL_HOST'] ?? '');
define('MAIL_PORT', $env['MAIL_PORT'] ?? '');
define('MAIL_USERNAME', $env['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $env['MAIL_PASSWORD'] ?? '');
define('MAIL_ENCRYPTION', $env['MAIL_ENCRYPTION'] ?? 'ssl');

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