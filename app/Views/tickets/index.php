<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$tickets = is_array($tickets ?? null)
    ? $tickets
    : [];

$totalRecords = (int) ($totalRecords ?? count($tickets));
$currentPageCount = count($tickets);

/**
 * Return the shared priority badge class.
 */
function getUserTicketPriorityClass(string $priority): string
{
    return match (strtolower($priority)) {
        'low' => 'status-open',
        'medium' => 'status-progress',
        'high' => 'status-pending',
        'urgent' => 'status-closed',
        default => 'status-open',
    };
}

/**
 * Return the shared status badge class.
 */
function getUserTicketStatusClass(string $status): string
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<div class="container-fluid px-0">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->
    <section class="ui-panel mb-3">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-2">

                        <i class="bi bi-ticket-perforated-fill"></i>

                        Support Tickets

                    </div>

                    <h1 class="page-title">
                        My Tickets
                    </h1>

                    <p class="page-description">
                        View and track all support tickets created under your account.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/tickets/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Create Ticket

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         FLASH MESSAGES
    ========================================================== -->



    <!-- =========================================================
         TICKET DIRECTORY
    ========================================================== -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    Ticket Directory
                </div>

                <div class="table-card-subtitle">
                    Review your ticket subjects, priorities, statuses and dates.
                </div>

            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">

                <?php if (!empty($tickets)): ?>

                    <div class="ticket-search-wrapper">

                        <i class="bi bi-search ticket-search-icon"></i>

                        <input
                            type="search"
                            id="userTicketSearch"
                            class="form-control ticket-search-input"
                            placeholder="Search tickets..."
                            autocomplete="off"
                            aria-label="Search tickets">

                    </div>

                <?php endif; ?>

                <div class="app-badge app-badge-primary">

                    <i class="bi bi-ticket-detailed"></i>

                    <span id="userTicketResultCount">
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
                        You have not created any support tickets yet.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/tickets/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Create Your First Ticket

                    </a>

                </div>

            <?php else: ?>

                <div
                    class="table-responsive"
                    id="userTicketTableWrapper">

                    <table
                        class="table"
                        id="userTicketTable">

                        <thead>

                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Closed</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody id="userTicketTableBody">

                            <?php foreach ($tickets as $ticket): ?>

                                <?php

                                $ticketId = (int) ($ticket['id'] ?? 0);

                                $ticketNumber = trim(
                                    (string) ($ticket['ticket_no'] ?? '')
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
                                            <a href="<?= BASE_URL ?>/tickets/show/<?= $ticketId; ?>" class="ticket-no-link">
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


                                    <td data-label="Closed">

                                        <?php if (!empty($ticket['closed_at'])): ?>
                                            <div class="fw-semibold" style="font-size: 12px; color: #334155;">
                                                <?= htmlspecialchars(
                                                    DateTimeHelper::format(
                                                        $ticket['closed_at'],
                                                        'd M Y'
                                                    )
                                                ); ?>
                                            </div>

                                            <div class="text-muted small mt-1">
                                                <?= htmlspecialchars(
                                                    DateTimeHelper::format(
                                                        $ticket['closed_at'],
                                                        'h:i A'
                                                    )
                                                ); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>

                                    </td>


                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/tickets/show/<?= $ticketId; ?>"
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

                </div>


                <!-- Search Empty State -->
                <div
                    id="userTicketNoResults"
                    class="empty-state d-none">

                    <div class="empty-state-icon">

                        <i class="bi bi-search"></i>

                    </div>

                    <h3 class="empty-state-title">
                        No matching tickets
                    </h3>

                    <p class="empty-state-description">
                        No tickets match your search. Try another ticket number,
                        subject, priority, status or date.
                    </p>

                    <button
                        type="button"
                        class="btn btn-light"
                        id="clearUserTicketSearch">

                        <i class="bi bi-x-circle me-2"></i>

                        Clear Search

                    </button>

                </div>


                <div id="userTicketPagination">

                    <?php
                    require ROOT_PATH . "/app/Views/partials/pagination.php";
                    ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('userTicketSearch');
    const tableWrapper = document.getElementById('userTicketTableWrapper');
    const tableBody = document.getElementById('userTicketTableBody');
    const noResults = document.getElementById('userTicketNoResults');
    const clearButton = document.getElementById('clearUserTicketSearch');
    const resultCount = document.getElementById('userTicketResultCount');
    const pagination = document.getElementById('userTicketPagination');

    if (!searchInput || !tableBody) {
        return;
    }

    const rows = Array.from(
        tableBody.querySelectorAll('tr')
    );

    const currentPageTotal = rows.length;

    function filterUserTickets() {

        const searchValue = searchInput.value
            .trim()
            .toLowerCase();

        let visibleRows = 0;

        rows.forEach(function (row) {

            const searchableText = row.textContent
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();

            const isMatch =
                searchValue === '' ||
                searchableText.includes(searchValue);

            row.classList.toggle('d-none', !isMatch);

            if (isMatch) {
                visibleRows++;
            }

        });

        if (tableWrapper) {
            tableWrapper.classList.toggle(
                'd-none',
                visibleRows === 0
            );
        }

        if (noResults) {
            noResults.classList.toggle(
                'd-none',
                visibleRows !== 0
            );
        }

        if (pagination) {
            pagination.classList.toggle(
                'd-none',
                searchValue !== ''
            );
        }

        if (resultCount) {

            if (searchValue === '') {

                resultCount.textContent =
                    'Showing ' +
                    currentPageTotal +
                    ' of <?= $totalRecords; ?>';

            } else {

                resultCount.textContent =
                    visibleRows +
                    ' matching ticket' +
                    (visibleRows === 1 ? '' : 's');

            }

        }

    }

    searchInput.addEventListener(
        'input',
        filterUserTickets
    );

    if (clearButton) {

        clearButton.addEventListener('click', function () {

            searchInput.value = '';

            filterUserTickets();

            searchInput.focus();

        });

    }

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>