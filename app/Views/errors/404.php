<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['auth_user_role'] ?? '';
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

$dashboardUrl = match ($role) {
    'admin' => $baseUrl . '/admin-dashboard',
    'agent' => $baseUrl . '/agent-dashboard',
    'user' => $baseUrl . '/user-dashboard',
    default => $baseUrl . '/user-login'
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Samtech Helpdesk</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --color-primary-dark: #6cb33f;
            --color-primary-deep: #3b941f;
        }
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            max-width: 520px;
            width: 100%;
            padding: 45px 32px;
            text-align: center;
        }
        .error-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fef3c7;
            color: #d97706;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 20px;
        }
        .btn-primary-custom {
            border: 0;
            border-radius: 10px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary-deep));
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        .btn-primary-custom:hover {
            opacity: 0.92;
            color: #ffffff;
        }
        .btn-light-custom {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 20px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-light-custom:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="mb-4">
            <img src="<?= $baseUrl; ?>/assets/images/samtech-logo.png" alt="Samtech Solutions" style="height: 48px; width: auto;" class="mx-auto">
        </div>

        <div class="error-icon-box">
            <i class="bi bi-compass-fill"></i>
        </div>

        <div class="mb-2">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                HTTP 404 &bull; NOT FOUND
            </span>
        </div>

        <h2 class="fw-bold text-dark mt-3 mb-2" style="font-size: 26px;">
            Page Not Found
        </h2>

        <p class="text-muted small mb-4" style="line-height: 1.6; font-size: 13px;">
            The page or resource you are looking for does not exist, has been removed, or the link may be broken.
        </p>

        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 pt-2">
            <button onclick="window.history.back();" class="btn-light-custom">
                <i class="bi bi-arrow-left"></i> Go Back
            </button>
            <a href="<?= $dashboardUrl; ?>" class="btn-primary-custom">
                <i class="bi bi-house-door-fill"></i> Go to Dashboard
            </a>
        </div>
    </div>

</body>
</html>
