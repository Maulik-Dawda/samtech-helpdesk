<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
    body {
        background: #f8fafc;
    }

    .dashboard-wrapper {
        padding: 18px 10px 30px;
    }

    .dashboard-hero {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .06);
        margin-bottom: 22px;
    }

    .welcome-title {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 5px;
    }

    .welcome-subtitle {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .date-pill {
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 0 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #111827;
        font-size: 14px;
        background: #fff;
        font-weight: 600;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 18px;
        min-height: 132px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .05);
        transition: .2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        right: -35px;
        top: -35px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        opacity: .12;
    }

    .stat-total::after { background: #3b82f6; }
    .stat-open::after { background: #22c55e; }
    .stat-progress::after { background: #8b5cf6; }
    .stat-pending::after { background: #f97316; }
    .stat-resolved::after { background: #14b8a6; }
    .stat-closed::after { background: #ef4444; }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 13px;
    }

    .icon-total {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    .icon-open {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .icon-progress {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }

    .icon-pending {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
    }

    .icon-resolved {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    .icon-closed {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .stat-number {
        font-size: 27px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
        font-weight: 600;
    }

    .stat-trend {
        margin-top: 14px;
        font-size: 12px;
        color: #16a34a;
        font-weight: 700;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .dashboard-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dashboard-card-title {
        font-weight: 800;
        color: #111827;
        margin: 0;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .view-all-btn {
        border: 1px solid #d6dde8;
        color: #111827;
        background: #ffffff;
        border-radius: 10px;
        padding: 7px 13px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .view-all-btn:hover {
        background: #f8fafc;
        color: #111827;
    }

    .modern-table {
        margin: 0;
    }

    .modern-table thead th {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: .05em;
        border-bottom: 1px solid #eef2f7;
        padding: 13px 14px;
        font-weight: 800;
    }

    .modern-table tbody td {
        padding: 13px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #111827;
        border-bottom: 1px solid #f1f5f9;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .badge-soft {
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }

    .status-open {
        background: #dcfce7;
        color: #15803d;
    }

    .status-in-progress {
        background: #ede9fe;
        color: #6d28d9;
    }

    .status-pending {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-resolved {
        background: #ccfbf1;
        color: #0f766e;
    }

    .status-closed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .quick-action-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .05);
        height: 100%;
    }

    .quick-action-title {
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 14px;
    }

    .quick-action-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        color: #111827;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid #eef2f7;
        margin-bottom: 10px;
        background: #f8fafc;
    }

    .quick-action-link:hover {
        background: #f0f9eb;
        color: #3b941f;
        border-color: rgba(108, 179, 63, .25);
    }

    .quick-action-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #f0f9eb;
        color: #3b941f;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    @media(max-width: 768px) {
        .dashboard-hero {
            padding: 18px;
        }

        .welcome-title {
            font-size: 20px;
        }

        .date-pill {
            margin-top: 14px;
            justify-content: center;
        }
    }
</style>

<div class="container-fluid dashboard-wrapper">

    <div class="dashboard-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="welcome-title">
                    Welcome back, <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'Agent'); ?>! 👋
                </h3>

                <p class="welcome-subtitle">
                    Here’s what’s happening with support tickets today.
                </p>
            </div>

            <div class="date-pill">
                <i class="bi bi-calendar3"></i>
                <?= date('M d, Y'); ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-total">
                <div class="stat-icon icon-total">
                    <i class="bi bi-ticket-detailed"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['total']; ?></div>
                <div class="stat-label">Total Tickets</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Overall
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-open">
                <div class="stat-icon icon-open">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['open_count']; ?></div>
                <div class="stat-label">Open</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Active queue
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-progress">
                <div class="stat-icon icon-progress">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['in_progress_count']; ?></div>
                <div class="stat-label">In Progress</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Being handled
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-pending">
                <div class="stat-icon icon-pending">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['pending_count']; ?></div>
                <div class="stat-label">Pending</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Awaiting action
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-resolved">
                <div class="stat-icon icon-resolved">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['resolved_count']; ?></div>
                <div class="stat-label">Resolved</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Completed
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-closed">
                <div class="stat-icon icon-closed">
                    <i class="bi bi-x-octagon"></i>
                </div>
                <div class="stat-number"><?= (int)$ticketCounts['closed_count']; ?></div>
                <div class="stat-label">Closed</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-right"></i> Archived
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3">

        <div class="col-xl-9 col-lg-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="bi bi-list-task"></i>
                        Recent Tickets
                    </h5>

                    <a href="<?= BASE_URL ?>/agent/tickets" class="view-all-btn">
                        View All Tickets
                        <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Organization</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($recentTickets)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No recent tickets found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentTickets as $ticket): ?>

                                    <?php
                                    $statusClass = match ($ticket['status']) {
                                        'open' => 'status-open',
                                        'in_progress' => 'status-in-progress',
                                        'pending' => 'status-pending',
                                        'resolved' => 'status-resolved',
                                        'closed' => 'status-closed',
                                        default => 'status-open'
                                    };
                                    ?>

                                    <tr>
                                        <td class="fw-bold">
                                            <?= htmlspecialchars($ticket['ticket_no']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <span class="badge-soft <?= $statusClass; ?>">
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="quick-action-card">

                <div class="quick-action-title">
                    Quick Actions
                </div>

                <a href="<?= BASE_URL ?>/agent/tickets" class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-ticket-detailed"></i>
                    </span>
                    View Tickets
                </a>

                <a href="<?= BASE_URL ?>/reports/tickets" class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </span>
                    Ticket Reports
                </a>

                <a href="<?= BASE_URL ?>/profile" class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    My Profile
                </a>

                <a href="<?= BASE_URL ?>/logout" class="quick-action-link">
                    <span class="quick-action-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    Logout
                </a>

            </div>
        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>