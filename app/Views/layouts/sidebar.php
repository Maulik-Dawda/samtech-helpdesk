<?php

require_once ROOT_PATH . "/app/Helpers/PermissionHelper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['auth_user_role'] ?? '';
$isAdminAgent = PermissionHelper::isAdminAgent();
$isAdmin = PermissionHelper::isAdmin();
$isAgent = PermissionHelper::isAgent();
$isOrgAdmin = (int)($_SESSION['is_organization_admin'] ?? 0) === 1;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

$fullName = $_SESSION['auth_user_name'] ?? 'User';

$roleLabel = match ($role) {
    'admin' => 'Administrator',
    'agent' => $isAdminAgent ? 'Admin Agent' : 'Support Agent',
    'user' => $isOrgAdmin ? 'Organization Admin' : 'User',
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

/*
|--------------------------------------------------------------------------
| Active Menu Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('sidebarPathMatches')) {
    function sidebarPathMatches(
        string $path,
        string $currentUri,
        bool $exact = false
    ): bool {
        $currentPath = parse_url(
            $currentUri,
            PHP_URL_PATH
        ) ?? '';

        $basePath = parse_url(
            BASE_URL,
            PHP_URL_PATH
        ) ?? '';

        if (
            $basePath !== '' &&
            str_starts_with($currentPath, $basePath)
        ) {
            $currentPath = substr(
                $currentPath,
                strlen($basePath)
            );
        }

        $currentPath = '/' . ltrim(
            $currentPath,
            '/'
        );

        $path = '/' . ltrim(
            $path,
            '/'
        );

        if ($exact) {
            return rtrim($currentPath, '/') === rtrim($path, '/');
        }

        return str_starts_with(
            $currentPath,
            $path
        );
    }
}

if (!function_exists('sidebarActive')) {
    function sidebarActive(
        string $path,
        string $currentUri,
        bool $exact = false
    ): string {
        return sidebarPathMatches(
            $path,
            $currentUri,
            $exact
        ) ? 'active' : '';
    }
}
?>

<aside
    class="sidebar"
    id="appSidebar"
    aria-label="Main navigation">

    <div class="sidebar-header">

        <a
            class="sidebar-logo"
            href="<?=
                    $isAdmin
                        ? BASE_URL . '/admin-dashboard'
                        : (
                            $isAgent
                            ? BASE_URL . '/agent-dashboard'
                            : BASE_URL . '/user-dashboard'
                        );
                    ?>"
            aria-label="Samtech Helpdesk dashboard">
            <img
                src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                alt="Samtech Solutions">
        </a>

        <button
            type="button"
            class="sidebar-close-btn"
            id="sidebarClose"
            aria-label="Close navigation menu">
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    <nav class="sidebar-nav">

        <section class="sidebar-section">

            <div class="sidebar-title">
                Main
            </div>

            <?php if ($isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/admin-dashboard', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/admin-dashboard">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Dashboard
                    </span>
                </a>

            <?php elseif ($isAgent): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent-dashboard', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/agent-dashboard">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Dashboard
                    </span>
                </a>

            <?php else: ?>

                <a
                    class="sidebar-link <?= sidebarActive('/user-dashboard', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/user-dashboard">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Dashboard
                    </span>
                </a>

            <?php endif; ?>

        </section>

        <section class="sidebar-section">

            <div class="sidebar-title">
                Tickets
            </div>

            <?php if ($role === 'user'): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/tickets">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        My Tickets
                    </span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets/create', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/tickets/create">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Create Ticket
                    </span>
                </a>

            <?php endif; ?>

            <?php if ($isAgent): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent/tickets/create', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/agent/tickets/create">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Create Ticket
                    </span>
                </a>

            <?php endif; ?>

            <?php if ($isAgent || $isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent/tickets', $currentUri); ?>"
                    href="<?= BASE_URL ?>/agent/tickets">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-ticket-detailed-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        All Tickets
                    </span>
                </a>

            <?php endif; ?>

            <?php if ($isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/tickets">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        My Tickets
                    </span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets/create', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/tickets/create">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Create Ticket
                    </span>
                </a>

            <?php endif; ?>

        </section>

        <?php if ($isAdmin || $isAdminAgent): ?>

            <section class="sidebar-section">

                <div class="sidebar-title">
                    Administration
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/admin/users', $currentUri); ?>"
                    href="<?= BASE_URL ?>/admin/users">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-people-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Users &amp; Agents
                    </span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/admin/activity-logs', $currentUri); ?>"
                    href="<?= BASE_URL ?>/admin/activity-logs">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Activity Logs
                    </span>
                </a>

                <?php if ($isAdmin): ?>

                    <a
                        class="sidebar-link <?= sidebarActive('/admin/permissions', $currentUri); ?>"
                        href="<?= BASE_URL ?>/admin/permissions">
                        <span class="sidebar-link-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>

                        <span class="sidebar-link-text">
                            Permissions
                        </span>
                    </a>

                <?php endif; ?>

            </section>

        <?php endif; ?>

        <?php if ($isAdmin || $isAdminAgent): ?>

            <section class="sidebar-section">

                <div class="sidebar-title">
                    Organizations
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/organizations', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/organizations">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-buildings-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        View Organizations
                    </span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/organizations/create', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/organizations/create">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-building-add"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Create Organization
                    </span>
                </a>

            </section>

        <?php endif; ?>

        <?php if ($role === 'user' && $isOrgAdmin): ?>

            <section class="sidebar-section">

                <div class="sidebar-title">
                    Organization
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/organization-users', $currentUri); ?>"
                    href="<?= BASE_URL ?>/organization-users">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-person-vcard-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Organization Users
                    </span>
                </a>

            </section>

        <?php endif; ?>

        <?php if ($isAdmin || $isAgent): ?>

            <section class="sidebar-section">

                <div class="sidebar-title">
                    Reports
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/reports/tickets', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/reports/tickets">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Ticket Reports
                    </span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/reports/ticket-detail', $currentUri, true); ?>"
                    href="<?= BASE_URL ?>/reports/ticket-detail">
                    <span class="sidebar-link-icon">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </span>

                    <span class="sidebar-link-text">
                        Ticket Detail Report
                    </span>
                </a>

            </section>

        <?php endif; ?>

        <section class="sidebar-section">

            <div class="sidebar-title">
                Account
            </div>

            <a
                class="sidebar-link <?= sidebarActive('/profile', $currentUri, true); ?>"
                href="<?= BASE_URL ?>/profile">
                <span class="sidebar-link-icon">
                    <i class="bi bi-person-circle"></i>
                </span>

                <span class="sidebar-link-text">
                    My Profile
                </span>
            </a>

        </section>

    </nav>

    <div class="sidebar-footer">

        <div class="sidebar-account">

            <div class="sidebar-account-avatar">
                <?= htmlspecialchars($initials); ?>
            </div>

            <div class="sidebar-account-info">

                <div class="sidebar-account-name">
                    <?= htmlspecialchars($fullName); ?>
                </div>

                <div class="sidebar-account-role">
                    <?= htmlspecialchars($roleLabel); ?>
                </div>

            </div>

            <button
                type="button"
                class="sidebar-account-logout"
                data-bs-toggle="modal"
                data-bs-target="#logoutModal"
                aria-label="Logout"
                title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>

        </div>

    </div>

</aside>