<?php

require_once "./../vendor/autoload.php";

require_once "../app/Services/MailService.php";
require_once "../app/Services/UploadService.php";

require_once "../config/config.php";
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

require_once "../app/Core/Database.php";
require_once "../app/Core/Model.php";
require_once "../app/Core/Router.php";

require_once "../app/Middleware/AuthMiddleware.php";
require_once "../app/Helpers/Csrf.php";
require_once ROOT_PATH . '/app/Helpers/DateTimeHelper.php';

try {
    $router = new Router();

    require_once "../routes/web.php";

    $router->dispatch();
} catch (Throwable $e) {
    error_log("Global exception in index.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());

    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    $_SESSION['error'] = "An unexpected error occurred. Please try again.";

    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (!empty($referer)) {
        header("Location: " . $referer);
        exit;
    }

    $fallback = defined('BASE_URL') ? BASE_URL : '/';
    header("Location: " . $fallback);
    exit;
}