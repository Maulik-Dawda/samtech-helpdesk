<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<?php

$statusClasses = [
    'open' => 'status-open',
    'closed' => 'status-closed',
    'resolved' => 'status-resolved',
    'pending' => 'status-pending',
    'in_progress' => 'status-in-progress'
];

$priorityClasses = [
    'low' => 'priority-low',
    'medium' => 'priority-medium',
    'high' => 'priority-high',
    'urgent' => 'priority-urgent'
];

$currentStatusClass = $statusClasses[$ticket['status']] ?? 'status-open';
$currentPriorityClass = $priorityClasses[$ticket['priority']] ?? 'priority-low';

?>

<style>
    .badge-soft {
        padding: 8px 14px;
        border-radius: 6px;
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

    .ticket-card {
        border-radius: 18px;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px;
        height: 100%;
    }

    .reply-box {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }

    .back-link {
        color: #111827;
        border: 1px solid #d1d5db;
        background: transparent;
        border-radius: 8px;
        padding: 6px 13px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }

    .back-link:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .timeline-item {
        border-left: 3px solid #b1e96f;
        padding-left: 15px;
        margin-bottom: 18px;
    }
</style>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Ticket Details</h4>
            <div class="text-muted small">View ticket information and conversation.</div>
        </div>

        <a href="<?= BASE_URL ?>/tickets" class="back-link">Back to Tickets</a>
    </div>

    <div class="row">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm ticket-card mb-4">
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

                        <span class="badge-soft <?= $currentStatusClass; ?>">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                        </span>
                    </div>

                    <div>
                        <strong>Description</strong>
                        <div class="border rounded p-3 bg-light mt-2">
                            <?= nl2br(htmlspecialchars($ticket['description'])); ?>
                        </div>
                    </div>
                    <?php if (!empty($attachments)): ?>

                        <div class="mt-4">

                            <strong>Attachments</strong>

                            <div class="mt-2">

                                <?php foreach ($attachments as $attachment): ?>

                                    <div
                                        class="border rounded p-2 mb-2 bg-light
    d-flex justify-content-between align-items-center">

                                        <div
                                            style="
            max-width:75%;
            overflow:hidden;
            white-space:nowrap;
            text-overflow:ellipsis;
        "
                                            title="<?= htmlspecialchars($attachment['original_name']); ?>">

                                            📎 <?= htmlspecialchars($attachment['original_name']); ?>

                                        </div>

                                        <a
                                            href="<?= BASE_URL ?>/attachments/ticket/download/<?= $attachment['id']; ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            Download
                                        </a>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>
            </div>

            <div class="card border-0 shadow-sm ticket-card">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Conversation</h5>

                    <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($replies)): ?>
                        <div class="alert alert-info">No replies yet.</div>
                    <?php else: ?>
                        <?php foreach ($replies as $reply): ?>
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
                                <?php if (!empty($replyAttachments[$reply['id']])): ?>

                                    <div class="mt-3">

                                        <?php foreach ($replyAttachments[$reply['id']] as $replyAttachment): ?>

                                            <div class="border rounded p-2 mb-2 bg-white d-flex justify-content-between align-items-center">

                                                <div
                                                    style="
                        max-width:75%;
                        overflow:hidden;
                        white-space:nowrap;
                        text-overflow:ellipsis;
                    "
                                                    title="<?= htmlspecialchars($replyAttachment['original_name']); ?>">
                                                    📎 <?= htmlspecialchars($replyAttachment['original_name']); ?>
                                                </div>

                                                <a
                                                    href="<?= BASE_URL ?>/attachments/reply/download/<?= $replyAttachment['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Download
                                                </a>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr>

                    <?php if ($ticket['status'] !== 'closed'): ?>
                        <form method="POST" action="<?= BASE_URL ?>/tickets/reply/<?= $ticket['id']; ?>" enctype="multipart/form-data">
                            <?= Csrf::field(); ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Add Reply</label>
                                <textarea
                                    name="message"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Write your reply..."
                                    required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Attachments</label>

                                <input
                                    type="file"
                                    name="attachments[]"
                                    class="form-control"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                                <small class="text-muted">
                                    Maximum 3 files, 5MB per file.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary-custom">
                                Send Reply
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success mb-0">
                            This ticket is closed. Replies are disabled.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm ticket-card mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">Ticket Information</h5>

                    <div class="mb-4">
                        <small class="text-muted">Current Status</small>
                        <div class="mt-2">
                            <span class="badge-soft <?= $currentStatusClass; ?>">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">Priority</small>
                        <div class="mt-2">
                            <span class="badge-soft <?= $currentPriorityClass; ?>">
                                <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Closed / Resolved By</small>
                        <div class="fw-semibold mt-1">
                            <?php if (!empty($ticket['closed_by_agent_name'])): ?>
                                <?= htmlspecialchars($ticket['closed_by_agent_name']); ?>
                            <?php else: ?>
                                Not closed yet
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Created At</small>
                        <div class="fw-semibold mt-1">
                            <?= htmlspecialchars($ticket['created_at']); ?>
                        </div>
                    </div>

                    <div>
                        <small class="text-muted">Closed At</small>
                        <div class="fw-semibold mt-1">
                            <?php if (!empty($ticket['closed_at'])): ?>
                                <?= htmlspecialchars($ticket['closed_at']); ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm ticket-card">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">Status Timeline</h5>

                    <?php if (empty($statusHistory)): ?>
                        <div class="text-muted">No status updates available.</div>
                    <?php else: ?>
                        <?php foreach ($statusHistory as $history): ?>
                            <div class="timeline-item">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['old_status']))); ?>
                                    →
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['new_status']))); ?>
                                </div>

                                <small class="text-muted">
                                    Updated By <?= htmlspecialchars($history['full_name']); ?><br>
                                    <?= htmlspecialchars($history['created_at']); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>