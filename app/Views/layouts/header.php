<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fullName = $_SESSION['auth_user_name'] ?? 'User';
$role = $_SESSION['auth_user_role'] ?? '';
$isAdminAgent = (int)($_SESSION['is_admin_agent'] ?? 0) === 1;
$isOrganizationAdmin =
    (int)($_SESSION['is_organization_admin'] ?? 0) === 1;

$roleLabel = match ($role) {
    'admin' => 'Administrator',
    'agent' => $isAdminAgent
        ? 'Admin Agent'
        : 'Support Agent',
    'user' => $isOrganizationAdmin
        ? 'Organization Admin'
        : 'User',
    default => 'User'
};

$nameParts = preg_split(
    '/\s+/',
    trim($fullName),
    -1,
    PREG_SPLIT_NO_EMPTY
);

$initials = '';

if (!empty($nameParts[0])) {
    $initials .= strtoupper(
        mb_substr($nameParts[0], 0, 1)
    );
}

if (!empty($nameParts[1])) {
    $initials .= strtoupper(
        mb_substr($nameParts[1], 0, 1)
    );
}

if ($initials === '') {
    $initials = 'U';
}

/*
|--------------------------------------------------------------------------
| CSS Cache Busting
|--------------------------------------------------------------------------
*/

function assetVersion(string $relativePath): string
{
    $filePath =
        ROOT_PATH .
        '/public/' .
        ltrim($relativePath, '/');

    if (file_exists($filePath)) {
        return (string)filemtime($filePath);
    }

    return (string)time();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta
        name="theme-color"
        content="#111827">

    <title>
        <?= htmlspecialchars(
            defined('APP_NAME')
                ? APP_NAME
                : 'Samtech Helpdesk'
        ); ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/theme.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/layout.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/sidebar.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/components.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/tables.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/forms.css?v=<?= time(); ?>">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/responsive.css?v=<?= time(); ?>">

    <link
        rel="icon"
        type="image/png"
        href="<?= BASE_URL ?>/assets/images/samtech-icon.png">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</head>

<body>

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        aria-hidden="true"></div>

    <?php
    require_once ROOT_PATH .
        "/app/Views/layouts/sidebar.php";
    ?>

    <div
        class="app-shell"
        id="appShell">

        <main
            class="main-content"
            id="mainContent">

            <nav class="top-navbar">

                <div class="top-navbar-left">

                    <button
                        type="button"
                        class="sidebar-toggle-btn"
                        id="sidebarToggle"
                        aria-label="Open navigation menu"
                        aria-controls="appSidebar"
                        aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>

                    <span class="top-navbar-title-brand">
                        Samtech Helpdesk
                    </span>

                    <div class="top-navbar-search">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search Tickets, Clients..." class="form-control" id="globalSearchInput">
                    </div>

                </div>

                <?php if (isset($_SESSION['auth_user_id'])): ?>

                    <?php
                    require_once ROOT_PATH . "/app/Services/NotificationService.php";
                    $notifData = NotificationService::getHeaderNotifications();
                    $headerNotifications = $notifData['notifications'];
                    $unreadCount = $notifData['unreadCount'];
                    ?>

                    <div class="top-navbar-actions">

                        <div class="dropdown me-1">

                            <button
                                type="button"
                                class="topbar-icon-btn position-relative"
                                id="notificationDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Notifications">

                                <i class="bi bi-bell"></i>

                                <?php if ($unreadCount > 0): ?>

                                    <span class="topbar-badge-count">
                                        <?= $unreadCount > 9 ? '9+' : $unreadCount; ?>
                                    </span>

                                <?php endif; ?>

                            </button>

                            <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 rounded-4" style="width: 360px; max-width: 90vw; margin-top: 10px;" aria-labelledby="notificationDropdown">

                                <div class="p-3 bg-dark text-white rounded-top-4 d-flex align-items-center justify-content-between">

                                    <div class="fw-bold">
                                        <i class="bi bi-bell-fill me-2 text-warning"></i>
                                        Notifications
                                    </div>

                                    <span class="badge bg-primary rounded-pill"><?= count($headerNotifications); ?> recent</span>

                                </div>

                                <div class="notification-list" style="max-height: 380px; overflow-y: auto;">

                                    <?php if (empty($headerNotifications)): ?>

                                        <div class="p-4 text-center text-muted">
                                            <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                            No notifications at this time.
                                        </div>

                                    <?php else: ?>

                                        <?php foreach ($headerNotifications as $notif): ?>

                                            <a href="<?= $notif['link']; ?>" class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-start gap-3 hover-bg-light" style="white-space: normal;">

                                                <div class="rounded-circle p-2 flex-shrink-0" style="background:#f1f5f9;">
                                                    <i class="bi bi-bell text-primary"></i>
                                                </div>

                                                <div class="flex-grow-1">

                                                    <div class="fw-bold text-dark mb-1 small">
                                                        <?= htmlspecialchars($notif['title']); ?>
                                                    </div>

                                                    <div class="text-secondary small mb-1">
                                                        <?= htmlspecialchars($notif['message']); ?>
                                                    </div>

                                                    <div class="text-muted" style="font-size: 11px;">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <?= date('M d, g:i A', strtotime($notif['time'])); ?>
                                                    </div>

                                                </div>

                                            </a>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </div>

                                <div class="p-2 bg-light text-center rounded-bottom-4 border-top">

                                    <a href="<?= ($role === 'user') ? (BASE_URL . '/tickets') : (BASE_URL . '/agent/tickets'); ?>" class="small text-decoration-none fw-bold text-primary">
                                        View All Tickets &rarr;
                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="dropdown">

                            <button
                                class="user-card-btn"
                                type="button"
                                id="profileDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <span class="user-info text-end">
                                    <span class="user-label-sub">My Profile</span>
                                    <span class="user-name"><?= htmlspecialchars($fullName); ?></span>
                                </span>

                                <span class="avatar-circle ms-1">
                                    <?= htmlspecialchars($initials); ?>
                                </span>

                            </button>

                            <ul
                                class="dropdown-menu dropdown-menu-end profile-dropdown-menu"
                                aria-labelledby="profileDropdown">

                                <li class="profile-dropdown-header">

                                    <span class="avatar-circle avatar-circle-large">
                                        <?= htmlspecialchars($initials); ?>
                                    </span>

                                    <span class="profile-dropdown-user">

                                        <span class="profile-dropdown-name">
                                            <?= htmlspecialchars($fullName); ?>
                                        </span>

                                        <span class="profile-dropdown-role">
                                            <?= htmlspecialchars($roleLabel); ?>
                                        </span>

                                    </span>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="<?= BASE_URL ?>/profile">
                                        <i class="bi bi-person-circle"></i>
                                        View Profile
                                    </a>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <button
                                        type="button"
                                        class="dropdown-item text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#logoutModal">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Logout
                                    </button>

                                </li>

                            </ul>

                        </div>

                    </div>

                <?php endif; ?>

            </nav>

            <?php if (isset($_SESSION['auth_user_id'])): ?>

                <div
                    class="modal fade"
                    id="logoutModal"
                    tabindex="-1"
                    aria-labelledby="logoutModalLabel"
                    aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-sm">

                        <div class="modal-content logout-modal-content">

                            <div class="modal-header">

                                <h5
                                    class="modal-title"
                                    id="logoutModalLabel">
                                    Confirm Logout
                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>

                            </div>

                            <div class="modal-body text-center">

                                <div class="logout-modal-icon">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>

                                <h6 class="logout-modal-title">
                                    Sign out of your account?
                                </h6>

                                <p class="logout-modal-description">
                                    You will need to sign in again to access
                                    Samtech Helpdesk.
                                </p>

                            </div>

                            <div class="modal-footer logout-modal-actions">

                                <button
                                    type="button"
                                    class="btn btn-light flex-fill"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <a
                                    href="<?= BASE_URL ?>/logout"
                                    class="btn btn-danger flex-fill">
                                    Logout
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>