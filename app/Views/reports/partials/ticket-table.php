<div class="table-responsive">

    <table class="table table-hover align-middle">

        <thead>
            <tr>
                <th>Ticket No</th>
                <th>Organization</th>
                <th>User</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th>Closed By</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($tickets)): ?>

                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
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
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>