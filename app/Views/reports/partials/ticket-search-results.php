<?php if (empty($tickets)): ?>

    <div class="text-center py-5">

        <div style="font-size:60px;">🔍</div>

        <h5 class="mt-3 mb-2 fw-bold">
            No matching tickets found
        </h5>

        <p class="text-muted mb-0">
            Try searching by Ticket ID, Subject, Organization,
            User, Email or Description.
        </p>

    </div>

<?php else: ?>

<style>

.search-ticket-card{
    display:block;
    text-decoration:none;
    color:#111827;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:18px;
    margin-bottom:14px;
    background:#ffffff;
    transition:.25s ease;
}

.search-ticket-card:hover{
    transform:translateY(-2px);
    border-color:#b1e96f;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    text-decoration:none;
    color:#111827;
}

.ticket-number{
    display:inline-block;
    background:#eef8df;
    color:#4f772d;
    font-weight:700;
    font-size:12px;
    padding:5px 12px;
    border-radius:50px;
}

.ticket-subject{
    font-size:17px;
    font-weight:700;
    margin-top:12px;
    color:#111827;
}

.ticket-meta{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    margin-top:12px;
    color:#6b7280;
    font-size:13px;
}

.ticket-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:18px;
}

.priority-badge{
    padding:5px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.priority-low{
    background:#eef2f7;
    color:#475569;
}

.priority-medium{
    background:#dbeafe;
    color:#1d4ed8;
}

.priority-high{
    background:#fef3c7;
    color:#92400e;
}

.priority-urgent{
    background:#fee2e2;
    color:#b91c1c;
}

.status-badge{
    padding:5px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.status-open{
    background:#dcfce7;
    color:#15803d;
}

.status-in-progress{
    background:#ede9fe;
    color:#6d28d9;
}

.status-pending{
    background:#fef3c7;
    color:#92400e;
}

.status-resolved{
    background:#dbeafe;
    color:#1d4ed8;
}

.status-closed{
    background:#fee2e2;
    color:#b91c1c;
}

.view-ticket{
    color:#4f772d;
    font-weight:700;
    font-size:13px;
}

</style>

<?php foreach ($tickets as $ticket): ?>

<?php

$statusClass = match($ticket['status']){
    'open'=>'status-open',
    'in_progress'=>'status-in-progress',
    'pending'=>'status-pending',
    'resolved'=>'status-resolved',
    'closed'=>'status-closed',
    default=>'status-open'
};

$priorityClass = match($ticket['priority']){
    'low'=>'priority-low',
    'medium'=>'priority-medium',
    'high'=>'priority-high',
    'urgent'=>'priority-urgent',
    default=>'priority-low'
};

?>

<a
    href="<?= BASE_URL ?>/reports/ticket-detail?ticket_id=<?= $ticket['id']; ?>"
    class="search-ticket-card">

    <div class="d-flex justify-content-between">

        <span class="ticket-number">
            <?= htmlspecialchars($ticket['ticket_no']); ?>
        </span>

        <span class="view-ticket">
            👁 View Report →
        </span>

    </div>

    <div class="ticket-subject">
        <?= htmlspecialchars($ticket['subject']); ?>
    </div>

    <div class="ticket-meta">

        <span>
            🏢
            <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
        </span>

        <span>
            👤
            <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
        </span>

        <span>
            📅
            <?= date('d M Y', strtotime($ticket['created_at'])); ?>
        </span>

    </div>

    <div class="ticket-footer">

        <div>

            <span class="priority-badge <?= $priorityClass; ?>">
                <?= ucfirst($ticket['priority']); ?>
            </span>

            <span class="status-badge <?= $statusClass; ?>">
                <?= ucwords(str_replace('_',' ',$ticket['status'])); ?>
            </span>

        </div>

        <?php if(!empty($ticket['closed_by_agent_name'])): ?>

            <small class="text-muted">
                Closed by
                <strong><?= htmlspecialchars($ticket['closed_by_agent_name']); ?></strong>
            </small>

        <?php endif; ?>

    </div>

</a>

<?php endforeach; ?>

<?php endif; ?>