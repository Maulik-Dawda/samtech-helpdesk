<?php
require_once ROOT_PATH . "/app/Views/layouts/header.php";

$agentName = $_SESSION['auth_user_name'] ?? 'Agent';

$ticketCounts = array_merge([
    'total' => 0,
    'open_count' => 0,
    'in_progress_count' => 0,
    'pending_count' => 0,
    'resolved_count' => 0,
    'closed_count' => 0,
], $ticketCounts ?? []);

$recentTickets = $recentTickets ?? [];
$monthlyTickets = $monthlyTickets ?? [];
$organizationTickets = $organizationTickets ?? [];

$currentHour = (int) date('H');

if ($currentHour < 12) {
    $greeting = 'Good Morning';
} elseif ($currentHour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
?>

<div class="container-fluid dashboard-wrapper">

    <!-- =====================================================
         DASHBOARD HERO
    ====================================================== -->
    <section class="dashboard-hero">

        <div class="dashboard-hero-content">

            <div class="dashboard-hero-left">

                <span class="page-badge">
                    <i class="bi bi-headset"></i>
                    Agent Workspace
                </span>

                <h1 class="dashboard-hero-title">
                    <?= htmlspecialchars($greeting); ?>,
                    <?= htmlspecialchars($agentName); ?>!
                    <span aria-hidden="true">👋</span>
                </h1>

                <p class="dashboard-hero-description">
                    Manage customer requests, monitor ticket progress and keep
                    support operations running smoothly.
                </p>

                <div class="dashboard-hero-actions">

                    <a
                        href="<?= BASE_URL ?>/agent/tickets/create"
                        class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Create Ticket
                    </a>

                    <a
                        href="<?= BASE_URL ?>/agent/tickets"
                        class="btn btn-outline">
                        <i class="bi bi-ticket-detailed"></i>
                        View Tickets
                    </a>

                </div>

            </div>

            <div class="dashboard-clock-card">

                <div class="dashboard-clock-icon">
                    <i class="bi bi-calendar3"></i>
                </div>

                <div class="dashboard-clock-content">

                    <span
                        id="currentDate"
                        class="dashboard-clock-date">
                        <?= date('D, d M Y'); ?>
                    </span>

                    <strong
                        id="currentTime"
                        class="dashboard-clock-time">
                        <?= date('h:i:s A'); ?>
                    </strong>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         TICKET STATISTICS
    ====================================================== -->
    <section class="row g-3 dashboard-statistics">

        <!-- Total Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets"
                class="stat-card-link">
                <article class="stat-card stat-card-blue">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-blue">
                            <i class="bi bi-ticket-detailed"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['total']; ?>
                    </div>

                    <div class="stat-card-label">
                        Total Tickets
                    </div>

                    <div class="stat-card-description">
                        All support requests
                    </div>

                </article>
            </a>

        </div>


        <!-- Open Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=open"
                class="stat-card-link">
                <article class="stat-card stat-card-green">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-green">
                            <i class="bi bi-folder2-open"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['open_count']; ?>
                    </div>

                    <div class="stat-card-label">
                        Open
                    </div>

                    <div class="stat-card-description">
                        New tickets waiting
                    </div>

                </article>
            </a>

        </div>


        <!-- In Progress Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=in_progress"
                class="stat-card-link">
                <article class="stat-card stat-card-purple">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-purple">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['in_progress_count']; ?>
                    </div>

                    <div class="stat-card-label">
                        In Progress
                    </div>

                    <div class="stat-card-description">
                        Currently being handled
                    </div>

                </article>
            </a>

        </div>


        <!-- Pending Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=pending"
                class="stat-card-link">
                <article class="stat-card stat-card-orange">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-orange">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['pending_count']; ?>
                    </div>

                    <div class="stat-card-label">
                        Pending
                    </div>

                    <div class="stat-card-description">
                        Awaiting an update
                    </div>

                </article>
            </a>

        </div>


        <!-- Resolved Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=resolved"
                class="stat-card-link">
                <article class="stat-card stat-card-teal">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-teal">
                            <i class="bi bi-check2-circle"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['resolved_count']; ?>
                    </div>

                    <div class="stat-card-label">
                        Resolved
                    </div>

                    <div class="stat-card-description">
                        Issues successfully solved
                    </div>

                </article>
            </a>

        </div>


        <!-- Closed Tickets -->
        <div class="col-xl-2 col-md-4 col-sm-6">

            <a
                href="<?= BASE_URL ?>/agent/tickets?status=closed"
                class="stat-card-link">
                <article class="stat-card stat-card-red">

                    <div class="stat-card-header">

                        <div class="stat-icon stat-icon-red">
                            <i class="bi bi-archive"></i>
                        </div>

                        <i class="bi bi-arrow-up-right stat-card-arrow"></i>

                    </div>

                    <div class="stat-card-value">
                        <?= (int) $ticketCounts['closed_count']; ?>
                    </div>

                    <div class="stat-card-label">
                        Closed
                    </div>

                    <div class="stat-card-description">
                        Completed and archived
                    </div>

                </article>
            </a>

        </div>

    </section>


    <!-- =====================================================
         DASHBOARD CHARTS
    ====================================================== -->
    <section class="row g-3 dashboard-section">

        <!-- Status Chart -->
        <div class="col-xl-4 col-lg-6">

            <article class="content-card dashboard-chart-card h-100">

                <div class="content-card-header">

                    <div>

                        <h2 class="content-card-title">
                            <i class="bi bi-pie-chart"></i>
                            Tickets by Status
                        </h2>

                        <p class="content-card-subtitle">
                            Current ticket distribution
                        </p>

                    </div>

                </div>

                <div class="content-card-body">

                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>

                </div>

            </article>

        </div>


        <!-- Monthly Chart -->
        <div class="col-xl-4 col-lg-6">

            <article class="content-card dashboard-chart-card h-100">

                <div class="content-card-header">

                    <div>

                        <h2 class="content-card-title">
                            <i class="bi bi-graph-up-arrow"></i>
                            Tickets Over Time
                        </h2>

                        <p class="content-card-subtitle">
                            Monthly support volume
                        </p>

                    </div>

                </div>

                <div class="content-card-body">

                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>

                </div>

            </article>

        </div>


        <!-- Organization Chart -->
        <div class="col-xl-4 col-lg-12">

            <article class="content-card dashboard-chart-card h-100">

                <div class="content-card-header">

                    <div>

                        <h2 class="content-card-title">
                            <i class="bi bi-building"></i>
                            Tickets by Organization
                        </h2>

                        <p class="content-card-subtitle">
                            Organizations with support requests
                        </p>

                    </div>

                </div>

                <div class="content-card-body">

                    <div class="chart-container">
                        <canvas id="organizationChart"></canvas>
                    </div>

                </div>

            </article>

        </div>

    </section>


    <!-- =====================================================
         RECENT TICKETS AND QUICK ACTIONS
    ====================================================== -->
    <section class="row g-3 dashboard-section">

        <!-- Recent Tickets -->
        <div class="col-xl-9 col-lg-8">

            <article class="content-card">

                <div class="content-card-header">

                    <div>

                        <h2 class="content-card-title">
                            <i class="bi bi-list-task"></i>
                            Recent Tickets
                        </h2>

                        <p class="content-card-subtitle">
                            Latest support requests across organizations
                        </p>

                    </div>

                    <a
                        href="<?= BASE_URL ?>/agent/tickets"
                        class="btn btn-outline btn-sm">
                        View All Tickets
                        <i class="bi bi-chevron-right"></i>
                    </a>

                </div>

                <div class="table-responsive">

                    <table class="table data-table align-middle">

                        <thead>

                            <tr>
                                <th>Ticket</th>
                                <th>Organization</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($recentTickets)): ?>

                                <tr>

                                    <td colspan="6">

                                        <div class="empty-state">

                                            <div class="empty-state-icon">
                                                <i class="bi bi-inbox"></i>
                                            </div>

                                            <h3 class="empty-state-title">
                                                No recent tickets
                                            </h3>

                                            <p class="empty-state-text">
                                                New support tickets will appear here.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($recentTickets as $ticket): ?>

                                    <?php
                                    $ticketStatus = $ticket['status'] ?? 'open';

                                    $statusClass = match ($ticketStatus) {
                                        'open' => 'badge-success',
                                        'in_progress' => 'badge-purple',
                                        'pending' => 'badge-warning',
                                        'resolved' => 'badge-info',
                                        'closed' => 'badge-danger',
                                        default => 'badge-success'
                                    };

                                    $ticketId = $ticket['id'] ?? '';

                                    $ticketUrl = BASE_URL
                                        . '/agent/tickets/show/'
                                        . urlencode((string) $ticketId);
                                    ?>

                                    <tr>

                                        <td>

                                            <a
                                                href="<?= htmlspecialchars($ticketUrl); ?>"
                                                class="table-primary-link">
                                                <?= htmlspecialchars(
                                                    $ticket['ticket_no'] ?? '-'
                                                ); ?>
                                            </a>

                                            <?php if (!empty($ticket['subject'])): ?>

                                                <div class="table-secondary-text">
                                                    <?= htmlspecialchars(
                                                        $ticket['subject']
                                                    ); ?>
                                                </div>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <span class="table-primary-text">
                                                <?= htmlspecialchars(
                                                    $ticket['organization_name'] ?? '-'
                                                ); ?>
                                            </span>

                                        </td>

                                        <td>

                                            <div class="table-user">

                                                <span class="table-user-avatar">
                                                    <?= htmlspecialchars(
                                                        strtoupper(
                                                            substr(
                                                                $ticket['customer_name'] ?? 'U',
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    ); ?>
                                                </span>

                                                <span class="table-user-name">
                                                    <?= htmlspecialchars(
                                                        $ticket['customer_name'] ?? '-'
                                                    ); ?>
                                                </span>

                                            </div>

                                        </td>

                                        <td>

                                            <span class="status-badge <?= $statusClass; ?>">

                                                <span class="status-indicator"></span>

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

                                        <td>

                                            <span class="table-date">
                                                <?= htmlspecialchars(
                                                    $ticket['created_at'] ?? '-'
                                                ); ?>
                                            </span>

                                        </td>

                                        <td class="text-end">

                                            <a
                                                href="<?= htmlspecialchars($ticketUrl); ?>"
                                                class="table-action-button"
                                                title="View Ticket"
                                                aria-label="View ticket <?= htmlspecialchars(
                                                                            $ticket['ticket_no'] ?? ''
                                                                        ); ?>">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </article>

        </div>


        <!-- Quick Actions -->
        <div class="col-xl-3 col-lg-4">

            <article class="content-card quick-actions-card h-100">

                <div class="content-card-header">

                    <div>

                        <h2 class="content-card-title">
                            <i class="bi bi-lightning-charge"></i>
                            Quick Actions
                        </h2>

                        <p class="content-card-subtitle">
                            Common agent tasks
                        </p>

                    </div>

                </div>

                <div class="content-card-body">

                    <div class="quick-actions-list">

                        <a
                            href="<?= BASE_URL ?>/agent/tickets/create"
                            class="quick-action-item">

                            <span class="quick-action-icon">
                                <i class="bi bi-plus-circle"></i>
                            </span>

                            <span class="quick-action-details">

                                <strong class="quick-action-title">
                                    Create Ticket
                                </strong>

                                <small class="quick-action-description">
                                    Open a new support request
                                </small>

                            </span>

                            <i class="bi bi-chevron-right quick-action-arrow"></i>

                        </a>


                        <a
                            href="<?= BASE_URL ?>/agent/tickets"
                            class="quick-action-item">

                            <span class="quick-action-icon">
                                <i class="bi bi-ticket-detailed"></i>
                            </span>

                            <span class="quick-action-details">

                                <strong class="quick-action-title">
                                    View Tickets
                                </strong>

                                <small class="quick-action-description">
                                    Review the support queue
                                </small>

                            </span>

                            <i class="bi bi-chevron-right quick-action-arrow"></i>

                        </a>


                        <a
                            href="<?= BASE_URL ?>/organizations/create"
                            class="quick-action-item">

                            <span class="quick-action-icon">
                                <i class="bi bi-building-add"></i>
                            </span>

                            <span class="quick-action-details">

                                <strong class="quick-action-title">
                                    Create Organization
                                </strong>

                                <small class="quick-action-description">
                                    Add a customer organization
                                </small>

                            </span>

                            <i class="bi bi-chevron-right quick-action-arrow"></i>

                        </a>


                        <a
                            href="<?= BASE_URL ?>/agent/users/create"
                            class="quick-action-item">

                            <span class="quick-action-icon">
                                <i class="bi bi-person-plus"></i>
                            </span>

                            <span class="quick-action-details">

                                <strong class="quick-action-title">
                                    Create User
                                </strong>

                                <small class="quick-action-description">
                                    Add a user to an organization
                                </small>

                            </span>

                            <i class="bi bi-chevron-right quick-action-arrow"></i>

                        </a>

                    </div>

                </div>

            </article>

        </div>

    </section>

</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* =====================================================
           LIVE DATE AND TIME
        ====================================================== */
        const currentDateElement = document.getElementById('currentDate');
        const currentTimeElement = document.getElementById('currentTime');

        function updateDashboardClock() {
            const now = new Date();

            if (currentDateElement) {
                currentDateElement.textContent =
                    new Intl.DateTimeFormat('en-GB', {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }).format(now);
            }

            if (currentTimeElement) {
                currentTimeElement.textContent =
                    new Intl.DateTimeFormat('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    }).format(now);
            }
        }

        updateDashboardClock();
        window.setInterval(updateDashboardClock, 1000);


        /* =====================================================
           CHART CONFIGURATION
        ====================================================== */
        if (typeof Chart === 'undefined') {
            return;
        }

        Chart.defaults.font.family =
            "'Inter', 'Segoe UI', Arial, sans-serif";

        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#64748b';


        /* =====================================================
           TICKETS BY STATUS CHART
        ====================================================== */
        const statusChartElement =
            document.getElementById('statusChart');

        if (statusChartElement) {

            new Chart(statusChartElement, {

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
                            '#22c55e',
                            '#8b5cf6',
                            '#f97316',
                            '#14b8a6',
                            '#ef4444'
                        ],

                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 6

                    }]

                },

                options: {

                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',

                    plugins: {

                        legend: {

                            position: 'bottom',

                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16
                            }

                        },

                        tooltip: {
                            displayColors: false
                        }

                    }

                }

            });

        }


        /* =====================================================
           MONTHLY TICKETS CHART
        ====================================================== */
        const monthlyChartElement =
            document.getElementById('monthlyChart');

        if (monthlyChartElement) {

            const monthlyContext =
                monthlyChartElement.getContext('2d');

            const monthlyGradient =
                monthlyContext.createLinearGradient(0, 0, 0, 260);

            monthlyGradient.addColorStop(
                0,
                'rgba(108, 179, 63, 0.30)'
            );

            monthlyGradient.addColorStop(
                1,
                'rgba(108, 179, 63, 0.02)'
            );

            new Chart(monthlyChartElement, {

                type: 'line',

                data: {

                    labels: <?= json_encode(
                                array_column($monthlyTickets, 'month')
                            ); ?>,

                    datasets: [{

                        label: 'Tickets',

                        data: <?= json_encode(
                                    array_map(
                                        'intval',
                                        array_column($monthlyTickets, 'total')
                                    )
                                ); ?>,

                        borderColor: '#5b9f35',
                        backgroundColor: monthlyGradient,
                        borderWidth: 3,
                        tension: 0.38,
                        fill: true,

                        pointBackgroundColor: '#5b9f35',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
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

                            border: {
                                display: false
                            },

                            grid: {
                                display: false
                            }

                        },

                        y: {

                            beginAtZero: true,

                            border: {
                                display: false
                            },

                            grid: {
                                color: '#eef2f7',
                                drawTicks: false
                            },

                            ticks: {
                                precision: 0,
                                padding: 10
                            }

                        }

                    }

                }

            });

        }


        /* =====================================================
           ORGANIZATION TICKETS CHART
        ====================================================== */
        const organizationChartElement =
            document.getElementById('organizationChart');

        if (organizationChartElement) {

            new Chart(organizationChartElement, {

                type: 'bar',

                data: {

                    labels: <?= json_encode(
                                array_column($organizationTickets, 'name')
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
                        hoverBackgroundColor: '#5b9f35',
                        borderRadius: 8,
                        borderSkipped: false,
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

                            border: {
                                display: false
                            },

                            grid: {
                                color: '#eef2f7',
                                drawTicks: false
                            },

                            ticks: {
                                precision: 0,
                                padding: 8
                            }

                        },

                        y: {

                            border: {
                                display: false
                            },

                            grid: {
                                display: false
                            }

                        }

                    }

                }

            });

        }

    });
</script>

<?php
require_once ROOT_PATH . "/app/Views/layouts/footer.php";
?>