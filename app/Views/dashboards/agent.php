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

$recentTickets = is_array($recentTickets ?? null)
    ? array_slice($recentTickets, 0, 5)
    : [];

$monthlyTickets = is_array($monthlyTickets ?? null)
    ? $monthlyTickets
    : [];

$organizationTickets = is_array($organizationTickets ?? null)
    ? $organizationTickets
    : [];

$agentName = $_SESSION['auth_user_name'] ?? 'Agent';

$greeting = DateTimeHelper::greeting();
$currentDateTime = DateTimeHelper::now();

$currentDate = $currentDateTime->format('l, d F Y');
$currentTime = $currentDateTime->format('h:i A');

/*
|--------------------------------------------------------------------------
| Dashboard Helper Functions
|--------------------------------------------------------------------------
*/

function agentDashboardStatusClass(string $status): string
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

function agentDashboardPriorityClass(string $priority): string
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
         DASHBOARD PAGE HEADER (Matching Mockup)
    ========================================================== -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark">Dashboard</h1>
            <p class="text-muted small mb-0">Welcome back, <?= htmlspecialchars($agentName); ?>. Here is your support operations overview.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light btn-sm border dropdown-toggle px-3 py-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($currentDate); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item small" href="#">Today</a></li>
                    <li><a class="dropdown-item small" href="#">This Week</a></li>
                    <li><a class="dropdown-item small" href="#">This Month</a></li>
                </ul>
            </div>
            <a href="<?= BASE_URL ?>/agent/tickets/create" class="btn btn-primary-custom btn-sm px-3 py-2">
                <i class="bi bi-plus-lg me-1"></i> New Ticket
            </a>
        </div>
    </div>

    <!-- =========================================================
         TOP TICKET METRICS ROW (Matching 5-Card Mockup Row)
    ========================================================== -->
    <div class="row g-3 mb-4">
        <!-- Total Tickets -->
        <div class="col">
            <a href="<?= BASE_URL ?>/agent/tickets" class="metric-card d-block text-decoration-none h-100">
                <div class="metric-card-header mb-2">
                    <span class="text-muted small fw-bold">Total Tickets</span>
                    <span class="metric-card-icon"><i class="bi bi-ticket-perforated"></i></span>
                </div>
                <div class="d-flex align-items-baseline justify-content-between">
                    <span class="metric-card-value me-2"><?= (int) $ticketCounts['total']; ?></span>
                    <span class="metric-card-meta positive"><i class="bi bi-arrow-up-short"></i> +7.4% ↑</span>
                </div>
            </a>
        </div>

        <!-- Open Tickets -->
        <div class="col">
            <a href="<?= BASE_URL ?>/agent/tickets?status=open" class="metric-card d-block text-decoration-none h-100">
                <div class="metric-card-header mb-2">
                    <span class="text-muted small fw-bold">Open Tickets</span>
                    <span class="metric-card-icon"><i class="bi bi-folder2-open"></i></span>
                </div>
                <div class="d-flex align-items-baseline justify-content-between">
                    <span class="metric-card-value me-2"><?= (int) $ticketCounts['open_count']; ?></span>
                    <span class="metric-card-meta positive"><i class="bi bi-arrow-up-short"></i> +2% ↑</span>
                </div>
            </a>
        </div>

        <!-- In Progress -->
        <div class="col">
            <a href="<?= BASE_URL ?>/agent/tickets?status=in_progress" class="metric-card d-block text-decoration-none h-100">
                <div class="metric-card-header mb-2">
                    <span class="text-muted small fw-bold">In Progress</span>
                    <span class="metric-card-icon"><i class="bi bi-clock-history"></i></span>
                </div>
                <div class="d-flex align-items-baseline justify-content-between">
                    <span class="metric-card-value me-2"><?= (int) $ticketCounts['in_progress_count']; ?></span>
                    <span class="metric-card-meta positive"><i class="bi bi-arrow-up-short"></i> +3.5% ↑</span>
                </div>
            </a>
        </div>

        <!-- Pending Tickets -->
        <div class="col">
            <a href="<?= BASE_URL ?>/agent/tickets?status=pending" class="metric-card d-block text-decoration-none h-100">
                <div class="metric-card-header mb-2">
                    <span class="text-muted small fw-bold">Pending</span>
                    <span class="metric-card-icon"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="d-flex align-items-baseline justify-content-between">
                    <span class="metric-card-value me-2"><?= (int) $ticketCounts['pending_count']; ?></span>
                    <span class="metric-card-meta warning"><i class="bi bi-dash-short"></i> 0%</span>
                </div>
            </a>
        </div>

        <!-- Resolved Tickets -->
        <div class="col">
            <a href="<?= BASE_URL ?>/agent/tickets?status=resolved" class="metric-card d-block text-decoration-none h-100">
                <div class="metric-card-header mb-2">
                    <span class="text-muted small fw-bold">Resolved</span>
                    <span class="metric-card-icon"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="d-flex align-items-baseline justify-content-between">
                    <span class="metric-card-value me-2"><?= (int) $ticketCounts['resolved_count']; ?></span>
                    <span class="metric-card-meta positive"><i class="bi bi-arrow-up-short"></i> +12% ↑</span>
                </div>
            </a>
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
                    Showing the five most recently created support tickets.
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
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>"
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

                                    <td data-label="Priority">

                                        <span class="priority-badge <?= agentDashboardPriorityClass($ticketPriority); ?>">

                                            <?= htmlspecialchars(
                                                ucfirst($ticketPriority)
                                            ); ?>

                                        </span>

                                    </td>

                                    <td data-label="Status">

                                        <span class="status-badge <?= agentDashboardStatusClass($ticketStatus); ?>">

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


    <!-- =========================================================
         DASHBOARD CHARTS
    ========================================================== -->
    <section class="content-section">

        <div class="row g-3">

            <!-- Tickets by Status -->
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


            <!-- Tickets Over Time -->
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


            <!-- Tickets by Organization -->
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

</div>


<!-- =========================================================
     DASHBOARD CHARTS SCRIPT
========================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded.');
        return;
    }

    const chartFont = {
        family: "'Inter', Arial, sans-serif",
        size: 11
    };


    /*
    |--------------------------------------------------------------------------
    | Tickets by Status
    |--------------------------------------------------------------------------
    */

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
                        <?= (int) $ticketCounts['open_count']; ?>,
                        <?= (int) $ticketCounts['in_progress_count']; ?>,
                        <?= (int) $ticketCounts['pending_count']; ?>,
                        <?= (int) $ticketCounts['resolved_count']; ?>,
                        <?= (int) $ticketCounts['closed_count']; ?>
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


    /*
    |--------------------------------------------------------------------------
    | Tickets Over Time
    |--------------------------------------------------------------------------
    */

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
                    tension: 0.4,
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


    /*
    |--------------------------------------------------------------------------
    | Tickets by Organization
    |--------------------------------------------------------------------------
    */

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