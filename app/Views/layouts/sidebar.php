<?php
require_once ROOT_PATH . "/app/Helpers/PermissionHelper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['auth_user_role'] ?? '';
$isOrgAdmin = $_SESSION['is_organization_admin'] ?? 0;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

function activeMenu($path, $currentUri)
{
    return str_contains($currentUri, $path) ? 'active' : '';
}
?>

<style>
    html,
    body {
        height: 100%;
        margin: 0;
        overflow: hidden;
        background: #f8fafc;
    }

    .sidebar {
        width: 280px;
        height: 100vh;
        background: linear-gradient(180deg,
                #0f172a 0%,
                #111827 100%);

        position: fixed;
        left: 0;
        top: 0;

        color: #fff;
        z-index: 1000;

        overflow-y: auto;
        overflow-x: hidden;

        padding: 20px 18px;

        border-right: 1px solid rgba(255, 255, 255, .05);

        box-shadow:
            8px 0 30px rgba(15, 23, 42, .15);
    }

    .sidebar-logo {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 24px;

        box-shadow:
            0 8px 20px rgba(0, 0, 0, .08);
    }

    .sidebar-logo img {
        width: 100%;
        display: block;
    }

    .sidebar-title {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .12em;
        margin: 22px 10px 10px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;

        text-decoration: none;

        color: #cbd5e1;

        padding: 12px 14px;

        border-radius: 12px;

        margin-bottom: 6px;

        font-size: 14px;
        font-weight: 500;

        transition: all .25s ease;
    }

    .sidebar a i {
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, .06);
        color: #ffffff;
        transform: translateX(4px);
    }

    .sidebar a.active {
        background: linear-gradient(135deg,
                #b1e96f,
                #8fd14f);

        color: #111827;

        font-weight: 700;

        box-shadow:
            0 10px 20px rgba(177, 233, 111, .25);
    }

    .sidebar a.active i {
        color: #111827;
    }

    .main-content {
        margin-left: 280px;

        min-height: 100vh;

        overflow-y: auto;

        background: #f8fafc;

        padding: 25px;
    }

    @media (max-width:768px) {

        html,
        body {
            overflow: auto;
        }

        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
            overflow: visible;
        }

        .main-content {
            margin-left: 0;
            height: auto;
            overflow: visible;
        }
    }
</style>
<div class="sidebar">

    <div class="sidebar-logo">
        <img src="<?= BASE_URL ?>/assets/images/samtech-logo.png" alt="Samtech Solutions">
    </div>

    <div class="sidebar-title">Main</div>

    <?php if ($role === 'admin'): ?>
        <a class="<?= activeMenu('/admin-dashboard', $currentUri); ?>" href="<?= BASE_URL ?>/admin-dashboard">Dashboard</a>
    <?php elseif ($role === 'agent'): ?>
        <a class="<?= activeMenu('/agent-dashboard', $currentUri); ?>" href="<?= BASE_URL ?>/agent-dashboard">Dashboard</a>
    <?php else: ?>
        <a class="<?= activeMenu('/user-dashboard', $currentUri); ?>" href="<?= BASE_URL ?>/user-dashboard">Dashboard</a>
    <?php endif; ?>

    <div class="sidebar-title">Tickets</div>

    <?php if ($role === 'user' || $role === 'admin'): ?>
        <a class="<?= activeMenu('/tickets', $currentUri); ?>" href="<?= BASE_URL ?>/tickets">My Tickets</a>
        <a class="<?= activeMenu('/tickets/create', $currentUri); ?>" href="<?= BASE_URL ?>/tickets/create">Create Ticket</a>
    <?php endif; ?>

    <?php if ($role === 'agent' || $role === 'admin'): ?>
        <a class="<?= activeMenu('/agent/tickets', $currentUri); ?>" href="<?= BASE_URL ?>/agent/tickets">All Tickets</a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>

        <div class="sidebar-title">Admin</div>

        <a class="<?= activeMenu('/admin/users', $currentUri); ?>" href="<?= BASE_URL ?>/admin/users">User Management</a>
        <a class="<?= activeMenu('/admin/permissions', $currentUri); ?>" href="<?= BASE_URL ?>/admin/permissions">Permissions</a>
        <a class="<?= activeMenu('/admin/activity-logs', $currentUri); ?>" href="<?= BASE_URL ?>/admin/activity-logs">
            Activity Logs
        </a>

    <?php endif; ?>
    <?php if ($role === 'admin' || $role === 'agent'): ?>

        <div class="sidebar-title">Organizations</div>

        <a class="<?= activeMenu('/organizations', $currentUri); ?>" href="<?= BASE_URL ?>/organizations">
            <i class="bi bi-building me-2"></i>
            View Organizations
        </a>

        <a class="<?= activeMenu('/organizations/create', $currentUri); ?>" href="<?= BASE_URL ?>/organizations/create">
            <i class="bi bi-plus-circle me-2"></i>
            Create Organization
        </a>

    <?php endif; ?>

    <?php if ($role === 'user' && $isOrgAdmin == 1): ?>

        <div class="sidebar-title">Organization</div>

        <a class="<?= activeMenu('/organization-users', $currentUri); ?>" href="<?= BASE_URL ?>/organization-users">Organization Users</a>

    <?php endif; ?>

    <?php if (
        $role === 'admin'
        || PermissionHelper::has('view_ticket_reports')
        || PermissionHelper::has('view_ticket_detail_report')
    ): ?>

        <div class="sidebar-title">Reports</div>

        <?php if ($role === 'admin' || PermissionHelper::has('view_ticket_reports')): ?>
            <a class="<?= activeMenu('/reports/tickets', $currentUri); ?>" href="<?= BASE_URL ?>/reports/tickets">Ticket Reports</a>
        <?php endif; ?>

        <?php if ($role === 'admin' || PermissionHelper::has('view_ticket_detail_report')): ?>
            <a class="<?= activeMenu('/reports/ticket-detail', $currentUri); ?>" href="<?= BASE_URL ?>/reports/ticket-detail">Ticket Detail Report</a>
        <?php endif; ?>

    <?php endif; ?>

    <div class="sidebar-title">Account</div>

    <a href="<?= BASE_URL ?>/logout">Logout</a>

</div>