<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";
require_once ROOT_PATH . "/app/Helpers/DateTimeHelper.php";

/*
|--------------------------------------------------------------------------
| Safe Dashboard Data
|--------------------------------------------------------------------------
*/

$ticketCounts = array_merge([
    'total' => 0,
    'open_count' => 0,
    'in_progress_count' => 0,
    'pending_count' => 0,
    'resolved_count' => 0,
    'closed_count' => 0
], $ticketCounts ?? []);

$userCounts = array_merge([
    'admins' => 0,
    'agents' => 0,
    'users' => 0
], $userCounts ?? []);

$organizationCount = (int)($organizationCount ?? 0);

$recentTickets = is_array($recentTickets ?? null)
    ? array_slice($recentTickets, 0, 5)
    : [];

$recentActivities = is_array($recentActivities ?? null)
    ? array_slice($recentActivities, 0, 8)
    : [];

$monthlyTickets = is_array($monthlyTickets ?? null)
    ? $monthlyTickets
    : [];

$organizationTickets = is_array($organizationTickets ?? null)
    ? $organizationTickets
    : [];

$adminName = $_SESSION['auth_user_name'] ?? 'Admin';

$greeting = DateTimeHelper::greeting();
$dubaiNow = DateTimeHelper::now();

$currentDate = $dubaiNow->format('l, d F Y');
$currentTime = $dubaiNow->format('h:i:s A');

/*
|--------------------------------------------------------------------------
| Status Helpers
|--------------------------------------------------------------------------
*/

function dashboardStatusClass(string $status): string
{
    return match ($status) {
        'open' => 'status-open',
        'in_progress' => 'status-in-progress',
        'pending' => 'status-pending',
        'resolved' => 'status-resolved',
        'closed' => 'status-closed',
        default => 'status-open'
    };
}

function dashboardPriorityClass(string $priority): string
{
    return match ($priority) {
        'low' => 'priority-low',
        'medium' => 'priority-medium',
        'high' => 'priority-high',
        'urgent' => 'priority-urgent',
        default => 'priority-medium'
    };
}

function dashboardActivityIcon(string $action): string
{
    $action = strtolower($action);

    if (str_contains($action, 'login')) {
        return 'bi-box-arrow-in-right';
    }

    if (
        str_contains($action, 'ticket') &&
        str_contains($action, 'create')
    ) {
        return 'bi-ticket-perforated-fill';
    }

    if (
        str_contains($action, 'reply') ||
        str_contains($action, 'message')
    ) {
        return 'bi-chat-left-text-fill';
    }

    if (
        str_contains($action, 'status') ||
        str_contains($action, 'closed') ||
        str_contains($action, 'resolved')
    ) {
        return 'bi-arrow-repeat';
    }

    if (
        str_contains($action, 'user') ||
        str_contains($action, 'agent')
    ) {
        return 'bi-person-fill';
    }

    if (str_contains($action, 'organization')) {
        return 'bi-buildings-fill';
    }

    return 'bi-activity';
}
?>

