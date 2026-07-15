<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fullName = $_SESSION['auth_user_name'] ?? 'User';
$role = $_SESSION['auth_user_role'] ?? '';
$isAdminAgent = (int)($_SESSION['is_admin_agent'] ?? 0) === 1;

$roleLabel = match ($role) {
    'admin' => 'Administrator',
    'agent' => $isAdminAgent ? 'Admin Agent' : 'Support Agent',
    'user' => (int)($_SESSION['is_organization_admin'] ?? 0) === 1
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
        substr($nameParts[0], 0, 1)
    );
}

if (!empty($nameParts[1])) {
    $initials .= strtoupper(
        substr($nameParts[1], 0, 1)
    );
}

if ($initials === '') {
    $initials = 'U';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta
        name="theme-color"
        content="#111827"
    >

    <title>
        <?= htmlspecialchars(APP_NAME ?? 'Samtech Helpdesk'); ?>
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- Samtech Design System -->
    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/theme.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/layout.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/sidebar.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/components.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/tables.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/forms.css"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/responsive.css"
    >

    <!-- Keep existing application styles temporarily -->
    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/assets/css/app.css"
    >

    <!-- Favicon -->
    <link
        rel="icon"
        type="image/png"
        href="<?= BASE_URL ?>/assets/images/samtech-icon.png"
    >

    <link
        rel="shortcut icon"
        type="image/png"
        href="<?= BASE_URL ?>/assets/images/samtech-icon.png"
    >

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <!-- Mobile/Tablet sidebar overlay -->
    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        aria-hidden="true"
    ></div>

    <?php require_once ROOT_PATH . "/app/Views/layouts/sidebar.php"; ?>

    <div
        class="app-shell"
        id="appShell"
    >

        <main
            class="main-content"
            id="mainContent"
        >

            <nav class="top-navbar">

                <div class="top-navbar-left">

                    <!-- Visible on mobile and tablet -->
                    <button
                        type="button"
                        class="sidebar-toggle-btn"
                        id="sidebarToggle"
                        aria-label="Open navigation menu"
                        aria-controls="appSidebar"
                        aria-expanded="false"
                    >
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="top-navbar-brand">

                        <img
                            src="<?= BASE_URL ?>/assets/images/samtech-icon.png"
                            alt=""
                            class="top-navbar-icon"
                        >

                        <div class="top-navbar-brand-text">

                            <div class="top-navbar-title">
                                Samtech Helpdesk
                            </div>

                            <div class="top-navbar-subtitle">
                                Support Management System
                            </div>

                        </div>

                    </div>

                </div>

                <?php if (isset($_SESSION['auth_user_id'])): ?>

                    <div class="top-navbar-actions">

                        <!-- Ready for future notification module -->
                        <button
                            type="button"
                            class="topbar-icon-btn"
                            title="Notifications"
                            aria-label="Notifications"
                        >
                            <i class="bi bi-bell"></i>

                            <span
                                class="notification-indicator"
                                aria-hidden="true"
                            ></span>
                        </button>

                        <div class="dropdown">

                            <button
                                class="user-card-btn dropdown-toggle"
                                type="button"
                                id="profileDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >

                                <div class="avatar-circle">
                                    <?= htmlspecialchars($initials); ?>
                                </div>

                                <div class="user-info">

                                    <div class="user-name">
                                        <?= htmlspecialchars($fullName); ?>
                                    </div>

                                    <div class="user-role">
                                        <?= htmlspecialchars($roleLabel); ?>
                                    </div>

                                </div>

                                <i class="bi bi-chevron-down user-menu-arrow"></i>

                            </button>

                            <ul
                                class="dropdown-menu dropdown-menu-end profile-dropdown-menu"
                                aria-labelledby="profileDropdown"
                            >

                                <li class="profile-dropdown-header">

                                    <div class="avatar-circle avatar-circle-large">
                                        <?= htmlspecialchars($initials); ?>
                                    </div>

                                    <div class="profile-dropdown-user">

                                        <div class="profile-dropdown-name">
                                            <?= htmlspecialchars($fullName); ?>
                                        </div>

                                        <div class="profile-dropdown-role">
                                            <?= htmlspecialchars($roleLabel); ?>
                                        </div>

                                    </div>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="<?= BASE_URL ?>/profile"
                                    >
                                        <i class="bi bi-person-circle"></i>

                                        <span>
                                            View Profile
                                        </span>
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
                                        data-bs-target="#logoutModal"
                                    >
                                        <i class="bi bi-box-arrow-right"></i>

                                        <span>
                                            Logout
                                        </span>
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
                    aria-hidden="true"
                >

                    <div class="modal-dialog modal-dialog-centered modal-sm">

                        <div class="modal-content logout-modal-content">

                            <div class="modal-header border-0">

                                <h5
                                    class="modal-title fw-bold"
                                    id="logoutModalLabel"
                                >
                                    Confirm Logout
                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>

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

                            <div class="modal-footer border-0 logout-modal-actions">

                                <button
                                    type="button"
                                    class="btn btn-light flex-fill"
                                    data-bs-dismiss="modal"
                                >
                                    Cancel
                                </button>

                                <a
                                    href="<?= BASE_URL ?>/logout"
                                    class="btn btn-danger flex-fill"
                                >
                                    Logout
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>