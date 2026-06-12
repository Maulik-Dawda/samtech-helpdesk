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

    .stat-total::after {
        background: #3b82f6;
    }

    .stat-open::after {
        background: #22c55e;
    }

    .stat-progress::after {
        background: #8b5cf6;
    }

    .stat-pending::after {
        background: #f97316;
    }

    .stat-resolved::after {
        background: #14b8a6;
    }

    .stat-closed::after {
        background: #ef4444;
    }

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

    .dashboard-card-title i {
        color: #475569;
    }

    .dashboard-card-body {
        padding: 20px;
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

    .summary-tile {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: 14px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
    }

    .summary-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .activity-item:last-child {
        border-bottom: 0;
    }

    .activity-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #6cb33f;
        margin-top: 6px;
        box-shadow: 0 0 0 4px rgba(177, 233, 111, .25);
        flex-shrink: 0;
    }

    .activity-action {
        font-size: 13px;
        font-weight: 800;
        color: #111827;
    }

    .activity-meta {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .chart-box {
        height: 260px;
        position: relative;
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
                    Welcome back, <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'Admin'); ?>! 👋
                </h3>

                <p class="welcome-subtitle">
                    Here’s what’s happening with your helpdesk today.
                </p>
            </div>

            <div class="date-pill">
                <i class="bi bi-calendar3"></i>
                <?= date('M d, Y'); ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <!-- Total Tickets -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-total">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-total">
                        <i class="bi bi-ticket-detailed"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['total']; ?>
                        </div>

                        <div class="stat-label">
                            Total Tickets
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Open -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-open">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-open">
                        <i class="bi bi-folder2-open"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['open_count']; ?>
                        </div>

                        <div class="stat-label">
                            Open
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-progress">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-progress">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['in_progress_count']; ?>
                        </div>

                        <div class="stat-label">
                            In Progress
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-pending">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-pending">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['pending_count']; ?>
                        </div>

                        <div class="stat-label">
                            Pending
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-resolved">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-resolved">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['resolved_count']; ?>
                        </div>

                        <div class="stat-label">
                            Resolved
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Closed -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stat-card stat-closed">
                <div class="d-flex align-items-center">

                    <div class="stat-icon icon-closed">
                        <i class="bi bi-x-circle"></i>
                    </div>

                    <div class="ms-3">
                        <div class="stat-number">
                            <?= (int)$ticketCounts['closed_count']; ?>
                        </div>

                        <div class="stat-label">
                            Closed
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="dashboard-card mb-4">
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
                        <th>Ticket ID</th>
                        <th>Organization</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($recentTickets)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
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

                                <td>
                                    <?= htmlspecialchars($ticket['created_at']); ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="summary-tile">
                <div class="summary-value"><?= (int)$organizationCount; ?></div>
                <div class="summary-label">Organizations</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-tile">
                <div class="summary-value"><?= (int)$userCounts['admins']; ?></div>
                <div class="summary-label">Admins</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-tile">
                <div class="summary-value"><?= (int)$userCounts['agents']; ?></div>
                <div class="summary-label">Agents</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-tile">
                <div class="summary-value"><?= (int)$userCounts['users']; ?></div>
                <div class="summary-label">Users</div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-xl-4 col-lg-6">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="bi bi-pie-chart"></i>
                        Tickets by Status
                    </h5>
                </div>

                <div class="dashboard-card-body">
                    <div class="chart-box">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        Tickets Over Time
                    </h5>
                </div>

                <div class="dashboard-card-body">
                    <div class="chart-box">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="bi bi-building"></i>
                        Tickets by Organization
                    </h5>
                </div>

                <div class="dashboard-card-body">
                    <div class="chart-box">
                        <canvas id="organizationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5 class="dashboard-card-title">
                <i class="bi bi-activity"></i>
                Recent Activity
            </h5>

            <a href="<?= BASE_URL ?>/admin/activity-logs" class="view-all-btn">
                View Logs
                <i class="bi bi-chevron-right ms-1"></i>
            </a>
        </div>

        <div class="dashboard-card-body">
            <?php if (empty($recentActivities)): ?>

                <div class="text-muted">
                    No recent activity found.
                </div>

            <?php else: ?>

                <?php foreach ($recentActivities as $activity): ?>

                    <div class="activity-item">
                        <div class="activity-dot"></div>

                        <div>
                            <div class="activity-action">
                                <?= htmlspecialchars($activity['action']); ?>
                            </div>

                            <div class="activity-meta">
                                <?= htmlspecialchars($activity['full_name'] ?? 'System'); ?>
                                —
                                <?= htmlspecialchars($activity['created_at']); ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    const chartFont = {
        family: "'Inter', 'Arial', sans-serif",
        size: 12
    };

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'],
            datasets: [{
                data: [
                    <?= (int)$ticketCounts['open_count']; ?>,
                    <?= (int)$ticketCounts['in_progress_count']; ?>,
                    <?= (int)$ticketCounts['pending_count']; ?>,
                    <?= (int)$ticketCounts['resolved_count']; ?>,
                    <?= (int)$ticketCounts['closed_count']; ?>
                ],
                backgroundColor: [
                    '#22c55e',
                    '#8b5cf6',
                    '#f97316',
                    '#14b8a6',
                    '#ef4444'
                ],
                borderWidth: 0
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
                        boxWidth: 8,
                        font: chartFont
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthlyTickets, 'month')); ?>,
            datasets: [{
                label: 'Tickets',
                data: <?= json_encode(array_column($monthlyTickets, 'total')); ?>,
                borderColor: '#3b941f',
                backgroundColor: 'rgba(177,233,111,.25)',
                tension: .4,
                fill: true,
                pointBackgroundColor: '#3b941f',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                        font: chartFont
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('organizationChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($organizationTickets, 'name')); ?>,
            datasets: [{
                label: 'Tickets',
                data: <?= json_encode(array_column($organizationTickets, 'total')); ?>,
                backgroundColor: '#6cb33f',
                borderRadius: 8,
                barThickness: 18
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
                        font: chartFont
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: chartFont
                    }
                }
            }
        }
    });
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>