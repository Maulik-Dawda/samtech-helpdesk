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

    <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
        <div style="flex: 1 1 auto; min-width: 0;">
            <h4 class="fw-bold mb-1">Ticket Details</h4>
            <div class="text-muted small">View ticket information and conversation.</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">
            <a href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= (int)$ticket['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary fw-bold d-inline-flex align-items-center text-nowrap">
                <i class="bi bi-printer-fill me-1"></i> Print / Download Report
            </a>
            <a href="<?= BASE_URL ?>/tickets" class="back-link text-nowrap">Back to Tickets</a>
        </div>
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



                    <?php if (empty($replies)): ?>
                        <div class="alert alert-info">No replies yet.</div>
                    <?php else: ?>
                        <?php foreach ($replies as $reply): ?>
                            <?php
                            $replyId = (int) ($reply['id'] ?? 0);
                            $replyUserId = (int) ($reply['user_id'] ?? 0);
                            $replyEditCount = (int) ($reply['edit_count'] ?? 0);
                            $replyEditedAt = !empty($reply['edited_at']) ? (string) $reply['edited_at'] : '';

                            $currentUserId = (int) ($_SESSION['auth_user_id'] ?? 0);
                            $ticketIsClosed = strtolower((string) ($ticket['status'] ?? '')) === 'closed';
                            $canEditReply = !$ticketIsClosed && ($replyUserId === $currentUserId) && $replyEditCount < 2;
                            ?>
                            <div class="reply-box p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>
                                        <?= htmlspecialchars($reply['full_name']); ?>
                                        <span class="badge-soft priority-low">
                                            <?= htmlspecialchars(ucfirst($reply['role'])); ?>
                                        </span>
                                    </strong>

                                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                                        <?php if ($replyEditCount > 0): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill" title="Edited <?= $replyEditCount; ?> time<?= $replyEditCount > 1 ? 's' : ''; ?><?= !empty($replyEditedAt) ? ' on ' . htmlspecialchars($replyEditedAt) : ''; ?>">
                                                <i class="bi bi-pencil-fill me-1"></i> (Edited)
                                            </span>
                                        <?php endif; ?>

                                        <span>
                                            <i class="bi bi-clock me-1"></i>
                                            <?= htmlspecialchars($reply['created_at']); ?>
                                        </span>

                                        <?php if ($canEditReply): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 text-nowrap ms-1" data-bs-toggle="modal" data-bs-target="#editUserReplyModal_<?= $replyId; ?>">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <?= nl2br(htmlspecialchars($reply['message'])); ?>
                                </div>
                                <?php if (!empty($replyAttachments[$replyId])): ?>

                                    <div class="mt-3">

                                        <?php foreach ($replyAttachments[$replyId] as $replyAttachment): ?>

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

                                <?php if ($canEditReply): ?>
                                    <!-- Edit Reply Modal -->
                                    <div class="modal fade text-start" id="editUserReplyModal_<?= $replyId; ?>" tabindex="-1" aria-labelledby="editUserReplyModalLabel_<?= $replyId; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="<?= BASE_URL ?>/tickets/reply/edit/<?= $replyId; ?>" method="POST">
                                                    <?= Csrf::field(); ?>
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark" id="editUserReplyModalLabel_<?= $replyId; ?>">
                                                            <i class="bi bi-pencil-square text-primary me-2"></i>
                                                            Edit Reply Message
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            You can edit your reply up to 2 times. Edits used: <strong><?= $replyEditCount; ?>/2</strong>.
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label fw-semibold">Reply Message</label>
                                                            <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($reply['message']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0 gap-2">
                                                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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

                            <div class="mb-3 attachment-upload-group">
                                <label class="form-label fw-semibold">Attachments</label>

                                <input
                                    type="file"
                                    class="form-control attachment-picker-input"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                                <input
                                    type="file"
                                    name="attachments[]"
                                    class="form-control attachment-hidden-input d-none"
                                    multiple>

                                <small class="text-muted d-block mt-1">
                                    JPG, PNG, PDF, Word, Excel, TXT, ZIP, RAR. Select files as many times as needed, 5MB per file.
                                </small>

                                <div class="attachment-staged-list d-flex flex-column gap-2 mt-2"></div>
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