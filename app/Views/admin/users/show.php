<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$user = is_array($user ?? null)
    ? $user
    : [];

$statistics = is_array($statistics ?? null)
    ? $statistics
    : [];

$recentTickets = is_array($recentTickets ?? null)
    ? $recentTickets
    : [];

$activityLogs = is_array($activityLogs ?? null)
    ? array_slice($activityLogs, 0, 10)
    : [];

$userId = (int) ($user['id'] ?? 0);

$fullName = trim((string) ($user['full_name'] ?? ''));

if ($fullName === '') {
    $fullName = 'Unnamed User';
}

$email = trim((string) ($user['email'] ?? ''));

$role = strtolower((string) ($user['role'] ?? 'user'));

$isAdminAgent = !empty($user['is_admin_agent']);
$isOrganizationAdmin = !empty($user['is_organization_admin']);
$isEmailVerified = !empty($user['is_email_verified']);
$isActive = !empty($user['is_active']);

$roleLabel = match ($role) {
    'admin' => 'Administrator',
    'agent' => $isAdminAgent ? 'Admin Agent' : 'Agent',
    default => 'User',
};

$statusLabel = $isActive
    ? 'Active'
    : 'Inactive';

$avatarText = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($fullName, 0, 1))
    : strtoupper(substr($fullName, 0, 1));

$totalTickets = (int) ($statistics['total'] ?? 0);
$openTickets = (int) ($statistics['open_count'] ?? 0);
$inProgressTickets = (int) ($statistics['in_progress_count'] ?? 0);
$pendingTickets = (int) ($statistics['pending_count'] ?? 0);
$resolvedTickets = (int) ($statistics['resolved_count'] ?? 0);
$closedTickets = (int) ($statistics['closed_count'] ?? 0);

$organizationName = trim(
    (string) ($user['organization_name'] ?? '')
);

if ($organizationName === '') {
    $organizationName = 'Not assigned';
}

$createdAt = !empty($user['created_at'])
    ? (string) $user['created_at']
    : '-';

$lastLoginAt = !empty($user['last_login_at'])
    ? (string) $user['last_login_at']
    : 'No login recorded';

/**
 * Generates a ticket status badge class.
 */
function getProfileTicketStatusClass(string $status): string
{
    return match (strtolower($status)) {
        'open' => 'status-open',
        'in_progress', 'in progress' => 'status-progress',
        'pending' => 'status-pending',
        'resolved' => 'status-resolved',
        'closed' => 'status-closed',
        default => 'status-open',
    };
}

/**
 * Generates a ticket priority badge class.
 */
function getProfileTicketPriorityClass(string $priority): string
{
    return match (strtolower($priority)) {
        'critical', 'urgent', 'high' => 'status-closed',
        'medium' => 'status-pending',
        'low' => 'status-resolved',
        default => 'status-open',
    };
}

?>

