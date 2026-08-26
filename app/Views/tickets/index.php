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
    <?php if (!empty($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">

            <i class="bi bi-check-circle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['success']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>


    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['error']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


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

                                $createdAt = !empty($ticket['created_at'])
                                    ? (string) $ticket['created_at']
                                    : '-';

                                $closedAt = !empty($ticket['closed_at'])
                                    ? (string) $ticket['closed_at']
                                    : null;

                                $priorityLabel = ucfirst($priority);

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


                                    <td data-label="Subject">

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $subject !== ''
                                                    ? $subject
                                                    : 'Untitled Ticket'
                                            ); ?>

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


                                    <td data-label="Priority">

                                        <span class="status-badge <?= getUserTicketPriorityClass($priority); ?>">

                                            <?= htmlspecialchars($priorityLabel); ?>

                                        </span>

                                    </td>


                                    <td data-label="Status">

                                        <span class="status-badge <?= getUserTicketStatusClass($status); ?>">

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

                                        <a
                                            href="<?= BASE_URL ?>/tickets/show/<?= $ticketId; ?>"
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