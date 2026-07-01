<?php if(empty($tickets)): ?>

    <div class="alert alert-warning mb-0">
        No matching tickets found.
    </div>

<?php else: ?>

    <?php foreach($tickets as $ticket): ?>

        <?php
            $statusClass = match($ticket['status']) {
                'open' => 'status-open',
                'in_progress' => 'status-in-progress',
                'pending' => 'status-pending',
                'resolved' => 'status-resolved',
                'closed' => 'status-closed',
                default => 'status-open'
            };
        ?>

        <a
            href="<?= BASE_URL ?>/reports/ticket-detail?ticket_id=<?= $ticket['id']; ?>"
            class="search-result-item">

            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <div class="fw-bold">
                        <?= htmlspecialchars($ticket['ticket_no']); ?>
                    </div>

                    <div class="mt-1">
                        <?= htmlspecialchars($ticket['subject']); ?>
                    </div>

                    <div class="text-muted small mt-1">
                        <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                        •
                        <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                    </div>
                </div>

                <span class="badge-soft <?= $statusClass; ?>">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                </span>

            </div>

        </a>

    <?php endforeach; ?>

<?php endif; ?>