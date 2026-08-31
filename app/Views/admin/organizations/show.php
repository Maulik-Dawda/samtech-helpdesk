<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$organization = is_array($organization ?? null) ? $organization : [];
$users = is_array($users ?? null) ? $users : [];
$tickets = is_array($tickets ?? null) ? $tickets : [];

$organizationId = (int)($organization['id'] ?? 0);
$organizationName = trim((string)($organization['name'] ?? 'Organization'));
$organizationEmail = trim((string)($organization['email'] ?? ''));
$organizationPhone = trim((string)($organization['phone'] ?? ''));
$organizationAddress = trim((string)($organization['address'] ?? ''));
$maxUsers = (int)($organization['max_users'] ?? 0);
$isActive = !empty($organization['is_active']);

$totalTickets = (int)($totalTickets ?? count($tickets));
$page = (int)($page ?? 1);
$totalPages = (int)($totalPages ?? 1);

$userCount = count($users);
$capacityPercentage = $maxUsers > 0 ? min(100, round(($userCount / $maxUsers) * 100)) : 0;
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
                        <i class="bi bi-building-gear"></i>
                        Organization Details
                    </div>

                    <h1 class="page-title">
                        <?= htmlspecialchars($organizationName); ?>
                    </h1>

                    <p class="page-description">
                        Organization specifications, assigned users, and complete support ticket history.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/organizations"
                        class="btn btn-light">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Organizations
                    </a>

                    <a
                        href="<?= BASE_URL ?>/admin/organizations/edit/<?= $organizationId; ?>"
                        class="btn btn-primary-custom">
                        <i class="bi bi-pencil-fill me-1"></i>
                        Edit Organization
                    </a>

                </div>

            </div>

        </div>

    </section>

    <!-- =========================================================
         TOP ROW: ORG INFO (LEFT) & USERS (RIGHT)
    ========================================================== -->
    <div class="row g-4 mb-4">

        <!-- LEFT: Organization Information -->
        <div class="col-lg-6">

            <div class="ui-panel h-100">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            Organization Info
                        </h2>

                        <p class="ui-panel-subtitle">
                            General profile, contact details and user limit capacity.
                        </p>

                    </div>

                    <div class="ui-panel-actions">

                        <?php if ($isActive): ?>
                            <span class="status-badge status-resolved">
                                <i class="bi bi-check-circle me-1"></i> Active
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-closed">
                                <i class="bi bi-x-circle me-1"></i> Inactive
                            </span>
                        <?php endif; ?>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="profile-summary-list">

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <span class="profile-summary-label">Organization Name</span>
                                <span class="profile-summary-value"><?= htmlspecialchars($organizationName); ?></span>
                            </div>
                        </div>

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-hash"></i>
                            </div>
                            <div>
                                <span class="profile-summary-label">Organization ID</span>
                                <span class="profile-summary-value">#<?= $organizationId; ?></span>
                            </div>
                        </div>

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <span class="profile-summary-label">Email Address</span>
                                <span class="profile-summary-value">
                                    <?= !empty($organizationEmail) ? htmlspecialchars($organizationEmail) : '<em class="text-muted">Not specified</em>'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <span class="profile-summary-label">Phone Number</span>
                                <span class="profile-summary-value">
                                    <?= !empty($organizationPhone) ? htmlspecialchars($organizationPhone) : '<em class="text-muted">Not specified</em>'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <span class="profile-summary-label">Address</span>
                                <span class="profile-summary-value">
                                    <?= !empty($organizationAddress) ? htmlspecialchars($organizationAddress) : '<em class="text-muted">Not specified</em>'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="profile-summary-item">
                            <div class="profile-summary-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="profile-summary-label mb-0">User Limit Capacity</span>
                                    <span class="fw-bold small"><?= $userCount; ?> / <?= $maxUsers; ?> Users</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 99px;">
                                    <div
                                        class="progress-bar bg-success"
                                        role="progressbar"
                                        style="width: <?= $capacityPercentage; ?>%;"
                                        aria-valuenow="<?= $userCount; ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="<?= $maxUsers; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT: Organization Users -->
        <div class="col-lg-6">

            <div class="ui-panel h-100">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            <i class="bi bi-people-fill me-2 text-primary"></i>
                            Organization Users
                        </h2>

                        <p class="ui-panel-subtitle">
                            All registered accounts belonging to this organization.
                        </p>

                    </div>

                    <div class="ui-panel-actions d-flex align-items-center gap-2">
                        <input
                            type="search"
                            id="orgUserSearchShow"
                            class="form-control form-control-sm"
                            style="width: 170px;"
                            placeholder="Search users..."
                            autocomplete="off">
                        <span class="app-badge app-badge-primary">
                            <i class="bi bi-person me-1"></i> <?= $userCount; ?> / <?= $maxUsers; ?>
                        </span>
                    </div>

                </div>

                <div class="ui-panel-body p-0">

                    <?php if (empty($users)): ?>

                        <div class="empty-state py-4">

                            <div class="empty-state-icon">
                                <i class="bi bi-person-x"></i>
                            </div>

                            <h4 class="empty-state-title fs-6">
                                No Users Assigned
                            </h4>

                            <p class="empty-state-description small">
                                There are no users linked to this organization yet.
                            </p>

                        </div>

                    <?php else: ?>

                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">

                            <table class="table align-middle mb-0">

                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody id="orgUsersShowBody">

                                    <?php foreach ($users as $user): ?>

                                        <?php
                                        $initial = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
                                        $isOrgAdmin = !empty($user['is_organization_admin']);
                                        ?>

                                        <tr>

                                            <td>

                                                <div class="d-flex align-items-center gap-3">

                                                    <div class="table-avatar">
                                                        <?= htmlspecialchars($initial); ?>
                                                    </div>

                                                    <div>

                                                        <div class="fw-semibold">
                                                            <?= htmlspecialchars($user['full_name']); ?>
                                                        </div>

                                                        <div class="text-muted small">
                                                            <?= htmlspecialchars($user['email']); ?>
                                                        </div>

                                                    </div>

                                                </div>

                                            </td>

                                            <td>
                                                <?php if ($isOrgAdmin): ?>
                                                    <span class="badge-soft status-in_progress">
                                                        Org Admin
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-soft status-resolved">
                                                        Member
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($user['is_active'])): ?>
                                                    <span class="status-badge status-resolved">Active</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-closed">Inactive</span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <!-- =========================================================
         BOTTOM SECTION: ORGANIZATION TICKETS (PAGINATED)
    ========================================================== -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    <i class="bi bi-ticket-detailed me-2 text-primary"></i>
                    Organization Tickets
                </div>

                <div class="table-card-subtitle">
                    Support tickets submitted by users of <?= htmlspecialchars($organizationName); ?>.
                </div>

            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                    <input
                        type="search"
                        id="orgTicketSearchShow"
                        class="form-control ps-5"
                        style="min-width: 220px;"
                        placeholder="Search tickets..."
                        autocomplete="off">
                </div>

                <div class="app-badge app-badge-primary">
                    <i class="bi bi-ticket-perforated me-1"></i>
                    <?= $totalTickets; ?> <?= $totalTickets === 1 ? 'Ticket' : 'Tickets'; ?>
                </div>
            </div>

        </div>

        <div class="table-card-body">

            <?php if (empty($tickets)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>

                    <h3 class="empty-state-title">
                        No Tickets Found
                    </h3>

                    <p class="empty-state-description">
                        No support tickets have been created by this organization yet.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>
                                <th>Ticket No</th>
                                <th>Subject</th>
                                <th>Customer</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody id="orgTicketsShowBody">

                            <?php foreach ($tickets as $ticket): ?>

                                <?php
                                $statusClass = match ($ticket['status'] ?? '') {
                                    'open' => 'status-open',
                                    'in_progress' => 'status-in-progress',
                                    'pending' => 'status-pending',
                                    'resolved' => 'status-resolved',
                                    'closed' => 'status-closed',
                                    default => 'status-open'
                                };

                                $priorityClass = match ($ticket['priority'] ?? '') {
                                    'low' => 'priority-low',
                                    'medium' => 'priority-medium',
                                    'high' => 'priority-high',
                                    'urgent' => 'priority-urgent',
                                    default => 'priority-medium'
                                };
                                ?>

                                <tr>

                                    <td>
                                        <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticket['id']; ?>" class="fw-bold text-decoration-none text-dark">
                                            <?= htmlspecialchars($ticket['ticket_no'] ?? ''); ?>
                                        </a>
                                    </td>

                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars($ticket['subject'] ?? ''); ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($ticket['customer_name'] ?? 'User'); ?>
                                            </div>
                                            <?php if (!empty($ticket['customer_email'])): ?>
                                                <div class="text-muted small">
                                                    <?= htmlspecialchars($ticket['customer_email']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if (!empty($ticket['assigned_agent_name'])): ?>
                                            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600;">
                                                <i class="bi bi-person-badge me-1"></i>
                                                <?= htmlspecialchars($ticket['assigned_agent_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">
                                                Unassigned
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="priority-badge <?= $priorityClass; ?>">
                                            <?= ucfirst($ticket['priority'] ?? 'Medium'); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="status-badge <?= $statusClass; ?>">
                                            <?= ucfirst(str_replace('_', ' ', $ticket['status'] ?? 'Open')); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="text-muted small">
                                            <?= date('M d, Y H:i', strtotime($ticket['created_at'] ?? 'now')); ?>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticket['id']; ?>"
                                                class="table-action-btn table-action-view"
                                                title="View Ticket">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= $ticket['id']; ?>"
                                                target="_blank"
                                                class="table-action-btn table-action-view text-success"
                                                style="background: #e8f5e9; color: #2e7d32;"
                                                title="Print Ticket Report">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <!-- PAGINATION -->
                <div class="pagination-wrapper">

                    <div class="pagination-info">
                        Showing Page <strong><?= $page; ?></strong> of <strong><?= $totalPages; ?></strong>
                        (Total <?= $totalTickets; ?> <?= $totalTickets === 1 ? 'ticket' : 'tickets'; ?>)
                    </div>

                    <?php if ($totalPages > 1): ?>

                        <ul class="pagination mb-0">

                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>/organizations/show/<?= $organizationId; ?>?page=<?= $page - 1; ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>/organizations/show/<?= $organizationId; ?>?page=<?= $i; ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>/organizations/show/<?= $organizationId; ?>?page=<?= $page + 1; ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                </li>
                            <?php endif; ?>

                        </ul>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function bindSearch(inputId, tbodyId) {
        const input = document.getElementById(inputId);
        const tbody = document.getElementById(tbodyId);
        if (!input || !tbody) return;

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    bindSearch('orgUserSearchShow', 'orgUsersShowBody');
    bindSearch('orgTicketSearchShow', 'orgTicketsShowBody');
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
