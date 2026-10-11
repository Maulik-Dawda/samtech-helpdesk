<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$tickets = is_array($tickets ?? null) ? $tickets : [];
$totalRecords = (int) ($totalRecords ?? count($tickets));
$currentPageCount = count($tickets);

$search = (string)($search ?? '');
$status = (string)($status ?? '');
$priority = (string)($priority ?? '');

$totalUnassigned = (int)($statusCounts['total'] ?? $totalRecords);
$totalOpen = (int)($statusCounts['total_open'] ?? 0);
$totalInProgress = (int)($statusCounts['total_in_progress'] ?? 0);
$totalPending = (int)($statusCounts['total_pending'] ?? 0);
$totalResolved = (int)($statusCounts['total_resolved'] ?? 0);
$totalClosed = (int)($statusCounts['total_closed'] ?? 0);

if (!function_exists('getUnassignedTicketStatusClass')) {
    function getUnassignedTicketStatusClass(string $status): string
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

if (!function_exists('getUnassignedTicketPriorityClass')) {
    function getUnassignedTicketPriorityClass(string $priority): string
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

?>

<div class="container-fluid px-0">

    <!-- TOP NAVIGATION -->
    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/agent/tickets" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> All Tickets
            </a>
            <a href="<?= BASE_URL ?>/agent/tickets/all-assigned" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-people me-1"></i> All Assigned Tickets
            </a>
        </div>
        <div>
            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> <?= $totalUnassigned; ?> Unassigned Ticket<?= $totalUnassigned !== 1 ? 's' : ''; ?>
            </span>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- PAGE HEADER -->
    <section class="ui-panel mb-4">
        <div class="ui-panel-body">
            <div class="page-header mb-0">
                <div class="page-header-content">
                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-person-x-fill"></i> Unassigned Queue
                    </div>
                    <h1 class="page-title">
                        Unassigned Tickets
                    </h1>
                    <p class="page-description">
                        All customer support tickets currently waiting to be assigned to an agent. You can assign them directly using the dropdown in each row.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- METRICS SECTION -->
    <section class="content-section mb-4">
        <div class="metric-grid">
            <div class="metric-card metric-card-warning">
                <div class="metric-card-header">
                    <div class="metric-card-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
                <div class="metric-card-label">Total Unassigned</div>
                <div class="metric-card-value"><?= $totalUnassigned; ?></div>
                <div class="metric-card-meta"><i class="bi bi-list-ul"></i> <?= $currentPageCount; ?> shown on this page</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-folder2-open"></i></div>
                </div>
                <div class="metric-card-label">Open</div>
                <div class="metric-card-value"><?= $totalOpen; ?></div>
                <div class="metric-card-meta">Awaiting attention</div>
            </div>

            <div class="metric-card metric-card-info">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-arrow-repeat"></i></div>
                </div>
                <div class="metric-card-label">In Progress</div>
                <div class="metric-card-value"><?= $totalInProgress; ?></div>
                <div class="metric-card-meta">Work commenced</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="metric-card-label">Pending</div>
                <div class="metric-card-value"><?= $totalPending; ?></div>
                <div class="metric-card-meta">Awaiting response</div>
            </div>

            <div class="metric-card metric-card-success">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="metric-card-label">Resolved / Closed</div>
                <div class="metric-card-value"><?= ($totalResolved + $totalClosed); ?></div>
                <div class="metric-card-meta">Completed</div>
            </div>
        </div>
    </section>

    <!-- FILTER & TICKET TABLE CARD -->
    <section class="table-card content-section">
        <div class="table-card-header flex-wrap gap-3">
            <div>
                <div class="table-card-title">Unassigned Tickets Directory</div>
                <div class="table-card-subtitle">Search by ticket number, subject, customer, or filter by status and priority.</div>
            </div>

            <!-- FILTER FORM -->
            <form method="GET" action="<?= BASE_URL ?>/agent/tickets/unassigned" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="position-relative">
                    <input
                        type="search"
                        name="search"
                        class="form-control form-control-sm"
                        placeholder="Search unassigned tickets..."
                        value="<?= htmlspecialchars($search); ?>"
                        style="min-width: 200px;"
                    >
                </div>

                <select name="status" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Statuses</option>
                    <option value="open" <?= $status === 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="resolved" <?= $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?= $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>

                <select name="priority" class="form-select form-select-sm" style="width: auto;">
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
                    <a href="<?= BASE_URL ?>/agent/tickets/unassigned" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-card-body">
            <?php if (empty($tickets)): ?>
                <div class="empty-state py-5 text-center">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    </div>
                    <h3 class="empty-state-title">No unassigned tickets found</h3>
                    <p class="empty-state-description text-muted">
                        <?= (!empty($search) || !empty($status) || !empty($priority))
                            ? 'No unassigned tickets match your filter criteria.'
                            : 'All customer tickets in the system are currently assigned to support agents. Excellent work!'; ?>
                    </p>
                    <a href="<?= BASE_URL ?>/agent/tickets" class="btn btn-primary-custom btn-sm mt-2">
                        <i class="bi bi-ticket-detailed me-1"></i> View All Tickets
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle" id="unassignedTicketTable">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
                                <th>Customer / Branch</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th style="min-width: 180px;">Assign Agent</th>
                                <th>Created</th>
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
                                $createdAt = !empty($ticket['created_at']) ? strtotime($ticket['created_at']) : null;
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="ticket-no-link fw-bold">
                                                #<?= htmlspecialchars($ticketNumber !== '' ? $ticketNumber : (string)$ticketId); ?>
                                            </a>
                                            <?php if (!empty($ticket['organization_name'])): ?>
                                                <div class="ticket-org-subtext text-muted small" title="<?= htmlspecialchars($ticket['organization_name']); ?>">
                                                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($ticket['organization_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ticket-subject-text fw-semibold" title="<?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>" style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>
                                            </a>
                                        </div>
                                        <?php if (!empty($ticket['description'])): ?>
                                            <div class="text-muted small text-truncate" style="max-width: 260px;">
                                                <?= htmlspecialchars(strip_tags($ticket['description'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars($customerName !== '' ? $customerName : '-'); ?>
                                        </div>
                                        <?php if (!empty($ticket['branch_name'])): ?>
                                            <div class="ticket-org-subtext text-muted small">
                                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($ticket['branch_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status-pill <?= getUnassignedTicketPriorityClass($priorityVal); ?>">
                                            <?= htmlspecialchars(ucfirst($priorityVal)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status-pill <?= getUnassignedTicketStatusClass($statusVal); ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $statusVal))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="<?= BASE_URL ?>/agent/tickets/assign/<?= $ticketId; ?>" class="d-flex align-items-center gap-1">
                                            <?= Csrf::field(); ?>
                                            <select name="assigned_agent_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 12px;" title="Select an agent to assign this ticket">
                                                <option value="">-- Assign Agent --</option>
                                                <?php foreach ($assignableAgents as $ag): ?>
                                                    <option value="<?= $ag['id']; ?>">
                                                        <?= htmlspecialchars($ag['full_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="text-nowrap small text-muted">
                                            <?= $createdAt ? date('d M Y, h:i A', $createdAt) : '-'; ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="btn btn-sm btn-outline-primary" title="View Ticket Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing page <?= $page; ?> of <?= $totalPages; ?> (<?= $totalRecords; ?> total unassigned tickets)
                        </div>
                        <nav aria-label="Unassigned tickets pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <?php
                                $queryParams = [];
                                if (!empty($search)) $queryParams['search'] = $search;
                                if (!empty($status)) $queryParams['status'] = $status;
                                if (!empty($priority)) $queryParams['priority'] = $priority;

                                $buildPageUrl = function($p) use ($queryParams) {
                                    $params = array_merge($queryParams, ['page' => $p]);
                                    return BASE_URL . '/agent/tickets/unassigned?' . http_build_query($params);
                                };
                                ?>

                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $buildPageUrl($page - 1); ?>" aria-label="Previous">&laquo;</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= $buildPageUrl($i); ?>"><?= $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $buildPageUrl($page + 1); ?>" aria-label="Next">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
