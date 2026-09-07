<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$agentsWithCounts = is_array($agentsWithCounts ?? null) ? $agentsWithCounts : [];
$unassignedCount = (int)($unassignedCount ?? 0);

$totalAgents = count($agentsWithCounts);
$totalAssignedTickets = 0;
$totalOpenTickets = 0;
$totalInProgressTickets = 0;
$totalResolvedTickets = 0;
$totalClosedTickets = 0;

foreach ($agentsWithCounts as $agentItem) {
    $totalAssignedTickets += (int)($agentItem['total_assigned'] ?? 0);
    $totalOpenTickets += (int)($agentItem['total_open'] ?? 0);
    $totalInProgressTickets += (int)($agentItem['total_in_progress'] ?? 0);
    $totalResolvedTickets += (int)($agentItem['total_resolved'] ?? 0);
    $totalClosedTickets += (int)($agentItem['total_closed'] ?? 0);
}

?>

<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <section class="ui-panel mb-4">
        <div class="ui-panel-body">
            <div class="page-header mb-0">
                <div class="page-header-content">
                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-people-fill"></i> Team Assignment Overview
                    </div>
                    <h1 class="page-title">
                        All Assigned Tickets
                    </h1>
                    <p class="page-description">
                        Monitor ticket workload across all support agents and manage team assignments.
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

    <!-- SUMMARY METRICS SECTION -->
    <section class="content-section mb-4">
        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-person-badge"></i></div>
                </div>
                <div class="metric-card-label">Active Support Agents</div>
                <div class="metric-card-value"><?= $totalAgents; ?></div>
                <div class="metric-card-meta">Total agents</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
                </div>
                <div class="metric-card-label">Total Assigned</div>
                <div class="metric-card-value"><?= $totalAssignedTickets; ?></div>
                <div class="metric-card-meta">Across all agents</div>
            </div>

            <div class="metric-card metric-card-warning">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
                </div>
                <div class="metric-card-label">Unassigned Tickets</div>
                <div class="metric-card-value"><?= $unassignedCount; ?></div>
                <div class="metric-card-meta">Pending assignment</div>
            </div>

            <div class="metric-card">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-folder2-open"></i></div>
                </div>
                <div class="metric-card-label">Open Tickets</div>
                <div class="metric-card-value"><?= $totalOpenTickets; ?></div>
                <div class="metric-card-meta">Team total</div>
            </div>

            <div class="metric-card metric-card-info">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-arrow-repeat"></i></div>
                </div>
                <div class="metric-card-label">In Progress</div>
                <div class="metric-card-value"><?= $totalInProgressTickets; ?></div>
                <div class="metric-card-meta">Team total</div>
            </div>

            <div class="metric-card metric-card-success">
                <div class="metric-card-header">
                    <div class="metric-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="metric-card-label">Resolved / Closed</div>
                <div class="metric-card-value"><?= ($totalResolvedTickets + $totalClosedTickets); ?></div>
                <div class="metric-card-meta">Completed</div>
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
                        There <?= $unassignedCount === 1 ? 'is' : 'are'; ?> <strong><?= $unassignedCount; ?></strong> ticket<?= $unassignedCount > 1 ? 's' : ''; ?> in the system currently unassigned.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- AGENTS TABLE CARD -->
    <section class="table-card content-section">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Support Agents & Assigned Ticket Workload</div>
                <div class="table-card-subtitle">Click on any agent to inspect all tickets assigned to them.</div>
            </div>
            <div class="app-badge app-badge-primary">
                <i class="bi bi-people"></i> <?= $totalAgents; ?> <?= $totalAgents === 1 ? 'Agent' : 'Agents'; ?>
            </div>
        </div>

        <div class="table-card-body">
            <?php if (empty($agentsWithCounts)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="empty-state-title">No support agents found</h3>
                    <p class="empty-state-description">There are no active support agents registered in the system.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table" id="allAssignedTable">
                        <thead>
                            <tr>
                                <th>Agent Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-center">Total Assigned</th>
                                <th class="text-center">Open</th>
                                <th class="text-center">In Progress</th>
                                <th class="text-center">Resolved</th>
                                <th class="text-center">Closed</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agentsWithCounts as $agent): ?>
                                <?php
                                $agentId = (int)$agent['id'];
                                $agentName = trim((string)$agent['full_name']);
                                $agentEmail = trim((string)$agent['email']);
                                $roleStr = (string)($agent['role'] ?? 'agent');
                                $isAdminAgent = (int)($agent['is_admin_agent'] ?? 0) === 1;

                                $initial = $agentName !== '' ? strtoupper(mb_substr($agentName, 0, 1)) : 'A';

                                $roleLabel = match ($roleStr) {
                                    'admin' => 'Administrator',
                                    'agent' => $isAdminAgent ? 'Admin Agent' : 'Support Agent',
                                    default => 'Support Agent'
                                };
                                $roleStatusClass = match ($roleStr) {
                                    'admin' => 'status-closed',
                                    'agent' => $isAdminAgent ? 'status-progress' : 'status-open',
                                    default => 'status-resolved'
                                };
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="table-avatar">
                                                <?= htmlspecialchars($initial); ?>
                                            </div>
                                            <div class="fw-semibold">
                                                <a href="<?= BASE_URL ?>/agent/tickets/assigned-agent/<?= $agentId; ?>" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($agentName); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            <?= htmlspecialchars($agentEmail); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $roleStatusClass; ?>">
                                            <?= htmlspecialchars($roleLabel); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-open fw-bold">
                                            <?= (int)($agent['total_assigned'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-pending fw-bold">
                                            <?= (int)($agent['total_open'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-progress fw-bold">
                                            <?= (int)($agent['total_in_progress'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-resolved fw-bold">
                                            <?= (int)($agent['total_resolved'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-closed fw-bold">
                                            <?= (int)($agent['total_closed'] ?? 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="<?= BASE_URL ?>/agent/tickets/assigned-agent/<?= $agentId; ?>" class="btn btn-sm btn-primary-custom" title="View Assigned Tickets">
                                                <i class="bi bi-eye-fill me-1"></i> View Tickets
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
