<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$tickets = is_array($tickets ?? null)
    ? $tickets
    : [];

$totalRecords = (int) ($totalRecords ?? count($tickets));
$currentPageCount = count($tickets);

$totalOpen = 0;
$totalInProgress = 0;
$totalPending = 0;
$totalResolved = 0;
$totalClosed = 0;

foreach ($tickets as $ticket) {
    $ticketStatus = strtolower((string) ($ticket['status'] ?? ''));

    match ($ticketStatus) {
        'open' => $totalOpen++,
        'in_progress' => $totalInProgress++,
        'pending' => $totalPending++,
        'resolved' => $totalResolved++,
        'closed' => $totalClosed++,
        default => null,
    };
}

/**
 * Returns the shared status badge class.
 */
function getAgentTicketStatusClass(string $status): string
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
 * Returns the shared priority badge class.
 */
function getAgentTicketPriorityClass(string $priority): string
{
    return match (strtolower($priority)) {
        'low' => 'status-open',
        'medium' => 'status-progress',
        'high' => 'status-pending',
        'urgent' => 'status-closed',
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

                        <i class="bi bi-ticket-perforated-fill"></i>

                        Ticket Management

                    </div>

                    <h1 class="page-title">
                        All Tickets
                    </h1>

                    <p class="page-description">
                        View, monitor and manage all customer support tickets.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/agent/tickets/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Create Ticket

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         CURRENT PAGE METRICS
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
                    <?= $totalRecords; ?>
                </div>

                <div class="metric-card-meta">

                    <i class="bi bi-list-ul"></i>

                    <?= $currentPageCount; ?> shown on this page

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
                    <?= $totalOpen; ?>
                </div>

                <div class="metric-card-meta">
                    Current page
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
                    <?= $totalInProgress; ?>
                </div>

                <div class="metric-card-meta">
                    Current page
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
                    <?= $totalPending; ?>
                </div>

                <div class="metric-card-meta">
                    Current page
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
                    <?= $totalResolved; ?>
                </div>

                <div class="metric-card-meta">
                    Current page
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
                    <?= $totalClosed; ?>
                </div>

                <div class="metric-card-meta">
                    Current page
                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         TICKET TABLE
    ========================================================== -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    Ticket Directory
                </div>

                <div class="table-card-subtitle">
                    Review ticket details, priority, status and creation dates.
                </div>

            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">

                <form method="GET" action="<?= BASE_URL ?>/agent/tickets" class="ticket-search-wrapper position-relative">

                    <i class="bi bi-search ticket-search-icon"></i>

                    <input
                        type="search"
                        name="search"
                        id="ticketSearch"
                        class="form-control ticket-search-input"
                        placeholder="Search tickets..."
                        value="<?= htmlspecialchars($search ?? ''); ?>"
                        autocomplete="off"
                        aria-label="Search tickets">

                </form>

                <div class="app-badge app-badge-primary">

                    <i class="bi bi-ticket-detailed"></i>

                    <span id="ticketResultCount">
                        Showing <?= $currentPageCount; ?> of <?= $totalRecords; ?>
                    </span>

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
                        No tickets found
                    </h3>

                    <p class="empty-state-description">
                        No support tickets are currently available.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/agent/tickets/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Create Ticket

                    </a>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table" id="ticketTable">

                        <thead>

                            <tr>
                                <th>Ticket</th>
                                <th>Customer</th>
                                <th>Assigned Agent</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Closed</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody id="ticketTableBody">

                            <?php foreach ($tickets as $ticket): ?>

                                <?php

                                $ticketId = (int) ($ticket['id'] ?? 0);

                                $ticketNumber = trim(
                                    (string) ($ticket['ticket_no'] ?? '')
                                );

                                $customerName = trim(
                                    (string) ($ticket['customer_name'] ?? '')
                                );

                                $assignedAgentName = trim(
                                    (string) ($ticket['assigned_agent_name'] ?? '')
                                );

                                $subject = trim(
                                    (string) ($ticket['subject'] ?? '')
                                );

                                $priority = strtolower(
                                    (string) ($ticket['priority'] ?? 'low')
                                );

                                $status = strtolower(
                                    (string) ($ticket['status'] ?? 'open')
                                );

                                $createdAt = !empty($ticket['created_at'])
                                    ? (string) $ticket['created_at']
                                    : '-';

                                $closedAt = !empty($ticket['closed_at'])
                                    ? (string) $ticket['closed_at']
                                    : null;

                                $statusLabel = ucwords(
                                    str_replace('_', ' ', $status)
                                );

                                ?>

                                <tr>

                                    <td data-label="Ticket">

                                        <div class="fw-semibold">
                                            <?= htmlspecialchars(
                                                $ticketNumber !== ''
                                                    ? $ticketNumber
                                                    : '-'
                                            ); ?>
                                        </div>

                                    </td>


                                    <td data-label="Customer">

                                        <div class="d-flex align-items-center gap-2">

                                            <div class="table-avatar">

                                                <?= htmlspecialchars(
                                                    strtoupper(
                                                        substr(
                                                            $customerName !== ''
                                                                ? $customerName
                                                                : 'C',
                                                            0,
                                                            1
                                                        )
                                                    )
                                                ); ?>

                                            </div>

                                            <div class="fw-semibold">

                                                <?= htmlspecialchars(
                                                    $customerName !== ''
                                                        ? $customerName
                                                        : 'Unknown Customer'
                                                ); ?>

                                            </div>

                                        </div>

                                    </td>


                                    <td data-label="Assigned Agent">

                                        <?php if ($assignedAgentName !== ''): ?>

                                            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600;">

                                                <i class="bi bi-person-badge me-1"></i>

                                                <?= htmlspecialchars($assignedAgentName); ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted small">

                                                Unassigned

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td data-label="Subject">

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $subject !== ''
                                                    ? $subject
                                                    : 'Untitled Ticket'
                                            ); ?>

                                        </div>

                                    </td>


                                    <td data-label="Priority">

                                        <span class="status-badge <?= getAgentTicketPriorityClass($priority); ?>">

                                            <?= htmlspecialchars(
                                                ucfirst($priority)
                                            ); ?>

                                        </span>

                                    </td>


                                    <td data-label="Status">

                                        <span class="status-badge <?= getAgentTicketStatusClass($status); ?>">

                                            <?= htmlspecialchars($statusLabel); ?>

                                        </span>

                                    </td>


                                    <td data-label="Created">

                                        <div class="text-nowrap">
                                            <?= htmlspecialchars($createdAt); ?>
                                        </div>

                                    </td>


                                    <td data-label="Closed">

                                        <?php if ($closedAt !== null): ?>

                                            <div class="text-nowrap">
                                                <?= htmlspecialchars($closedAt); ?>
                                            </div>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>"
                                                class="table-action-btn table-action-view"
                                                title="View ticket"
                                                aria-label="View ticket">

                                                <i class="bi bi-eye-fill"></i>

                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= $ticketId; ?>"
                                                target="_blank"
                                                class="table-action-btn table-action-view text-success"
                                                style="background: #e8f5e9; color: #2e7d32;"
                                                title="Print Ticket Report"
                                                aria-label="Print Ticket Report">

                                                <i class="bi bi-printer-fill"></i>

                                            </a>
                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>
                    <div
                        id="noSearchResults"
                        class="empty-state d-none">

                        <div class="empty-state-icon">
                            <i class="bi bi-search"></i>
                        </div>

                        <h3 class="empty-state-title">
                            No matching tickets
                        </h3>

                        <p class="empty-state-description">
                            Try searching with a different ticket number, customer, subject,
                            priority or status.
                        </p>

                        <button
                            type="button"
                            class="btn btn-light"
                            id="clearTicketSearch">

                            <i class="bi bi-x-circle me-2"></i>
                            Clear Search

                        </button>

                    </div>

                </div>


                <?php
                require ROOT_PATH . "/app/Views/partials/pagination.php";
                ?>

            <?php endif; ?>

        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('ticketSearch');
    const table = document.getElementById('ticketTable');
    const tableBody = document.getElementById('ticketTableBody');
    const noResults = document.getElementById('noSearchResults');
    const clearButton = document.getElementById('clearTicketSearch');
    const resultCount = document.getElementById('ticketResultCount');

    if (!searchInput || !table || !tableBody) {
        return;
    }

    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const totalRows = rows.length;

    function filterTickets() {

        const searchValue = searchInput.value
            .trim()
            .toLowerCase();

        let visibleRows = 0;

        rows.forEach(function (row) {

            const rowText = row.textContent
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();

            const isVisible =
                searchValue === '' ||
                rowText.includes(searchValue);

            row.style.display = isVisible ? '' : 'none';

            if (isVisible) {
                visibleRows++;
            }

        });

        const tableWrapper = table.closest('.table-responsive');
        if (tableWrapper) {
            tableWrapper.classList.toggle('d-none', visibleRows === 0);
        }

        if (noResults) {
            noResults.classList.toggle('d-none', visibleRows !== 0);
        }

        if (resultCount) {

            if (searchValue === '') {
                resultCount.textContent =
                    'Showing ' + totalRows + ' of <?= $totalRecords; ?>';
            } else {
                resultCount.textContent =
                    visibleRows + ' matching ticket' +
                    (visibleRows === 1 ? '' : 's');
            }

        }

    }

    searchInput.addEventListener('input', filterTickets);

    if (clearButton) {

        clearButton.addEventListener('click', function () {

            window.location.href = '<?= BASE_URL ?>/agent/tickets';

        });

    }

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>