<div class="container-fluid px-0">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">

                        <i class="bi bi-person-badge-fill"></i>

                        User Profile

                    </div>

                    <h1 class="page-title">
                        <?= htmlspecialchars($fullName); ?>
                    </h1>

                    <p class="page-description">
                        Review account information, permissions, ticket
                        statistics and recent activity.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/admin/users"
                        class="btn btn-light">

                        <i class="bi bi-arrow-left me-2"></i>

                        Back to Users

                    </a>

                    <?php if ($role !== 'admin'): ?>

                        <a
                            href="<?= BASE_URL ?>/admin/users/edit/<?= $userId; ?>"
                            class="btn btn-primary-custom">

                            <i class="bi bi-pencil-fill me-2"></i>

                            Edit User

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         PROFILE SUMMARY
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">

                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">

                    <div class="table-avatar">
                        <?= htmlspecialchars($avatarText); ?>
                    </div>

                    <div>

                        <h2 class="page-title mb-1">
                            <?= htmlspecialchars($fullName); ?>
                        </h2>

                        <div class="text-muted mb-3">

                            <?php if ($email !== ''): ?>

                                <a
                                    href="mailto:<?= htmlspecialchars($email); ?>"
                                    class="text-decoration-none">

                                    <i class="bi bi-envelope me-1"></i>

                                    <?= htmlspecialchars($email); ?>

                                </a>

                            <?php else: ?>

                                <span>
                                    No email address
                                </span>

                            <?php endif; ?>

                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <?php if ($role === 'admin'): ?>

                                <span class="status-badge status-closed">

                                    <i class="bi bi-shield-lock me-1"></i>

                                    <?= htmlspecialchars($roleLabel); ?>

                                </span>

                            <?php elseif ($role === 'agent'): ?>

                                <span class="status-badge status-progress">

                                    <i class="bi bi-headset me-1"></i>

                                    <?= htmlspecialchars($roleLabel); ?>

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-person me-1"></i>

                                    <?= htmlspecialchars($roleLabel); ?>

                                </span>

                            <?php endif; ?>


                            <?php if ($isActive): ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Active

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-closed">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Inactive

                                </span>

                            <?php endif; ?>


                            <?php if ($isOrganizationAdmin): ?>

                                <span class="status-badge status-pending">

                                    <i class="bi bi-building-check me-1"></i>

                                    Organization Admin

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>


                <?php if ($role !== 'admin'): ?>

                    <div class="d-flex flex-wrap gap-2">

                        <a
                            href="<?= BASE_URL ?>/admin/users/edit/<?= $userId; ?>"
                            class="btn btn-primary-custom">

                            <i class="bi bi-pencil-fill me-2"></i>

                            Edit Profile

                        </a>

                        <?php if ($isActive): ?>

                            <a
                                href="<?= BASE_URL ?>/admin/users/disable/<?= $userId; ?>"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Are you sure you want to disable this user?');">

                                <i class="bi bi-person-x-fill me-2"></i>

                                Disable User

                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =========================================================
         TICKET METRICS
    ========================================================== -->
    <section class="content-section">

        <div class="metric-grid">

            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Total Tickets
                </div>

                <div class="metric-card-value">
                    <?= $totalTickets; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-collection"></i>
                    All recorded tickets
                </div>

            </div>


            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Open
                </div>

                <div class="metric-card-value">
                    <?= $openTickets; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-circle"></i>
                    Awaiting action
                </div>

            </div>


            <div class="metric-card metric-card-info">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    In Progress
                </div>

                <div class="metric-card-value">
                    <?= $inProgressTickets; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-gear"></i>
                    Currently being handled
                </div>

            </div>


            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Pending
                </div>

                <div class="metric-card-value">
                    <?= $pendingTickets; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-clock"></i>
                    Waiting for response
                </div>

            </div>


            <div class="metric-card metric-card-success">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Resolved
                </div>

                <div class="metric-card-value">
                    <?= $resolvedTickets; ?>
                </div>

                <div class="metric-card-meta positive">
                    <i class="bi bi-check2"></i>
                    Successfully resolved
                </div>

            </div>


            <div class="metric-card metric-card-danger">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-lock-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Closed
                </div>

                <div class="metric-card-value">
                    <?= $closedTickets; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-archive"></i>
                    Completed and closed
                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         ACCOUNT AND PERMISSION DETAILS
    ========================================================== -->
    <div class="row g-4 mb-4">

        <div class="col-lg-6">

            <section class="ui-panel h-100">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Profile Information
                        </h2>

                        <p class="ui-panel-subtitle">
                            Basic identity and account information.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="list-group list-group-flush">

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Full Name
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($fullName); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Email Address
                            </div>

                            <div class="fw-semibold text-end text-break">
                                <?= htmlspecialchars($email !== '' ? $email : '-'); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Account Role
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($roleLabel); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Account Status
                            </div>

                            <div>

                                <?php if ($isActive): ?>

                                    <span class="status-badge status-resolved">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge status-closed">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Created
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($createdAt); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Last Login
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($lastLoginAt); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>


        <div class="col-lg-6">

            <section class="ui-panel h-100">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Access and Permissions
                        </h2>

                        <p class="ui-panel-subtitle">
                            Organization assignment and account privileges.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="list-group list-group-flush">

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div>

                                <div class="fw-semibold">
                                    Organization
                                </div>

                                <div class="text-muted small">
                                    Assigned customer organization
                                </div>

                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($organizationName); ?>
                            </div>

                        </div>


                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div>

                                <div class="fw-semibold">
                                    Organization Administrator
                                </div>

                                <div class="text-muted small">
                                    Can manage permitted organization users
                                </div>

                            </div>

                            <?php if ($isOrganizationAdmin): ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Enabled

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-closed">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Disabled

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div>

                                <div class="fw-semibold">
                                    Admin Agent
                                </div>

                                <div class="text-muted small">
                                    Extended agent administration privileges
                                </div>

                            </div>

                            <?php if ($isAdminAgent): ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Enabled

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-closed">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Disabled

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div>

                                <div class="fw-semibold">
                                    Email Verification
                                </div>

                                <div class="text-muted small">
                                    Confirms ownership of the email address
                                </div>

                            </div>

                            <?php if ($isEmailVerified): ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-patch-check me-1"></i>

                                    Verified

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-pending">

                                    <i class="bi bi-exclamation-circle me-1"></i>

                                    Not Verified

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div>

                                <div class="fw-semibold">
                                    Sign-in Access
                                </div>

                                <div class="text-muted small">
                                    Determines whether the account can sign in
                                </div>

                            </div>

                            <?php if ($isActive): ?>

                                <span class="status-badge status-resolved">

                                    <i class="bi bi-unlock me-1"></i>

                                    Allowed

                                </span>

                            <?php else: ?>

                                <span class="status-badge status-closed">

                                    <i class="bi bi-lock me-1"></i>

                                    Blocked

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </section>

        </div>

    </div>


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
                    Most recent support tickets associated with this account.
                </div>

            </div>

            <div class="app-badge app-badge-primary">

                <i class="bi bi-ticket-perforated"></i>

                <?= count($recentTickets); ?>
                Recent

            </div>

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
                        This account does not have any recent support tickets.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
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

                                $ticketNumber = (string) (
                                    $ticket['ticket_no'] ?? '-'
                                );

                                $ticketSubject = (string) (
                                    $ticket['subject'] ?? 'Untitled Ticket'
                                );

                                $priority = strtolower(
                                    (string) ($ticket['priority'] ?? 'normal')
                                );

                                $ticketStatus = strtolower(
                                    (string) ($ticket['status'] ?? 'open')
                                );

                                $ticketCreatedAt = (string) (
                                    $ticket['created_at'] ?? '-'
                                );
                                ?>

                                <tr>

                                    <td data-label="Ticket">

                                        <span class="fw-semibold">
                                            <?= htmlspecialchars($ticketNumber); ?>
                                        </span>

                                    </td>

                                    <td data-label="Subject">

                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($ticketSubject); ?>
                                        </div>

                                    </td>

                                    <td data-label="Priority">

                                        <span class="status-badge <?= getProfileTicketPriorityClass($priority); ?>">

                                            <?= htmlspecialchars(ucfirst($priority)); ?>

                                        </span>

                                    </td>

                                    <td data-label="Status">

                                        <span class="status-badge <?= getProfileTicketStatusClass($ticketStatus); ?>">

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

                                        <?= htmlspecialchars($ticketCreatedAt); ?>

                                    </td>

                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <a
                                            href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>"
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

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================================================
         RECENT ACTIVITY
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">
                    Recent Activity
                </h2>

                <p class="ui-panel-subtitle">
                    The 10 most recent recorded activities for this account.
                </p>

            </div>

            <div class="app-badge app-badge-primary">

                <i class="bi bi-clock-history"></i>

                <?= count($activityLogs); ?>
                Activities

            </div>

        </div>

        <div class="ui-panel-body">

            <?php if (empty($activityLogs)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <h3 class="empty-state-title">
                        No activity recorded
                    </h3>

                    <p class="empty-state-description">
                        Recent account activity will appear here.
                    </p>

                </div>

            <?php else: ?>

                <div class="list-group list-group-flush">

                    <?php foreach ($activityLogs as $index => $log): ?>

                        <?php
                        $activityAction = trim(
                            (string) ($log['action'] ?? '')
                        );

                        if ($activityAction === '') {
                            $activityAction = 'Account activity';
                        }

                        $activityDate = !empty($log['created_at'])
                            ? (string) $log['created_at']
                            : '-';

                        $ipAddress = trim(
                            (string) ($log['ip_address'] ?? '')
                        );
                        ?>

                        <div class="list-group-item px-0 py-3">

                            <div class="d-flex align-items-start gap-3">

                                <div class="table-avatar">

                                    <i class="bi bi-activity"></i>

                                </div>

                                <div class="flex-grow-1">

                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">

                                        <div>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($activityAction); ?>
                                            </div>

                                            <?php if ($ipAddress !== ''): ?>

                                                <div class="text-muted small mt-1">

                                                    <i class="bi bi-globe me-1"></i>

                                                    IP Address:
                                                    <?= htmlspecialchars($ipAddress); ?>

                                                </div>

                                            <?php endif; ?>

                                        </div>

                                        <div class="text-muted small text-md-end">

                                            <i class="bi bi-calendar3 me-1"></i>

                                            <?= htmlspecialchars($activityDate); ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================================================
         FOOTER ACTIONS
    ========================================================== -->
    <div class="d-flex flex-wrap justify-content-between gap-2">

        <a
            href="<?= BASE_URL ?>/admin/users"
            class="btn btn-light">

            <i class="bi bi-arrow-left me-2"></i>

            Back to Users

        </a>

        <?php if ($role !== 'admin'): ?>

            <a
                href="<?= BASE_URL ?>/admin/users/edit/<?= $userId; ?>"
                class="btn btn-primary-custom">

                <i class="bi bi-pencil-fill me-2"></i>

                Edit User

            </a>

        <?php endif; ?>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>