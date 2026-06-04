<?php

require_once "../vendor/autoload.php";

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

$router = new Router();

require_once "../routes/web.php";

$router->dispatch();