<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
    .dashboard-card {
        border: none;
        border-radius: 20px;
        color: #fff;
        overflow: hidden;
    }

    .stat-number {
        font-size: 34px;
        font-weight: 700;
    }

    .stat-label {
        font-size: 14px;
        opacity: .9;
    }

    .bg-total {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .bg-open {
        background: linear-gradient(135deg, #22c55e, #15803d);
    }

    .bg-progress {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    }

    .bg-pending {
        background: linear-gradient(135deg, #ec4899, #be185d);
    }

    .bg-resolved {
        background: linear-gradient(135deg, #f97316, #c2410c);
    }

    .bg-closed {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .card-radius {
        border-radius: 20px;
    }
</style>

<div class="container-fluid mt-4">

    <h3 class="fw-bold mb-4">
        User Dashboard
    </h3>

    <div class="row g-3">

        <h3 class="fw-bold mb-1">
            Welcome,
            <?= htmlspecialchars($user['full_name']); ?>
        </h3>

        <p class="text-muted mb-4">
            <?= htmlspecialchars($user['organization_name'] ?? 'Organization'); ?>
        </p>

        <div class="col-md-2">
            <div class="card dashboard-card bg-total">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['total']; ?>
                    </div>
                    <div class="stat-label">
                        Total Tickets
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card dashboard-card bg-open">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['open_count']; ?>
                    </div>
                    <div class="stat-label">
                        Open
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card dashboard-card bg-progress">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['in_progress_count']; ?>
                    </div>
                    <div class="stat-label">
                        In Progress
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card dashboard-card bg-pending">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['pending_count']; ?>
                    </div>
                    <div class="stat-label">
                        Pending
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card dashboard-card bg-resolved">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['resolved_count']; ?>
                    </div>
                    <div class="stat-label">
                        Resolved
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card dashboard-card bg-closed">
                <div class="card-body">
                    <div class="stat-number">
                        <?= $ticketCounts['closed_count']; ?>
                    </div>
                    <div class="stat-label">
                        Closed
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-8">

            <div class="card shadow-sm border-0 card-radius">

                <div class="card-body">

                    <h5 class="fw-bold">
                        Recent Tickets
                    </h5>

                    <div class="table-responsive mt-3">

                        <table class="table">

                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Organization</th>
                                    <th>User</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($recentTickets as $ticket): ?>

                                    <tr>
                                        <td><?= htmlspecialchars($ticket['ticket_no']); ?></td>
                                        <td><?= htmlspecialchars($ticket['organization_name']); ?></td>
                                        <td><?= htmlspecialchars($ticket['customer_name']); ?></td>
                                        <td><?= htmlspecialchars($ticket['status']); ?></td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>