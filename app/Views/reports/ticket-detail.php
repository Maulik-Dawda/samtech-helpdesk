<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
.card-radius{
    border-radius:18px;
}

.badge-soft{
    padding:7px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

.status-open{background:#dcfce7;color:#15803d;}
.status-in-progress{background:#ede9fe;color:#6d28d9;}
.status-pending{background:#fce7f3;color:#be185d;}
.status-resolved{background:#ffedd5;color:#c2410c;}
.status-closed{background:#fee2e2;color:#b91c1c;}

.priority-low{background:#e5e7eb;color:#374151;}
.priority-medium{background:#dbeafe;color:#1d4ed8;}
.priority-high{background:#fef3c7;color:#92400e;}
.priority-urgent{background:#fee2e2;color:#b91c1c;}

.info-box{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:14px;
}

.timeline-item{
    border-left:3px solid #b1e96f;
    padding-left:15px;
    margin-bottom:18px;
}

.reply-box{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:14px;
}
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius mb-4">

        <div class="card-header bg-white p-4">

            <h4 class="fw-bold mb-1">
                Ticket Detail Report
            </h4>

            <div class="text-muted small">
                View full ticket details, replies, attachments and timeline.
            </div>

        </div>

        <div class="card-body p-4">

            <form method="GET" action="<?= BASE_URL ?>/reports/ticket-detail">

                <div class="row g-3 align-items-end">

                

                    <div class="col-md-8">

                        <label class="form-label">
                            Select Ticket
                        </label>

                        <select
                            name="ticket_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Ticket
                            </option>

                            <?php foreach($tickets as $reportTicket): ?>

                                <option
                                    value="<?= $reportTicket['id']; ?>"
                                    <?= $ticketId == $reportTicket['id']
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    <?= htmlspecialchars(
                                        $reportTicket['ticket_no'] .
                                        ' - ' .
                                        $reportTicket['subject']
                                    ); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <button
                            type="submit"
                            class="btn btn-primary-custom w-100"
                        >
                            View Report
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if($ticket): ?>

        <?php
            $statusClass = match($ticket['status']) {
                'open' => 'status-open',
                'in_progress' => 'status-in-progress',
                'pending' => 'status-pending',
                'resolved' => 'status-resolved',
                'closed' => 'status-closed',
                default => 'status-open'
            };

            $priorityClass = match($ticket['priority']) {
                'low' => 'priority-low',
                'medium' => 'priority-medium',
                'high' => 'priority-high',
                'urgent' => 'priority-urgent',
                default => 'priority-low'
            };
        ?>

        <div class="row">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm card-radius mb-4">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start mb-4">

                            <div>
                                <h4 class="fw-bold mb-1">
                                    <?= htmlspecialchars($ticket['subject']); ?>
                                </h4>

                                <div class="text-muted">
                                    <?= htmlspecialchars($ticket['ticket_no']); ?>
                                </div>
                            </div>

                            <span class="badge-soft <?= $statusClass; ?>">
                                <?= htmlspecialchars(
                                    ucwords(str_replace('_', ' ', $ticket['status']))
                                ); ?>
                            </span>

                        </div>

                        <div class="mb-4">

                            <strong>Description</strong>

                            <div class="border rounded bg-light p-3 mt-2">
                                <?= nl2br(htmlspecialchars($ticket['description'])); ?>
                            </div>

                        </div>

                        <?php if(!empty($attachments)): ?>

                            <div class="mb-4">

                                <strong>Ticket Attachments</strong>

                                <div class="mt-2">

                                    <?php foreach($attachments as $attachment): ?>

                                        <div
                                            class="border rounded p-2 mb-2 bg-light d-flex justify-content-between align-items-center"
                                        >

                                            <div
                                                style="
                                                    max-width:75%;
                                                    overflow:hidden;
                                                    white-space:nowrap;
                                                    text-overflow:ellipsis;
                                                "
                                                title="<?= htmlspecialchars($attachment['original_name']); ?>"
                                            >
                                                📎 <?= htmlspecialchars($attachment['original_name']); ?>
                                            </div>

                                            <a
                                                href="<?= BASE_URL ?>/attachments/ticket/download/<?= $attachment['id']; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Download
                                            </a>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="card border-0 shadow-sm card-radius">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">
                            Conversation
                        </h5>

                        <?php if(empty($replies)): ?>

                            <div class="alert alert-info">
                                No replies found.
                            </div>

                        <?php else: ?>

                            <?php foreach($replies as $reply): ?>

                                <div class="reply-box p-3 mb-3">

                                    <div class="d-flex justify-content-between mb-2">

                                        <strong>
                                            <?= htmlspecialchars($reply['full_name']); ?>

                                            <span class="badge-soft priority-low">
                                                <?= htmlspecialchars(ucfirst($reply['role'])); ?>
                                            </span>
                                        </strong>

                                        <small class="text-muted">
                                            <?= htmlspecialchars($reply['created_at']); ?>
                                        </small>

                                    </div>

                                    <div>
                                        <?= nl2br(htmlspecialchars($reply['message'])); ?>
                                    </div>

                                    <?php if(!empty($replyAttachments[$reply['id']])): ?>

                                        <div class="mt-3">

                                            <?php foreach($replyAttachments[$reply['id']] as $replyAttachment): ?>

                                                <div
                                                    class="border rounded p-2 mb-2 bg-white d-flex justify-content-between align-items-center"
                                                >

                                                    <div
                                                        style="
                                                            max-width:75%;
                                                            overflow:hidden;
                                                            white-space:nowrap;
                                                            text-overflow:ellipsis;
                                                        "
                                                        title="<?= htmlspecialchars($replyAttachment['original_name']); ?>"
                                                    >
                                                        📎 <?= htmlspecialchars($replyAttachment['original_name']); ?>
                                                    </div>

                                                    <a
                                                        href="<?= BASE_URL ?>/attachments/reply/download/<?= $replyAttachment['id']; ?>"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Download
                                                    </a>

                                                </div>

                                            <?php endforeach; ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm card-radius mb-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">
                            Ticket Information
                        </h5>

                        <div class="mb-4">
                            <small class="text-muted">
                                Organization
                            </small>

                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">
                                User
                            </small>

                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                            </div>

                            <div class="text-muted small">
                                <?= htmlspecialchars($ticket['customer_email'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">
                                Status
                            </small>

                            <div class="mt-2">
                                <span class="badge-soft <?= $statusClass; ?>">
                                    <?= htmlspecialchars(
                                        ucwords(str_replace('_', ' ', $ticket['status']))
                                    ); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">
                                Priority
                            </small>

                            <div class="mt-2">
                                <span class="badge-soft <?= $priorityClass; ?>">
                                    <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Created At
                            </small>

                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['created_at']); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Closed At
                            </small>

                            <div class="fw-semibold mt-1">
                                <?= !empty($ticket['closed_at'])
                                    ? htmlspecialchars($ticket['closed_at'])
                                    : '-'; ?>
                            </div>
                        </div>

                        <div>
                            <small class="text-muted">
                                Closed / Resolved By
                            </small>

                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card border-0 shadow-sm card-radius">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">
                            Status Timeline
                        </h5>

                        <?php if(empty($statusHistory)): ?>

                            <div class="text-muted">
                                No status updates available.
                            </div>

                        <?php else: ?>

                            <?php foreach($statusHistory as $history): ?>

                                <div class="timeline-item">

                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            ucwords(str_replace('_', ' ', $history['old_status']))
                                        ); ?>

                                        →

                                        <?= htmlspecialchars(
                                            ucwords(str_replace('_', ' ', $history['new_status']))
                                        ); ?>
                                    </div>

                                    <small class="text-muted">
                                        Updated By
                                        <?= htmlspecialchars($history['full_name']); ?>
                                        <br>
                                        <?= htmlspecialchars($history['created_at']); ?>
                                    </small>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    <?php elseif(!empty($ticketId)): ?>

        <div class="alert alert-danger">
            Ticket not found.
        </div>

    <?php endif; ?>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>