<?php

require_once ROOT_PATH .
    "/app/Helpers/PermissionHelper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['auth_user_role'] ?? '';

$isAdmin = PermissionHelper::isAdmin();
$isAgent = PermissionHelper::isAgent();
$isAdminAgent = PermissionHelper::isAdminAgent();

$isOrgAdmin =
    (int)($_SESSION['is_organization_admin'] ?? 0) === 1;

$currentUri = $_SERVER['REQUEST_URI'] ?? '';

$sidebarFullName =
    $_SESSION['auth_user_name'] ?? 'User';

$sidebarRoleLabel = match ($role) {
    'admin' => 'Administrator',
    'agent' => $isAdminAgent
        ? 'Admin Agent'
        : 'Support Agent',
    'user' => $isOrgAdmin
        ? 'Organization Admin'
        : 'User',
    default => 'User'
};

$sidebarNameParts = preg_split(
    '/\s+/',
    trim($sidebarFullName),
    -1,
    PREG_SPLIT_NO_EMPTY
);

$sidebarInitials = '';

if (!empty($sidebarNameParts[0])) {
    $sidebarInitials .= strtoupper(
        mb_substr($sidebarNameParts[0], 0, 1)
    );
}

if (!empty($sidebarNameParts[1])) {
    $sidebarInitials .= strtoupper(
        mb_substr($sidebarNameParts[1], 0, 1)
    );
}

if ($sidebarInitials === '') {
    $sidebarInitials = 'U';
}

if (!function_exists('sidebarCurrentPath')) {
    function sidebarCurrentPath(): string
    {
        $path = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        return '/' . ltrim(
            $path ?: '/',
            '/'
        );
    }
}

if (!function_exists('sidebarActive')) {
    function sidebarActive(
        string $path,
        bool $exact = false
    ): string {
        $currentPath = rtrim(sidebarCurrentPath(), '/');
        $targetPath = rtrim('/' . ltrim($path, '/'), '/');

        if ($exact || $currentPath === $targetPath) {
            return $currentPath === $targetPath ? 'active' : '';
        }

        if ($targetPath === '/agent/tickets' && str_starts_with($currentPath, '/agent/tickets/create')) {
            return '';
        }

        if ($targetPath === '/admin/users' && str_starts_with($currentPath, '/admin/users/create')) {
            return '';
        }

        if ($targetPath === '/organizations' && str_starts_with($currentPath, '/organizations/create')) {
            return '';
        }

        return str_starts_with($currentPath, $targetPath) ? 'active' : '';
    }
}

$dashboardUrl = BASE_URL . '/user-dashboard';

if ($isAdmin) {
    $dashboardUrl = BASE_URL . '/admin-dashboard';
} elseif ($isAgent) {
    $dashboardUrl = BASE_URL . '/agent-dashboard';
}
?>

<aside
    class="sidebar"
    id="appSidebar"
    aria-label="Main navigation"
