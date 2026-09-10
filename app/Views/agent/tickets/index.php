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


    <?php if (!empty($unassignedCount) && $unassignedCount > 0): ?>

        <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between gap-3">

            <div class="d-flex align-items-center gap-3">

                <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">

                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                </div>

                <div>

                    <h6 class="fw-bold text-dark mb-1">

                        Attention Required: <?= $unassignedCount; ?> Unassigned Ticket<?= $unassignedCount > 1 ? 's' : ''; ?>

                    </h6>

                    <p class="text-muted small mb-0">

                        There <?= $unassignedCount === 1 ? 'is' : 'are'; ?> <strong><?= $unassignedCount; ?></strong> ticket<?= $unassignedCount > 1 ? 's' : ''; ?> in the system that <?= $unassignedCount === 1 ? 'has' : 'have'; ?> not been assigned to any support agent yet.

                    </p>

                </div>

            </div>

            <a href="<?= BASE_URL ?>/agent/tickets/all-assigned" class="btn btn-sm btn-warning text-dark font-weight-bold text-nowrap px-3 rounded-pill">

                <i class="bi bi-people-fill me-1"></i> View All Assigned

            </a>

        </div>

    <?php endif; ?>


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
                                <th>Subject</th>
                                <th>Customer</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
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

                                $statusLabel = ucwords(
                                    str_replace('_', ' ', $status)
                                );

                                ?>

                                <tr>

                                    <td data-label="Ticket">

                                        <div>
                                            <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="ticket-no-link">
                                                <?= htmlspecialchars($ticketNumber !== '' ? $ticketNumber : '-'); ?>
                                            </a>
                                            <?php if (!empty($ticket['organization_name'])): ?>
                                                <div class="ticket-org-subtext" title="<?= htmlspecialchars($ticket['organization_name']); ?>">
                                                    <?= htmlspecialchars($ticket['organization_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </td>


                                    <td data-label="Subject">

                                        <span class="ticket-subject-text" title="<?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>">
                                            <?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>
                                        </span>

                                    </td>


                                    <td data-label="Customer">

                                        <div class="fw-medium text-dark">
                                            <?= htmlspecialchars($customerName !== '' ? $customerName : '-'); ?>
                                        </div>
                                        <?php if (!empty($ticket['branch_name'])): ?>
                                            <div class="ticket-org-subtext">
                                                <i class="bi bi-building me-1"></i><?= htmlspecialchars($ticket['branch_name']); ?>
                                            </div>
                                        <?php endif; ?>

                                    </td>


                                    <td data-label="Assigned Agent">

                                         <?php if ($assignedAgentName !== ''): ?>

                                             <span class="badge-agent-pill">
                                                 <i class="bi bi-person me-1"></i>
                                                 <?= htmlspecialchars($assignedAgentName); ?>
                                             </span>

                                         <?php else: ?>

                                             <span class="badge-unassigned-pill">
                                                 <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                 Not Assigned Yet
                                             </span>

                                         <?php endif; ?>

                                    </td>


                                    <td data-label="Priority">

                                        <span class="priority-badge priority-<?= strtolower($priority); ?>">
                                            <?= htmlspecialchars(ucfirst($priority)); ?>
                                        </span>

                                    </td>


                                    <td data-label="Status">

                                        <span class="status-badge status-<?= str_replace([' ', '_'], '-', strtolower($status)); ?>">
                                            <?= htmlspecialchars($statusLabel); ?>
                                        </span>

                                    </td>


                                    <td data-label="Created">

                                        <div class="fw-semibold" style="font-size: 12px; color: #334155;">
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

                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>"
                                                class="ticket-btn-icon ticket-btn-view"
                                                title="View ticket"
                                                aria-label="View ticket">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= $ticketId; ?>"
                                                target="_blank"
                                                class="ticket-btn-icon ticket-btn-print"
                                                title="Print Ticket Report"
                                                aria-label="Print Ticket Report">

                                                <i class="bi bi-printer"></i>

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