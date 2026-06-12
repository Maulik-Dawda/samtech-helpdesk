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
        width: 260px;
        height: 100vh;
        background: #111827;
        position: fixed;
        left: 0;
        top: 0;
        color: #fff;
        z-index: 1000;

        overflow-y: auto;
        overflow-x: hidden;

        padding: 20px 16px;

        box-shadow:
            4px 0 20px rgba(0, 0, 0, .12);
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #1f2937;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 20px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    .sidebar-logo {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 25px;
    }

    .sidebar-logo img {
        width: 100%;
        height: auto;
        display: block;
    }

    .sidebar-title {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin: 18px 10px 8px;
    }

    .sidebar a {
        display: flex;
        align-items: center;

        text-decoration: none;

        color: #d1d5db;

        padding: 11px 14px;

        border-radius: 10px;

        margin-bottom: 5px;

        font-size: 14px;
        font-weight: 500;

        transition: all .2s ease;
    }

    .sidebar a:hover {
        background: #1f2937;
        color: #ffffff;
    }

    .sidebar a.active {
        background: #b1e96f;
        color: #111827;
        font-weight: 600;
    }

    .main-content {
        margin-left: 260px;

        height: 100vh;

        overflow-y: auto;
        overflow-x: hidden;

        background: #f8fafc;

        padding: 25px;
    }

    .main-content::-webkit-scrollbar {
        width: 8px;
    }

    .main-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }

    .main-content::-webkit-scrollbar-track {
        background: #f1f5f9;
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

    <?php if ($role === 'agent'): ?>

        <a
            class="<?= activeMenu('/agent/tickets/create', $currentUri); ?>"
            href="<?= BASE_URL ?>/agent/tickets/create">

            Create Ticket

        </a>

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