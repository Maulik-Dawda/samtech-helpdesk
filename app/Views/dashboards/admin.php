<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>

.dashboard-card{
    border:none;
    border-radius:20px;
    color:#fff;
    overflow:hidden;
}

.stat-number{
    font-size:34px;
    font-weight:700;
}

.stat-label{
    font-size:14px;
    opacity:.9;
}

.bg-total{
    background:linear-gradient(135deg,#3b82f6,#1d4ed8);
}

.bg-open{
    background:linear-gradient(135deg,#22c55e,#15803d);
}

.bg-progress{
    background:linear-gradient(135deg,#8b5cf6,#6d28d9);
}

.bg-pending{
    background:linear-gradient(135deg,#ec4899,#be185d);
}

.bg-resolved{
    background:linear-gradient(135deg,#f97316,#c2410c);
}

.bg-closed{
    background:linear-gradient(135deg,#ef4444,#b91c1c);
}

.card-radius{
    border-radius:20px;
}

</style>

<div class="container-fluid mt-4">

    <h3 class="fw-bold mb-4">
        Admin Dashboard
    </h3>

    

    <div class="row g-3">

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

        <div class="row mt-4">

    <div class="col-lg-6">

        <div class="card border-0 shadow-sm card-radius">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Tickets By Status
                </h5>

                <canvas id="statusChart" height="160"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card border-0 shadow-sm card-radius">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Monthly Ticket Trend
                </h5>

                <canvas id="monthlyChart" height="160"></canvas>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-lg-12">

        <div class="card border-0 shadow-sm card-radius">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Tickets By Organization
                </h5>

                <canvas id="organizationChart" height="110"></canvas>

            </div>

        </div>

    </div>

</div>

    </div>

    <div class="row mt-4">

        <div class="col-md-4">

            <div class="card shadow-sm border-0 card-radius">

                <div class="card-body">

                    <h5 class="fw-bold">
                        System Summary
                    </h5>

                    <hr>

                    <p>
                        <strong>Organizations:</strong>
                        <?= $organizationCount; ?>
                    </p>

                    <p>
                        <strong>Admins:</strong>
                        <?= $userCounts['admins']; ?>
                    </p>

                    <p>
                        <strong>Agents:</strong>
                        <?= $userCounts['agents']; ?>
                    </p>

                    <p>
                        <strong>Users:</strong>
                        <?= $userCounts['users']; ?>
                    </p>

                </div>

            </div>

        </div>

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

                                <?php foreach($recentTickets as $ticket): ?>

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

    <div class="card shadow-sm border-0 card-radius mt-4">

    <div class="card-body">

        <h5 class="fw-bold">
            Recent Activity
        </h5>

        <hr>

        <?php if(empty($recentActivities)): ?>

            <div class="text-muted">
                No recent activity found.
            </div>

        <?php else: ?>

            <?php foreach($recentActivities as $activity): ?>

                <div class="border-bottom py-2">

                    <div class="fw-semibold">
                        <?= htmlspecialchars($activity['action']); ?>
                    </div>

                    <small class="text-muted">
                        <?= htmlspecialchars($activity['full_name'] ?? 'System'); ?>
                        —
                        <?= htmlspecialchars($activity['created_at']); ?>
                    </small>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</div>

<script>
const statusData = {
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
            '#ec4899',
            '#f97316',
            '#ef4444'
        ]
    }]
};

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: statusData
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyTickets, 'month')); ?>,
        datasets: [{
            label: 'Tickets',
            data: <?= json_encode(array_column($monthlyTickets, 'total')); ?>,
            borderColor: '#111827',
            backgroundColor: 'rgba(177,233,111,.25)',
            tension: .4,
            fill: true
        }]
    }
});

new Chart(document.getElementById('organizationChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($organizationTickets, 'name')); ?>,
        datasets: [{
            label: 'Tickets',
            data: <?= json_encode(array_column($organizationTickets, 'total')); ?>,
            backgroundColor: '#b1e96f'
        }]
    },
    options: {
        indexAxis: 'y'
    }
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>