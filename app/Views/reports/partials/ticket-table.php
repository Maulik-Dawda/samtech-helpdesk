<div class="table-responsive">

    <table class="table table-hover align-middle">

        <thead>
            <tr>
                <th>Ticket</th>
                <th>Subject</th>
                <th>Customer</th>
                <th>Assigned Agent</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th>Closed By</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>

        <tbody id="ticketReportTableBody">

            <?php if (empty($tickets)): ?>

                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No records found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $ticketId = (int)($ticket['id'] ?? 0);
                    $ticketNumber = trim((string)($ticket['ticket_no'] ?? ''));
                    $customerName = trim((string)($ticket['customer_name'] ?? ''));
                    $assignedAgentName = trim((string)($ticket['assigned_agent_name'] ?? ''));
                    $subject = trim((string)($ticket['subject'] ?? ''));
                    $priorityVal = strtolower((string)($ticket['priority'] ?? 'low'));
                    $statusVal = strtolower((string)($ticket['status'] ?? 'open'));
                    $createdAt = !empty($ticket['created_at']) ? strtotime($ticket['created_at']) : null;
                    ?>
                    <tr>
                        <td>
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

                        <td>
                            <span class="ticket-subject-text" title="<?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>">
                                <?= htmlspecialchars($subject !== '' ? $subject : 'Untitled Ticket'); ?>
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold text-dark">
                                <?= htmlspecialchars($customerName !== '' ? $customerName : '-'); ?>
                            </div>
                        </td>

                        <td>
                            <?php if ($assignedAgentName !== ''): ?>
                                <span class="badge-agent-pill">
                                    <i class="bi bi-person me-1"></i>
                                    <?= htmlspecialchars($assignedAgentName); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge-unassigned-pill">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Not Assigned
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="status-badge priority-<?= $priorityVal; ?>">
                                <?= htmlspecialchars(ucfirst($priorityVal)); ?>
                            </span>
                        </td>

                        <td>
                            <span class="status-badge status-<?= str_replace('_', '-', $statusVal); ?>">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $statusVal))); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($createdAt): ?>
                                <div class="fw-semibold text-dark" style="font-size:12px;"><?= date('d M Y', $createdAt); ?></div>
                                <div class="text-muted" style="font-size:11px;"><?= date('h:i A', $createdAt); ?></div>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?>
                        </td>

                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticketId; ?>" class="ticket-btn-icon ticket-btn-view" title="View ticket" aria-label="View ticket">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= $ticketId; ?>" target="_blank" class="ticket-btn-icon ticket-btn-print" title="Print ticket" aria-label="Print ticket">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>