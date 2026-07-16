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

function cssAsset(string $filename): string
{
    return BASE_URL .
        '/assets/css/' .
        $filename .
        '?v=' .
        assetVersion('assets/css/' . $filename);
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
        <?= htmlspecialchars(
            defined('APP_NAME')
                ? APP_NAME
                : 'Samtech Helpdesk'
        ); ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('theme.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('layout.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('sidebar.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('components.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('tables.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('forms.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?= cssAsset('responsive.css'); ?>"
    >

    <link
        rel="icon"
        type="image/png"
        href="<?= BASE_URL ?>/assets/images/samtech-icon.png"
    >

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    aria-hidden="true"
></div>

<?php
require_once ROOT_PATH .
    "/app/Views/layouts/sidebar.php";
?>

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
                        alt="Samtech"
                        class="top-navbar-icon"
                        width="40"
                        height="40"
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

                    <button
                        type="button"
                        class="topbar-icon-btn"
                        aria-label="Notifications"
                        title="Notifications"
                    >
                        <i class="bi bi-bell"></i>
                    </button>

                    <div class="dropdown">

                        <button
                            class="user-card-btn dropdown-toggle"
                            type="button"
                            id="profileDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >

                            <span class="avatar-circle">
                                <?= htmlspecialchars($initials); ?>
                            </span>

                            <span class="user-info">

                                <span class="user-name">
                                    <?= htmlspecialchars($fullName); ?>
                                </span>

                                <span class="user-role">
                                    <?= htmlspecialchars($roleLabel); ?>
                                </span>

                            </span>

                            <i class="bi bi-chevron-down user-menu-arrow"></i>

                        </button>

                        <ul
                            class="dropdown-menu dropdown-menu-end profile-dropdown-menu"
                            aria-labelledby="profileDropdown"
                        >

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
                                    href="<?= BASE_URL ?>/profile"
                                >
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
                                    data-bs-target="#logoutModal"
                                >
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
                aria-hidden="true"
            >

                <div class="modal-dialog modal-dialog-centered modal-sm">

                    <div class="modal-content logout-modal-content">

                        <div class="modal-header">

                            <h5
                                class="modal-title"
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

                        <div class="modal-footer logout-modal-actions">

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