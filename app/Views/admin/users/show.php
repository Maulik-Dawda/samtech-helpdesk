<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<?php
$roleLabel = ucfirst($user['role']);

if ($user['role'] === 'agent' && ($user['is_admin_agent'] ?? 0) == 1) {
    $roleLabel = 'Admin Agent';
}

$statusLabel = ((int)$user['is_active'] === 1) ? 'Active' : 'Disabled';

$avatarText = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));

$totalTickets = $statistics['total'] ?? 0;
$openTickets = $statistics['open_count'] ?? 0;
$inProgressTickets = $statistics['in_progress_count'] ?? 0;
$pendingTickets = $statistics['pending_count'] ?? 0;
$resolvedTickets = $statistics['resolved_count'] ?? 0;
$closedTickets = $statistics['closed_count'] ?? 0;
?>

<style>
    .profile-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    }

    .profile-avatar {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #b1e96f;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 900;
    }

    .badge-soft {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-active {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-disabled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-role {
        background: #eef8df;
        color: #4f772d;
    }

    .stat-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        background: #ffffff;
        padding: 20px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 900;
        color: #111827;
    }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        border-bottom: 1px solid #eef2f7;
        padding: 13px 0;
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    .info-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .info-value {
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        text-align: right;
    }

    .timeline-item {
        border-left: 3px solid #b1e96f;
        padding-left: 16px;
        margin-bottom: 18px;
    }

    .timeline-title {
        font-weight: 800;
        color: #111827;
    }

    .timeline-date {
        font-size: 12px;
        color: #64748b;
    }

    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
</style>

<div class="container-fluid mt-4">

    <div class="card profile-card mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                <div class="d-flex align-items-center gap-3">

                    <div class="profile-avatar">
                        <?= htmlspecialchars($avatarText); ?>
                    </div>

                    <div>
                        <h3 class="fw-bold mb-1">
                            <?= htmlspecialchars($user['full_name']); ?>
                        </h3>

                        <div class="text-muted mb-2">
                            <?= htmlspecialchars($user['email']); ?>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">

                            <span class="badge-soft badge-role">
                                <?= htmlspecialchars($roleLabel); ?>
                            </span>

                            <span class="badge-soft <?= ((int)$user['is_active'] === 1) ? 'badge-active' : 'badge-disabled'; ?>">
                                <?= htmlspecialchars($statusLabel); ?>
                            </span>

                            <?php if (($user['is_organization_admin'] ?? 0) == 1): ?>
                                <span class="badge-soft badge-role">
                                    Organization Admin
                                </span>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a
                        href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id']; ?>"
                        class="btn btn-primary-custom">
                        Edit Profile
                    </a>

                    <?php if ($user['role'] !== 'admin'): ?>
                        <a
                            href="<?= BASE_URL ?>/admin/users/disable/<?= $user['id']; ?>"
                            class="btn btn-outline-danger"
                            onclick="return confirm('Are you sure you want to disable this user?');">
                            Disable
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$totalTickets; ?></div>
                <div class="stat-label">Total Tickets</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$openTickets; ?></div>
                <div class="stat-label">Open</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$inProgressTickets; ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$pendingTickets; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$resolvedTickets; ?></div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$closedTickets; ?></div>
                <div class="stat-label">Closed</div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card profile-card mb-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        Profile Information
                    </h5>

                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= htmlspecialchars($user['full_name']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($user['email']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Role</div>
                        <div class="info-value"><?= htmlspecialchars($roleLabel); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Organization</div>
                        <div class="info-value"><?= htmlspecialchars($user['organization_name'] ?? '-'); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Organization Admin</div>
                        <div class="info-value">
                            <?= (($user['is_organization_admin'] ?? 0) == 1) ? 'Yes' : 'No'; ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Admin Agent</div>
                        <div class="info-value">
                            <?= (($user['is_admin_agent'] ?? 0) == 1) ? 'Yes' : 'No'; ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Email Verified</div>
                        <div class="info-value">
                            <?= (($user['is_email_verified'] ?? 0) == 1) ? 'Yes' : 'No'; ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value"><?= htmlspecialchars($statusLabel); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Created</div>
                        <div class="info-value">
                            <?= !empty($user['created_at']) ? htmlspecialchars($user['created_at']) : '-'; ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            <?= !empty($user['last_login_at']) ? htmlspecialchars($user['last_login_at']) : '-'; ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-8">

            <div class="card profile-card mb-4">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="fw-bold mb-0">
                            Recent Tickets
                        </h5>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>View</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($recentTickets)): ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No tickets found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($recentTickets as $ticket): ?>

                                        <tr>
                                            <td class="fw-bold">
                                                <?= htmlspecialchars($ticket['ticket_no']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($ticket['subject']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($ticket['created_at']); ?>
                                            </td>

                                            <td>
                                                <a
                                                    href="<?= BASE_URL ?>/tickets/show/<?= $ticket['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="card profile-card">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Activity Logs
                    </h5>

                    <?php if (empty($activityLogs)): ?>

                        <div class="text-muted">
                            No activity logs found.
                        </div>

                    <?php else: ?>

                        <?php foreach ($activityLogs as $log): ?>

                            <div class="timeline-item">

                                <div class="timeline-title">
                                    <?= htmlspecialchars($log['action']); ?>
                                </div>

                                <div class="timeline-date">
                                    <?= htmlspecialchars($log['created_at']); ?>
                                </div>

                                <?php if (!empty($log['ip_address'])): ?>

                                    <div class="small text-muted mt-1">
                                        IP: <?= htmlspecialchars($log['ip_address']); ?>
                                    </div>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <div class="mt-4 d-flex justify-content-between">

        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary">
            Back to Users
        </a>

        <a href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id']; ?>" class="btn btn-primary-custom">
            Edit User
        </a>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>