>

    <div class="sidebar-header">

        <a
            class="sidebar-logo"
            href="<?= $dashboardUrl; ?>"
            aria-label="Open dashboard"
        >
            <img
                src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                alt="Samtech Solutions"
                width="185"
                height="56"
            >
        </a>

        <button
            type="button"
            class="sidebar-close-btn"
            id="sidebarClose"
            aria-label="Close navigation menu"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    <nav class="sidebar-nav">

        <div class="sidebar-section">

            <div class="sidebar-title">
                Main
            </div>

            <?php if ($isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/admin-dashboard', true); ?>"
                    href="<?= BASE_URL ?>/admin-dashboard"
                >
                    <i class="bi bi-grid-1x2-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Dashboard</span>
                </a>

            <?php elseif ($isAgent): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent-dashboard', true); ?>"
                    href="<?= BASE_URL ?>/agent-dashboard"
                >
                    <i class="bi bi-grid-1x2-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Dashboard</span>
                </a>

            <?php else: ?>

                <a
                    class="sidebar-link <?= sidebarActive('/user-dashboard', true); ?>"
                    href="<?= BASE_URL ?>/user-dashboard"
                >
                    <i class="bi bi-grid-1x2-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Dashboard</span>
                </a>

            <?php endif; ?>

        </div>

        <div class="sidebar-section">

            <div class="sidebar-title">
                Tickets
            </div>

            <?php if ($role === 'user'): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets', true); ?>"
                    href="<?= BASE_URL ?>/tickets"
                >
                    <i class="bi bi-ticket-perforated-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">My Tickets</span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/tickets/create', true); ?>"
                    href="<?= BASE_URL ?>/tickets/create"
                >
                    <i class="bi bi-plus-circle-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Create Ticket</span>
                </a>

            <?php endif; ?>

            <?php if ($isAgent || $isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent/tickets/create', true); ?>"
                    href="<?= BASE_URL ?>/agent/tickets/create"
                >
                    <i class="bi bi-plus-circle-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Create Ticket</span>
                </a>

            <?php endif; ?>

            <?php if ($isAgent || $isAdmin): ?>

                <a
                    class="sidebar-link <?= sidebarActive('/agent/tickets'); ?>"
                    href="<?= BASE_URL ?>/agent/tickets"
                >
                    <i class="bi bi-ticket-detailed-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">All Tickets</span>
                </a>

            <?php endif; ?>

        </div>

        <?php if ($isAdmin || $isAdminAgent): ?>

            <div class="sidebar-section">

                <div class="sidebar-title">
                    Administration
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/admin/users'); ?>"
                    href="<?= BASE_URL ?>/admin/users"
                >
                    <i class="bi bi-people-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Users &amp; Agents</span>
                </a>

                <?php if ($isAdmin || $isAdminAgent): ?>

                    <a
                        class="sidebar-link <?= sidebarActive('/admin/activity-logs'); ?>"
                        href="<?= BASE_URL ?>/admin/activity-logs"
                    >
                        <i class="bi bi-clock-history sidebar-link-icon"></i>
                        <span class="sidebar-link-text">Activity Logs</span>
                    </a>

                <?php endif; ?>

                <?php if ($isAdmin): ?>

                    <a
                        class="sidebar-link <?= sidebarActive('/admin/permissions'); ?>"
                        href="<?= BASE_URL ?>/admin/permissions"
                    >
                        <i class="bi bi-shield-lock-fill sidebar-link-icon"></i>
                        <span class="sidebar-link-text">Permissions</span>
                    </a>

                <?php endif; ?>

            </div>

        <?php endif; ?>

        <?php if ($isAdmin || $isAgent): ?>

            <div class="sidebar-section">

                <div class="sidebar-title">
                    Organizations
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/organizations', true); ?>"
                    href="<?= BASE_URL ?>/organizations"
                >
                    <i class="bi bi-buildings-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">View Organizations</span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/organizations/create', true); ?>"
                    href="<?= BASE_URL ?>/organizations/create"
                >
                    <i class="bi bi-building-add sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Create Organization</span>
                </a>

            </div>

        <?php endif; ?>

        <?php if ($role === 'user' && $isOrgAdmin): ?>

            <div class="sidebar-section">

                <div class="sidebar-title">
                    Organization
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/organization-users'); ?>"
                    href="<?= BASE_URL ?>/organization-users"
                >
                    <i class="bi bi-person-vcard-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Organization Users</span>
                </a>

            </div>

        <?php endif; ?>

        <?php if ($isAdmin || $isAgent): ?>

            <div class="sidebar-section">

                <div class="sidebar-title">
                    Reports
                </div>

                <a
                    class="sidebar-link <?= sidebarActive('/reports/tickets', true); ?>"
                    href="<?= BASE_URL ?>/reports/tickets"
                >
                    <i class="bi bi-bar-chart-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Ticket Reports</span>
                </a>

                <a
                    class="sidebar-link <?= sidebarActive('/reports/ticket-detail', true); ?>"
                    href="<?= BASE_URL ?>/reports/ticket-detail"
                >
                    <i class="bi bi-file-earmark-text-fill sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Ticket Detail Report</span>
                </a>

            </div>

        <?php endif; ?>

        <div class="sidebar-section">

            <div class="sidebar-title">
                Account
            </div>

            <a
                class="sidebar-link <?= sidebarActive('/profile', true); ?>"
                href="<?= BASE_URL ?>/profile"
            >
                <i class="bi bi-person-circle sidebar-link-icon"></i>
                <span class="sidebar-link-text">My Profile</span>
            </a>

        </div>

    </nav>

    <div class="sidebar-footer">

        <div class="sidebar-account">

            <div class="sidebar-account-avatar">
                <?= htmlspecialchars($sidebarInitials); ?>
            </div>

            <div class="sidebar-account-info">

                <div class="sidebar-account-name">
                    <?= htmlspecialchars($sidebarFullName); ?>
                </div>

                <div class="sidebar-account-role">
                    <?= htmlspecialchars($sidebarRoleLabel); ?>
                </div>

            </div>

            <button
                type="button"
                class="sidebar-account-logout"
                data-bs-toggle="modal"
                data-bs-target="#logoutModal"
                aria-label="Logout"
            >
                <i class="bi bi-box-arrow-right"></i>
            </button>

        </div>

    </div>

</aside>