<div class="container-fluid px-0">

    <!-- Dashboard Hero -->
    <section class="ui-panel mb-3">

        <div class="ui-panel-body p-3">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-2">
                        <i class="bi bi-speedometer2"></i>
                        Administrator Dashboard
                    </div>

                    <h2 class="page-title fs-4 mb-1">
                        <?= htmlspecialchars($greeting); ?>,
                        <?= htmlspecialchars($adminName); ?>! 👋
                    </h2>

                    <p class="page-description small mb-3 mb-md-0 text-muted">
                        Monitor tickets, users, organizations and recent
                        helpdesk activity from one place.
                    </p>

                </div>

                <div class="page-actions mt-3 mt-md-0">

                    <div class="date-pill">

                        <i class="bi bi-calendar3"></i>

                        <div>

                            <div class="dashboard-date" data-live-date="true">
                                <?= htmlspecialchars($currentDate); ?>
                            </div>

                            <div class="dashboard-time" data-live-time="true">
                                <?= htmlspecialchars($currentTime); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="quick-actions-grid mt-4">

                <a
                    href="<?= BASE_URL ?>/agent/tickets/create"
                    class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title">
                            Create Ticket
                        </span>

                        <span class="quick-action-description">
                            Open a ticket for any organization.
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>

                <a
                    href="<?= BASE_URL ?>/admin/users/create"
                    class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-person-plus-fill"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title">
                            Create User or Agent
                        </span>

                        <span class="quick-action-description">
                            Add users, agents or administrators.
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>

                <a
                    href="<?= BASE_URL ?>/organizations/create"
                    class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-building-add"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title">
                            Create Organization
                        </span>

                        <span class="quick-action-description">
                            Add a new customer organization.
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>

                <a
                    href="<?= BASE_URL ?>/reports/tickets"
                    class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title">
                            Open Reports
                        </span>

                        <span class="quick-action-description">
                            Review, filter and print ticket reports.
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>

            </div>

        </div>

    </section>

    <?php require ROOT_PATH . "/app/Views/partials/sla-overdue-alert.php"; ?>

    <!-- Ticket Metrics -->
    <section class="content-section">

        <div class="metric-grid">

            <a
                href="<?= BASE_URL ?>/agent/tickets"
                class="metric-card text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Total Tickets
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['total']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-collection"></i>
                    All helpdesk tickets
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=open"
                class="metric-card metric-card-info text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Open Tickets
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['open_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-exclamation-circle"></i>
                    Waiting for attention
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=in_progress"
                class="metric-card text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    In Progress
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['in_progress_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-person-workspace"></i>
                    Currently being handled
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=pending"
                class="metric-card metric-card-warning text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Pending Tickets
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['pending_count']; ?>
                </div>

                <div class="metric-card-meta warning">
                    <i class="bi bi-hourglass"></i>
                    Awaiting further action
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=resolved"
                class="metric-card metric-card-success text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Resolved Tickets
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['resolved_count']; ?>
                </div>

                <div class="metric-card-meta positive">
                    <i class="bi bi-check-circle"></i>
                    Resolution completed
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=closed"
                class="metric-card metric-card-danger text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-archive-fill"></i>
                    </div>

                    <i class="bi bi-arrow-up-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Closed Tickets
                </div>

                <div class="metric-card-value">
                    <?= (int)$ticketCounts['closed_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-lock-fill"></i>
                    Completed and archived
                </div>

            </a>

        </div>

    </section>

    <!-- Account Summary -->
    <section class="content-section">

        <div class="metric-grid">

            <a
                href="<?= BASE_URL ?>/organizations"
                class="metric-card text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-buildings-fill"></i>
                    </div>

                    <i class="bi bi-chevron-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Organizations
                </div>

                <div class="metric-card-value">
                    <?= $organizationCount; ?>
                </div>

                <div class="metric-card-meta">
                    Customer organizations
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/admin/users"
                class="metric-card metric-card-info text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>

                    <i class="bi bi-chevron-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Administrators
                </div>

                <div class="metric-card-value">
                    <?= (int)$userCounts['admins']; ?>
                </div>

                <div class="metric-card-meta">
                    Full system administrators
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/admin/users?role=agent"
                class="metric-card metric-card-warning text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-headset"></i>
                    </div>

                    <i class="bi bi-chevron-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Agents
                </div>

                <div class="metric-card-value">
                    <?= (int)$userCounts['agents']; ?>
                </div>

                <div class="metric-card-meta">
                    Support and admin agents
                </div>

            </a>

            <a
                href="<?= BASE_URL ?>/admin/users?role=user"
                class="metric-card metric-card-success text-decoration-none">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <i class="bi bi-chevron-right text-muted"></i>

                </div>

                <div class="metric-card-label">
                    Users
                </div>

                <div class="metric-card-value">
                    <?= (int)$userCounts['users']; ?>
                </div>

                <div class="metric-card-meta">
                    Organization users
                </div>

            </a>

        </div>

    </section>

    <!-- Recent Tickets -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    Recent Tickets
                </div>

                <div class="table-card-subtitle">
                    Showing the five most recently created tickets.
                </div>

            </div>

            <a
                href="<?= BASE_URL ?>/agent/tickets"
                class="btn btn-light btn-sm">
                View All Tickets
                <i class="bi bi-arrow-right ms-1"></i>
            </a>

        </div>

        <div class="table-card-body">

            <?php if (empty($recentTickets)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>

                    <h3 class="empty-state-title">
                        No tickets found
                    </h3>

                    <p class="empty-state-description">
                        New helpdesk tickets will appear here.
                    </p>

                    <div class="empty-state-action">

                        <a
                            href="<?= BASE_URL ?>/agent/tickets/create"
                            class="btn btn-primary-custom">
                            Create Ticket
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Ticket</th>
                                <th>Organization</th>
                                <th>Customer</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($recentTickets as $ticket): ?>

                                <?php
                                $ticketStatus =
                                    $ticket['status'] ?? 'open';

                                $ticketPriority =
                                    $ticket['priority'] ?? 'medium';
                                ?>

                                <tr>

                                    <td data-label="Ticket">

                                        <div>

                                            <a
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= (int)$ticket['id']; ?>"
                                                class="fw-bold text-decoration-none">
                                                <?= htmlspecialchars(
                                                    $ticket['ticket_no'] ?? '-'
                                                ); ?>
                                            </a>

                                            <?php if (!empty($ticket['subject'])): ?>

                                                <div class="text-muted small mt-1">
                                                    <?= htmlspecialchars(
                                                        mb_strimwidth(
                                                            $ticket['subject'],
                                                            0,
                                                            45,
                                                            '...'
                                                        )
                                                    ); ?>
                                                </div>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                    <td data-label="Organization">
                                        <?= htmlspecialchars(
                                            $ticket['organization_name'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td data-label="Customer">
                                        <?= htmlspecialchars(
                                            $ticket['customer_name'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td data-label="Assigned Agent">
                                        <?php if (!empty($ticket['assigned_agent_name'])): ?>
                                            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:8px; font-size:11px; font-weight:600;">
                                                <i class="bi bi-person-badge me-1"></i>
                                                <?= htmlspecialchars($ticket['assigned_agent_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Unassigned</span>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="Priority">

                                        <span class="priority-badge <?= dashboardPriorityClass($ticketPriority); ?>">
                                            <?= htmlspecialchars(
                                                ucfirst($ticketPriority)
                                            ); ?>
                                        </span>

                                    </td>

                                    <td data-label="Status">

                                        <span class="status-badge <?= dashboardStatusClass($ticketStatus); ?>">
                                            <?= htmlspecialchars(
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $ticketStatus
                                                    )
                                                )
                                            ); ?>
                                        </span>

                                    </td>

                                    <td data-label="Created">

                                        <div class="fw-semibold">
                                            <?= htmlspecialchars(
                                                DateTimeHelper::format(
                                                    $ticket['created_at'] ?? null,
                                                    'd M Y'
                                                )
                                            ); ?>
                                        </div>

                                        <div class="text-muted small mt-1">
                                            <?= htmlspecialchars(
                                                DateTimeHelper::format(
                                                    $ticket['created_at'] ?? null,
                                                    'h:i A'
                                                )
                                            ); ?>
                                            Dubai
                                        </div>

                                    </td>

                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= (int)$ticket['id']; ?>"
                                                class="table-action-btn table-action-view"
                                                title="View ticket"
                                                aria-label="View ticket">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= (int)$ticket['id']; ?>"
                                                target="_blank"
                                                class="table-action-btn table-action-view text-success"
                                                style="background: #e8f5e9; color: #2e7d32;"
                                                title="Print Ticket Report"
                                                aria-label="Print Ticket Report">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>
                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="pagination-wrapper">

                    <div class="pagination-info">
                        Showing latest
                        <?= count($recentTickets); ?>
                        tickets
                    </div>

                    <a
                        href="<?= BASE_URL ?>/agent/tickets"
                        class="view-all-link">
                        View complete ticket list
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

    <!-- Charts -->
    <section class="content-section">

        <div class="row g-3">

            <div class="col-xl-4 col-lg-6">

                <div class="chart-card h-100">

                    <div class="chart-card-header">

                        <div>

                            <h2 class="chart-card-title">
                                Tickets by Status
                            </h2>

                            <div class="chart-card-subtitle">
                                Current distribution of ticket statuses.
                            </div>

                        </div>

                        <div class="app-badge app-badge-primary">
                            All Time
                        </div>

                    </div>

                    <div class="chart-wrapper">
                        <canvas id="statusChart"></canvas>
                    </div>

                </div>

            </div>

            <div class="col-xl-4 col-lg-6">

                <div class="chart-card h-100">

                    <div class="chart-card-header">

                        <div>

                            <h2 class="chart-card-title">
                                Tickets Over Time
                            </h2>

                            <div class="chart-card-subtitle">
                                Monthly ticket creation trends.
                            </div>

                        </div>

                    </div>

                    <div class="chart-wrapper">
                        <canvas id="monthlyChart"></canvas>
                    </div>

                </div>

            </div>

            <div class="col-xl-4 col-lg-12">

                <div class="chart-card h-100">

                    <div class="chart-card-header">

                        <div>

                            <h2 class="chart-card-title">
                                Tickets by Organization
                            </h2>

                            <div class="chart-card-subtitle">
                                Organizations generating the most tickets.
                            </div>

                        </div>

                    </div>

                    <div class="chart-wrapper">
                        <canvas id="organizationChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Recent Activity -->
    <section class="ui-panel content-section">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">
                    Recent Activity
                </h2>

                <p class="ui-panel-subtitle">
                    Latest user, ticket and system actions.
                </p>

            </div>

            <div class="ui-panel-actions">

                <a
                    href="<?= BASE_URL ?>/admin/activity-logs"
                    class="btn btn-light btn-sm">
                    View Activity Logs
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>

            </div>

        </div>

        <div class="ui-panel-body">

            <?php if (empty($recentActivities)): ?>

                <div class="empty-state py-4">

                    <div class="empty-state-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <h3 class="empty-state-title">
                        No recent activity
                    </h3>

                    <p class="empty-state-description">
                        User and system activities will appear here.
                    </p>

                </div>

            <?php else: ?>

                <div class="activity-list">

                    <?php foreach ($recentActivities as $activity): ?>

                        <div class="activity-item">

                            <div class="activity-icon">
                                <i class="bi <?= dashboardActivityIcon(
                                                    $activity['action'] ?? ''
                                                ); ?>"></i>
                            </div>

                            <div class="activity-content">

                                <div class="activity-title">
                                    <?= htmlspecialchars(
                                        $activity['action'] ??
                                            'System activity'
                                    ); ?>
                                </div>

                                <div class="activity-meta">

                                    <span>
                                        <?= htmlspecialchars(
                                            $activity['full_name'] ??
                                                'System'
                                        ); ?>
                                    </span>

                                    <span class="mx-1">•</span>

                                    <span>
                                        <?= htmlspecialchars(
                                            DateTimeHelper::format(
                                                $activity['created_at'] ?? null,
                                                'd M Y, h:i A'
                                            )
                                        ); ?>
                                        Dubai Time
                                    </span>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

        <?php if (!empty($recentActivities)): ?>

            <div class="ui-panel-footer">

                <span class="list-limit-note">
                    Showing the latest
                    <?= count($recentActivities); ?>
                    activities.
                </span>

                <a
                    href="<?= BASE_URL ?>/admin/activity-logs"
                    class="view-all-link">
                    View all activity
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        <?php endif; ?>

    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded.');
            return;
        }

        const chartFont = {
            family: "'Inter', Arial, sans-serif",
            size: 11
        };

        const statusCanvas =
            document.getElementById('statusChart');

        if (statusCanvas) {
            new Chart(statusCanvas, {
                type: 'doughnut',

                data: {
                    labels: [
                        'Open',
                        'In Progress',
                        'Pending',
                        'Resolved',
                        'Closed'
                    ],

                    datasets: [{
                        data: [
                            <?= (int)$ticketCounts['open_count']; ?>,
                            <?= (int)$ticketCounts['in_progress_count']; ?>,
                            <?= (int)$ticketCounts['pending_count']; ?>,
                            <?= (int)$ticketCounts['resolved_count']; ?>,
                            <?= (int)$ticketCounts['closed_count']; ?>
                        ],

                        backgroundColor: [
                            '#2563eb',
                            '#7c3aed',
                            '#d97706',
                            '#16a34a',
                            '#dc2626'
                        ],

                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 5
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',

                    plugins: {
                        legend: {
                            position: 'bottom',

                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 14,
                                font: chartFont
                            }
                        },

                        tooltip: {
                            displayColors: true
                        }
                    }
                }
            });
        }

        const monthlyCanvas =
            document.getElementById('monthlyChart');

        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'line',

                data: {
                    labels: <?= json_encode(
                                array_column(
                                    $monthlyTickets,
                                    'month'
                                ),
                                JSON_UNESCAPED_UNICODE |
                                    JSON_UNESCAPED_SLASHES
                            ); ?>,

                    datasets: [{
                        label: 'Tickets',

                        data: <?= json_encode(
                                    array_map(
                                        'intval',
                                        array_column(
                                            $monthlyTickets,
                                            'total'
                                        )
                                    )
                                ); ?>,

                        borderColor: '#3b941f',
                        backgroundColor: 'rgba(177, 233, 111, .22)',

                        borderWidth: 2,
                        tension: .4,
                        fill: true,

                        pointBackgroundColor: '#3b941f',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },

                    plugins: {
                        legend: {
                            display: false
                        }
                    },

                    scales: {
                        x: {
                            grid: {
                                display: false
                            },

                            ticks: {
                                font: chartFont
                            }
                        },

                        y: {
                            beginAtZero: true,

                            grid: {
                                color: '#eef2f7'
                            },

                            ticks: {
                                precision: 0,
                                font: chartFont
                            }
                        }
                    }
                }
            });
        }

        const organizationCanvas =
            document.getElementById('organizationChart');

        if (organizationCanvas) {
            new Chart(organizationCanvas, {
                type: 'bar',

                data: {
                    labels: <?= json_encode(
                                array_column(
                                    $organizationTickets,
                                    'name'
                                ),
                                JSON_UNESCAPED_UNICODE |
                                    JSON_UNESCAPED_SLASHES
                            ); ?>,

                    datasets: [{
                        label: 'Tickets',

                        data: <?= json_encode(
                                    array_map(
                                        'intval',
                                        array_column(
                                            $organizationTickets,
                                            'total'
                                        )
                                    )
                                ); ?>,

                        backgroundColor: '#6cb33f',
                        borderRadius: 7,
                        borderSkipped: false,
                        maxBarThickness: 24
                    }]
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        }
                    },

                    scales: {
                        x: {
                            beginAtZero: true,

                            grid: {
                                color: '#eef2f7'
                            },

                            ticks: {
                                precision: 0,
                                font: chartFont
                            }
                        },

                        y: {
                            grid: {
                                display: false
                            },

                            ticks: {
                                autoSkip: false,
                                font: chartFont
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php
require_once ROOT_PATH .
    "/app/Views/layouts/footer.php";
?>