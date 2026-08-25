<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";
require_once ROOT_PATH . "/app/Helpers/DateTimeHelper.php";

/*
|--------------------------------------------------------------------------
| Safe Dashboard Data
|--------------------------------------------------------------------------
*/

$user = is_array($user ?? null)
    ? $user
    : [];

$ticketCounts = array_merge([
    'total' => 0,
    'open_count' => 0,
    'in_progress_count' => 0,
    'pending_count' => 0,
    'resolved_count' => 0,
    'closed_count' => 0
], $ticketCounts ?? []);

$recentTickets = is_array($recentTickets ?? null)
    ? array_slice($recentTickets, 0, 5)
    : [];

$userName = $user['full_name']
    ?? $_SESSION['auth_user_name']
    ?? 'User';

$organizationName = $user['organization_name']
    ?? 'Organization';

$greeting = DateTimeHelper::greeting();

$currentDateTime = DateTimeHelper::now();

$currentDate = $currentDateTime->format('l, d F Y');
$currentTime = $currentDateTime->format('h:i A');

/*
|--------------------------------------------------------------------------
| Dashboard Helper Functions
|--------------------------------------------------------------------------
*/

function userDashboardStatusClass(string $status): string
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

function userDashboardPriorityClass(string $priority): string
{
    return match ($priority) {
        'low' => 'priority-low',
        'medium' => 'priority-medium',
        'high' => 'priority-high',
        'urgent' => 'priority-urgent',
        default => 'priority-medium'
    };
}
?>

