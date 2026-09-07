<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$targetAgent = is_array($targetAgent ?? null) ? $targetAgent : [];
$tickets = is_array($tickets ?? null) ? $tickets : [];
$totalRecords = (int) ($totalRecords ?? count($tickets));
$currentPageCount = count($tickets);

$search = (string)($search ?? '');
$status = (string)($status ?? '');
$priority = (string)($priority ?? '');

$totalOpen = 0;
$totalInProgress = 0;
$totalPending = 0;
$totalResolved = 0;
$totalClosed = 0;

foreach ($tickets as $ticket) {
    $ticketStatus = strtolower((string) ($ticket['status'] ?? ''));
    match ($ticketStatus) {
        'open' => $totalOpen++,
        'in_progress', 'in progress' => $totalInProgress++,
        'pending' => $totalPending++,
        'resolved' => $totalResolved++,
        'closed' => $totalClosed++,
        default => null,
    };
}

if (!function_exists('getAgentAssignedTicketStatusClass')) {
    function getAgentAssignedTicketStatusClass(string $status): string
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
}

if (!function_exists('getAgentAssignedTicketPriorityClass')) {
    function getAgentAssignedTicketPriorityClass(string $priority): string
    {
        return match (strtolower($priority)) {
            'low' => 'status-open',
            'medium' => 'status-progress',
            'high' => 'status-pending',
            'urgent' => 'status-closed',
            default => 'status-open',
        };
    }
}

$agentName = htmlspecialchars($targetAgent['full_name'] ?? 'Agent');
$agentEmail = htmlspecialchars($targetAgent['email'] ?? '');
$agentRole = htmlspecialchars(ucfirst($targetAgent['role'] ?? 'Agent'));

?>

