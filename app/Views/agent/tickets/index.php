<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
    .badge-soft {
        padding: 3px 12px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
    }

    .priority-low {
        background: #e5e7eb;
        color: #374151;
    }

    .priority-medium {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .priority-high {
        background: #fef3c7;
        color: #92400e;
    }

    .priority-urgent {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-open {
        background: #dcfce7;
        color: #15803d;
    }

    .status-in-progress {
        background: #ede9fe;
        color: #6d28d9;
    }

    .status-pending {
        background: #fce7f3;
        color: #be185d;
    }

    .status-resolved {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-closed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .view-link {
        color: #111827;
        border: 1px solid #d1d5db;
        background: transparent;
        border-radius: 8px;
        padding: 5px 12px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }

    .view-link:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .tickets-card {
        border-radius: 18px;
    }

    .tickets-table th {
        color: #6b7280;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .tickets-table td {
        vertical-align: middle;
        font-size: 14px;
    }
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm tickets-card">

        <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
            <div>
                <h4 class="fw-bold mb-1">All Tickets</h4>
                <div class="text-muted small">
                    View and manage all customer support tickets.
                </div>
            </div>

            <span class="badge-soft status-open">
                <?= count($tickets); ?> Tickets
            </span>
        </div>

        <div class="card-body p-4">

            <?php if (empty($tickets)): ?>

                <div class="alert alert-info mb-0">
                    No tickets found.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table align-middle tickets-table">

                        <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Closed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($tickets as $ticket): ?>

                                <?php
                                    $priorityClass = match ($ticket['priority']) {
                                        'low' => 'priority-low',
                                        'medium' => 'priority-medium',
                                        'high' => 'priority-high',
                                        'urgent' => 'priority-urgent',
                                        default => 'priority-low'
                                    };

                                    $statusClass = match ($ticket['status']) {
                                        'open' => 'status-open',
                                        'in_progress' => 'status-in-progress',
                                        'pending' => 'status-pending',
                                        'resolved' => 'status-resolved',
                                        'closed' => 'status-closed',
                                        default => 'status-open'
                                    };
                                ?>

                                <tr>

                                    <td class="fw-semibold">
                                        <?= htmlspecialchars($ticket['ticket_no']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($ticket['customer_name']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($ticket['subject']); ?>
                                    </td>

                                    <td>
                                        <span class="badge-soft <?= $priorityClass; ?>">
                                            <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge-soft <?= $statusClass; ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($ticket['created_at']); ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($ticket['closed_at'])): ?>
                                            <?= htmlspecialchars($ticket['closed_at']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a
                                            href="<?= BASE_URL ?>/agent/tickets/show/<?= $ticket['id']; ?>"
                                            class="view-link">
                                            View
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>