<div class="container-fluid px-0">

    <!-- =========================================================
         USER DASHBOARD HERO
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-person-workspace"></i>
                        Customer Dashboard
                    </div>

                    <h1 class="page-title">
                        <?= htmlspecialchars($greeting); ?>,
                        <?= htmlspecialchars($userName); ?>! 👋
                    </h1>

                    <p class="page-description">
                        Create support requests, track ticket progress and
                        review updates from the support team.
                    </p>

                </div>

                <div class="page-actions">

                    <div class="date-pill">

                        <i class="bi bi-building"></i>

                        <div>

                            <div class="dashboard-date">
                                Organization
                            </div>

                            <div class="dashboard-time">
                                <?= htmlspecialchars($organizationName); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-grid mt-4">

                <a
                    href="<?= BASE_URL ?>/tickets/create"
                    class="quick-action-link">

                    <span class="quick-action-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </span>

                    <span class="quick-action-content">

                        <span class="quick-action-title">
                            Create Ticket
                        </span>

                        <span class="quick-action-description">
                            Submit a new support request.
                        </span>

                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a
                    href="<?= BASE_URL ?>/tickets"
                    class="quick-action-link">

                    <span class="quick-action-icon">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </span>

                    <span class="quick-action-content">

                        <span class="quick-action-title">
                            View Tickets
                        </span>

                        <span class="quick-action-description">
                            Review organization support tickets.
                        </span>

                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a
                    href="<?= BASE_URL ?>/profile"
                    class="quick-action-link">

                    <span class="quick-action-icon">
                        <i class="bi bi-person-circle"></i>
                    </span>

                    <span class="quick-action-content">

                        <span class="quick-action-title">
                            My Profile
                        </span>

                        <span class="quick-action-description">
                            Review your account information.
                        </span>

                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a
                    href="<?= BASE_URL ?>/logout"
                    class="quick-action-link">

                    <span class="quick-action-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>

                    <span class="quick-action-content">

                        <span class="quick-action-title">
                            Logout
                        </span>

                        <span class="quick-action-description">
                            Sign out from the helpdesk securely.
                        </span>

                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

            </div>

        </div>

    </section>

    <?php require ROOT_PATH . "/app/Views/partials/sla-overdue-alert.php"; ?>


    <!-- =========================================================
         TICKET METRICS
    ========================================================== -->
    <section class="content-section">

        <div class="metric-grid">

            <!-- Total Tickets -->
            <a
                href="<?= BASE_URL ?>/tickets"
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
                    <?= (int) $ticketCounts['total']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-collection"></i>
                    Organization tickets
                </div>

            </a>


            <!-- Open Tickets -->
            <a
                href="<?= BASE_URL ?>/tickets?status=open"
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
                    <?= (int) $ticketCounts['open_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-exclamation-circle"></i>
                    Waiting for support
                </div>

            </a>


            <!-- In Progress -->
            <a
                href="<?= BASE_URL ?>/tickets?status=in_progress"
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
                    <?= (int) $ticketCounts['in_progress_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-headset"></i>
                    Support team working
                </div>

            </a>


            <!-- Pending -->
            <a
                href="<?= BASE_URL ?>/tickets?status=pending"
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
                    <?= (int) $ticketCounts['pending_count']; ?>
                </div>

                <div class="metric-card-meta warning">
                    <i class="bi bi-hourglass"></i>
                    Awaiting further action
                </div>

            </a>


            <!-- Resolved -->
            <a
                href="<?= BASE_URL ?>/tickets?status=resolved"
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
                    <?= (int) $ticketCounts['resolved_count']; ?>
                </div>

                <div class="metric-card-meta positive">
                    <i class="bi bi-check-circle"></i>
                    Resolution completed
                </div>

            </a>


            <!-- Closed -->
            <a
                href="<?= BASE_URL ?>/tickets?status=closed"
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
                    <?= (int) $ticketCounts['closed_count']; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-lock-fill"></i>
                    Completed and archived
                </div>

            </a>

        </div>

    </section>


    <!-- =========================================================
         RECENT TICKETS
    ========================================================== -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    Recent Tickets
                </div>

                <div class="table-card-subtitle">
                    Showing the five most recently created organization tickets.
                </div>

            </div>

            <a
                href="<?= BASE_URL ?>/tickets"
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
                        New support requests will appear here after they are created.
                    </p>

                    <div class="empty-state-action">

                        <a
                            href="<?= BASE_URL ?>/tickets/create"
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
                                <th>Created By</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($recentTickets as $ticket): ?>

                                <?php
                                $ticketId = (int) ($ticket['id'] ?? 0);

                                $ticketStatus =
                                    $ticket['status'] ?? 'open';

                                $ticketPriority =
                                    $ticket['priority'] ?? 'medium';
                                ?>

                                <tr>

                                    <td data-label="Ticket">

                                        <div>

                                            <a
                                                href="<?= BASE_URL ?>/tickets/show/<?= $ticketId; ?>"
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
                                            $ticket['organization_name']
                                            ?? $organizationName
                                        ); ?>

                                    </td>

                                    <td data-label="Created By">

                                        <?= htmlspecialchars(
                                            $ticket['customer_name']
                                            ?? $userName
                                        ); ?>

                                    </td>

                                    <td data-label="Priority">

                                        <span class="priority-badge <?= userDashboardPriorityClass($ticketPriority); ?>">

                                            <?= htmlspecialchars(
                                                ucfirst($ticketPriority)
                                            ); ?>

                                        </span>

                                    </td>

                                    <td data-label="Status">

                                        <span class="status-badge <?= userDashboardStatusClass($ticketStatus); ?>">

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

                                        </div>

                                    </td>

                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <a
                                            href="<?= BASE_URL ?>/tickets/show/<?= $ticketId; ?>"
                                            class="table-action-btn table-action-view ms-auto"
                                            title="View ticket"
                                            aria-label="View ticket">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

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
                        href="<?= BASE_URL ?>/tickets"
                        class="view-all-link">

                        View complete ticket list

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================================================
         SUPPORT INFORMATION
    ========================================================== -->
    <section class="content-section">

        <div class="row g-3">

            <div class="col-lg-8">

                <section class="ui-panel h-100">

                    <div class="ui-panel-header">

                        <div class="ui-panel-title-wrap">

                            <h2 class="ui-panel-title">
                                Need Help?
                            </h2>

                            <p class="ui-panel-subtitle">
                                Create a support ticket and provide as much
                                information as possible.
                            </p>

                        </div>

                        <div class="ui-panel-actions">

                            <a
                                href="<?= BASE_URL ?>/tickets/create"
                                class="btn btn-primary-custom btn-sm">

                                <i class="bi bi-plus-circle me-1"></i>
                                Create Ticket

                            </a>

                        </div>

                    </div>

                    <div class="ui-panel-body">

                        <div class="activity-list">

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-card-text"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        Use a clear subject
                                    </div>

                                    <div class="activity-meta">
                                        Summarize the issue briefly so the
                                        support team can identify it quickly.
                                    </div>

                                </div>

                            </div>

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-info-circle"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        Add complete details
                                    </div>

                                    <div class="activity-meta">
                                        Mention the device, application, error
                                        message and steps already attempted.
                                    </div>

                                </div>

                            </div>

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-chat-left-text"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        Reply inside the ticket
                                    </div>

                                    <div class="activity-meta">
                                        Keep all ticket communication in one
                                        place so the support history stays clear.
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

            </div>

            <div class="col-lg-4">

                <section class="ui-panel h-100">

                    <div class="ui-panel-header">

                        <div class="ui-panel-title-wrap">

                            <h2 class="ui-panel-title">
                                Account Summary
                            </h2>

                            <p class="ui-panel-subtitle">
                                Your current helpdesk account.
                            </p>

                        </div>

                    </div>

                    <div class="ui-panel-body">

                        <div class="activity-list">

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-person-fill"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        <?= htmlspecialchars($userName); ?>
                                    </div>

                                    <div class="activity-meta">
                                        Account user
                                    </div>

                                </div>

                            </div>

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-buildings-fill"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        <?= htmlspecialchars($organizationName); ?>
                                    </div>

                                    <div class="activity-meta">
                                        Organization
                                    </div>

                                </div>

                            </div>

                            <div class="activity-item">

                                <div class="activity-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>

                                <div class="activity-content">

                                    <div class="activity-title">
                                        <?= htmlspecialchars($currentDate); ?>
                                    </div>

                                    <div class="activity-meta">
                                        <?= htmlspecialchars($currentTime); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

            </div>

        </div>

    </section>

</div>

<?php

require_once ROOT_PATH .
    "/app/Views/layouts/footer.php";

?>