<div class="table-responsive">

    <table class="table table-hover align-middle">

        <thead>
            <tr>
                <th>Ticket No</th>
                <th>Organization</th>
                <th>User</th>
                <th>Assigned Agent</th>
                <th>Subject</th>
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
                    <td colspan="10" class="text-center text-muted py-4">
                        No records found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($tickets as $ticket): ?>

                    <tr>
                        <td class="fw-semibold">
                            <?= htmlspecialchars($ticket['ticket_no']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php if (!empty($ticket['assigned_agent_name'])): ?>
                                <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:8px; font-size:11px; font-weight:600;">
                                    <i class="bi bi-person-badge me-1"></i>
                                    <?= htmlspecialchars($ticket['assigned_agent_name']); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">Unassigned</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['subject']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['created_at']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?>
                        </td>

                        <td class="text-end">
                            <a
                                href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= (int)$ticket['id']; ?>"
                                target="_blank"
                                class="btn btn-sm btn-outline-success border-0"
                                title="Print Ticket Detail Report">
                                <i class="bi bi-printer-fill fs-6"></i>
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>