<div class="container-fluid px-0">

    <!-- BACK BUTTON -->
    <div class="mb-3">
        <a href="<?= BASE_URL ?>/agent/tickets/all-assigned" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to All Assigned Tickets
        </a>
    </div>

    <!-- PAGE HEADER -->
    <section class="ui-panel mb-4">
        <div class="ui-panel-body">
            <div class="page-header mb-0">
                <div class="page-header-content">
                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-person-badge-fill"></i> Agent Assigned Queue
                    </div>
                    <h1 class="page-title">
                        Tickets Assigned to <?= $agentName; ?>
                    </h1>
                    <p class="page-description">
                        Email: <strong><?= $agentEmail; ?></strong> | Role: <strong><?= $agentRole; ?></strong>
                    </p>
                </div>
                <div class="page-actions">
                    <a href="<?= BASE_URL ?>/agent/tickets/create" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle-fill me-2"></i> Create Ticket
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- METRICS SECTION -->
    <section class="content-section mb-4">
        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
                </div>
                <div class="metric-card-label">Total Assigned</div>
                <div class="metric-card-value"><?= $totalRecords; ?></div>
                <div class="metric-card-meta"><i class="bi bi-list-ul"></i> <?= $currentPageCount; ?> shown on this page</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-folder2-open"></i></div>
                </div>
                <div class="metric-card-label">Open</div>
                <div class="metric-card-value"><?= $totalOpen; ?></div>
                <div class="metric-card-meta">Current page</div>
            </div>

            <div class="metric-card metric-card-info">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-arrow-repeat"></i></div>
                </div>
                <div class="metric-card-label">In Progress</div>
                <div class="metric-card-value"><?= $totalInProgress; ?></div>
                <div class="metric-card-meta">Current page</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="metric-card-label">Pending</div>
                <div class="metric-card-value"><?= $totalPending; ?></div>
                <div class="metric-card-meta">Current page</div>
            </div>

            <div class="metric-card metric-card-success">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="metric-card-label">Resolved</div>
                <div class="metric-card-value"><?= $totalResolved; ?></div>
                <div class="metric-card-meta">Current page</div>
            </div>

            <div class="metric-card metric-card-danger">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-lock-fill"></i></div>
                </div>
                <div class="metric-card-label">Closed</div>
                <div class="metric-card-value"><?= $totalClosed; ?></div>
                <div class="metric-card-meta">Current page</div>
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
            <a href="<?= BASE_URL ?>/agent/tickets/all-assigned" class="btn btn-sm btn-primary-custom">
                <i class="bi bi-people-fill me-1"></i> View All Assigned
            </a>
        </div>
    <?php endif; ?>

    <!-- FILTER & TICKET TABLE CARD -->
    <section class="table-card content-section">
        <div class="table-card-header flex-wrap gap-3">
            <div>
                <div class="table-card-title">Assigned Tickets for <?= $agentName; ?></div>
                <div class="table-card-subtitle">Search and filter tickets assigned to this agent.</div>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/agent/tickets/assigned-agent/<?= (int)($targetAgent['id'] ?? 0); ?>" class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <div class="ticket-search-wrapper position-relative">
                    <i class="bi bi-search ticket-search-icon"></i>
                    <input type="search" name="search" class="form-control ticket-search-input" placeholder="Search ticket no, subject..." value="<?= htmlspecialchars($search); ?>" autocomplete="off" aria-label="Search tickets">
                </div>

                <select name="status" class="form-select table-filter" aria-label="Filter status">
                    <option value="">All Statuses</option>
                    <option value="open" <?= $status === 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="resolved" <?= $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?= $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>

                <select name="priority" class="form-select table-filter" aria-label="Filter priority">
                    <option value="">All Priorities</option>
                    <option value="low" <?= $priority === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?= $priority === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?= $priority === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?= $priority === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary-custom">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>

                <?php if (!empty($search) || !empty($status) || !empty($priority)): ?>
                    <a href="<?= BASE_URL ?>/agent/tickets/assigned-agent/<?= (int)($targetAgent['id'] ?? 0); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-card-body">
            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <h3 class="empty-state-title">No assigned tickets found</h3>
                    <p class="empty-state-description">
                        <?= (!empty($search) || !empty($status) || !empty($priority)) ? 'No tickets match your filter criteria.' : 'No tickets are currently assigned to this agent.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table" id="ticketTable">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Closed</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <?php
                                $ticketId = (int)($ticket['id'] ?? 0);
                                $ticketNumber = trim((string)($ticket['ticket_no'] ?? ''));
                                $customerName = trim((string)($ticket['customer_name'] ?? ''));
                                $subject = trim((string)($ticket['subject'] ?? ''));
                                $priorityVal = strtolower((string)($ticket['priority'] ?? 'low'));
                                $statusVal = strtolower((string)($ticket['status'] ?? 'open'));
                                $createdAt = !empty($ticket['created_at']) ? date('M d, Y H:i', strtotime($ticket['created_at'])) : '-';
                                $closedAt = !empty($ticket['closed_at']) ? date('M d, Y H:i', strtotime($ticket['closed_at'])) : '-';
                                $initial = $customerName !== '' ? strtoupper(substr($customerName, 0, 1)) : 'C';
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="text-decoration-none text-primary">
                                                <?= htmlspecialchars($ticketNumber); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="table-avatar">
                                                <?= htmlspecialchars($initial); ?>
                                            </div>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($customerName ?: 'Unknown Customer'); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($subject); ?>">
                                            <?= htmlspecialchars($subject); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= getAgentAssignedTicketPriorityClass($priorityVal); ?>">
                                            <?= htmlspecialchars(ucfirst($priorityVal)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= getAgentAssignedTicketStatusClass($statusVal); ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $statusVal))); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($createdAt); ?></td>
                                    <td><?= htmlspecialchars($closedAt); ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="table-action-btn table-action-view" title="View ticket" aria-label="View ticket">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php require ROOT_PATH . '/app/Views/partials/pagination.php'; ?>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
