<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<?php

$statusClasses = [
    'open' => 'bg-success',
    'closed' => 'bg-danger',
    'resolved' => 'bg-warning text-dark',
    'pending' => 'bg-pink',
    'in_progress' => 'bg-purple'
];

$currentStatusClass = $statusClasses[$ticket['status']] ?? 'bg-secondary';

?>

<style>
    .bg-purple {
        background: #7c3aed !important;
        color: #fff;
    }

    .bg-pink {
        background: #db2777 !important;
        color: #fff;
    }

    .status-pill {
        padding: 10px 18px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px;
    }

    .timeline-item {
        border-left: 3px solid #b1e96f;
        padding-left: 15px;
        margin-bottom: 18px;
    }

    .ticket-card {
        border-radius: 18px;
    }
</style>

<div class="container-fluid mt-4">

    <div class="row">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4 ticket-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <h4 class="fw-bold mb-1">
                                <?= htmlspecialchars($ticket['subject']); ?>
                            </h4>

                            <div class="text-muted">
                                <?= htmlspecialchars($ticket['ticket_no']); ?>
                            </div>
                        </div>

                        <span class="badge status-pill <?= $currentStatusClass ?>">
                            <?= ucwords(str_replace('_', ' ', $ticket['status'])); ?>
                        </span>

                    </div>

                    <hr>

                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted">Customer</small>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($ticket['customer_name']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted">Email</small>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($ticket['customer_email']); ?>
                                </div>
                            </div>
                        </div>

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
                <div class="card-header bg-white p-4">
                    <h5 class="fw-bold mb-0">Conversation</h5>
                </div>

                <div class="card-body p-4">

                    <?php if (empty($replies)): ?>

                        <div class="alert alert-info">
                            No replies yet.
                        </div>

                    <?php else: ?>

                        <?php foreach ($replies as $reply): ?>

                            <div class="border rounded p-3 mb-3 bg-light">

                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <?= htmlspecialchars($reply['full_name']); ?>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars(ucfirst($reply['role'])); ?>
                                        </span>
                                    </strong>

                                    <small class="text-muted">
                                        <?= htmlspecialchars($reply['created_at']); ?>
                                    </small>
                                </div>

                                <div class="mt-2">
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

                    <?php if ($ticket['status'] !== 'closed'): ?>

                        <hr>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/agent/tickets/reply/<?= $ticket['id']; ?>"
                            enctype="multipart/form-data">
                            <?= Csrf::field(); ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reply</label>

                                <textarea
                                    name="message"
                                    rows="5"
                                    class="form-control"
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

            <div class="card border-0 shadow-sm ticket-card">
                <div class="card-header bg-white p-4">
                    <h5 class="fw-bold mb-0">Status Management</h5>
                </div>

                <div class="card-body p-4">

                    <div class="mb-3">
                        <strong>Closed / Resolved By</strong>

                        <div class="text-muted mt-1">
                            <?php if (!empty($ticket['closed_by_agent_name'])): ?>
                                <?= htmlspecialchars($ticket['closed_by_agent_name']); ?>
                            <?php else: ?>
                                Not closed yet
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Current Status</label>

                        <div class="info-box">
                            <span class="badge status-pill <?= $currentStatusClass ?>">
                                <?= ucwords(str_replace('_', ' ', $ticket['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($ticket['status'] !== 'closed'): ?>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/agent/tickets/status/<?= $ticket['id']; ?>"
                            id="statusUpdateForm">
                            <?= Csrf::field(); ?>

                            <div class="mb-4">
                                <label class="fw-bold mb-2">Change Status To</label>

                                <select
                                    name="status"
                                    id="ticketStatus"
                                    class="form-select"
                                    required>
                                    <?php if ($ticket['status'] !== 'open'): ?>
                                        <option value="open">Open</option>
                                    <?php endif; ?>

                                    <?php if ($ticket['status'] !== 'in_progress'): ?>
                                        <option value="in_progress">In Progress</option>
                                    <?php endif; ?>

                                    <?php if ($ticket['status'] !== 'pending'): ?>
                                        <option value="pending">Pending</option>
                                    <?php endif; ?>

                                    <?php if ($ticket['status'] !== 'resolved'): ?>
                                        <option value="resolved">Resolved</option>
                                    <?php endif; ?>

                                    <?php if ($ticket['status'] !== 'closed'): ?>
                                        <option value="closed">Closed</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <input type="hidden" name="resolution_message" id="resolutionMessageInput">

                            <button type="submit" class="btn btn-success w-100">
                                Update Status
                            </button>
                        </form>

                    <?php else: ?>

                        <div class="alert alert-success mb-0">
                            This ticket has been closed.
                        </div>

                    <?php endif; ?>

                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4 ticket-card">
                <div class="card-header bg-white p-4">
                    <h5 class="fw-bold mb-0">Status Timeline</h5>
                </div>

                <div class="card-body p-4">

                    <?php if (empty($statusHistory)): ?>

                        <div class="text-muted">
                            No status changes yet.
                        </div>

                    <?php else: ?>

                        <?php foreach ($statusHistory as $history): ?>

                            <div class="timeline-item">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['old_status']))); ?>
                                    →
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['new_status']))); ?>
                                </div>

                                <small class="text-muted">
                                    By <?= htmlspecialchars($history['full_name']); ?>
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

</div>

<div class="modal fade" id="resolutionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Resolution Message</h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                <p class="text-muted">
                    Please enter the final resolution message before closing this ticket.
                </p>

                <textarea
                    id="resolutionMessageTextarea"
                    class="form-control"
                    rows="5"
                    placeholder="Example: Issue resolved after resetting the user's Outlook profile."></textarea>

                <div class="text-danger small mt-2 d-none" id="resolutionError">
                    Resolution message is required.
                </div>
            </div>

            <div class="modal-footer border-0">
                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-success"
                    id="confirmCloseTicket">
                    Close Ticket
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusForm = document.getElementById('statusUpdateForm');
        const statusSelect = document.getElementById('ticketStatus');
        const resolutionInput = document.getElementById('resolutionMessageInput');
        const resolutionTextarea = document.getElementById('resolutionMessageTextarea');
        const resolutionError = document.getElementById('resolutionError');
        const confirmCloseBtn = document.getElementById('confirmCloseTicket');

        if (!statusForm) {
            return;
        }

        const resolutionModalEl = document.getElementById('resolutionModal');
        const resolutionModal = new bootstrap.Modal(resolutionModalEl);

        statusForm.addEventListener('submit', function(e) {
            if (statusSelect.value === 'closed') {
                e.preventDefault();

                resolutionTextarea.value = '';
                resolutionInput.value = '';
                resolutionError.classList.add('d-none');

                resolutionModal.show();
            }
        });

        confirmCloseBtn.addEventListener('click', function() {
            const message = resolutionTextarea.value.trim();

            if (message.length === 0) {
                resolutionError.classList.remove('d-none');
                return;
            }

            resolutionInput.value = message;
            resolutionModal.hide();
            statusForm.submit();
        });
    });
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>