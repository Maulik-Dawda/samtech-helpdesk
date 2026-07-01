<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
.card-radius{border-radius:18px;}
.report-card{border-radius:20px;border:0;box-shadow:0 10px 30px rgba(15,23,42,.06);}
.search-box{position:relative;}
.search-box i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#64748b;}
.search-input{height:52px;border-radius:14px;padding-left:46px;border:1px solid #dbe3ed;}
.search-input:focus{border-color:#6cb33f;box-shadow:0 0 0 .18rem rgba(108,179,63,.14);}
.search-result-item{border:1px solid #e5e7eb;border-radius:14px;padding:14px 16px;margin-bottom:10px;background:#fff;transition:.2s;text-decoration:none;color:#111827;display:block;}
.search-result-item:hover{background:#f8fafc;border-color:#b1e96f;color:#111827;}
.badge-soft{padding:7px 12px;border-radius:999px;font-size:12px;font-weight:600;}
.status-open{background:#dcfce7;color:#15803d;}
.status-in-progress{background:#ede9fe;color:#6d28d9;}
.status-pending{background:#fce7f3;color:#be185d;}
.status-resolved{background:#ffedd5;color:#c2410c;}
.status-closed{background:#fee2e2;color:#b91c1c;}
.priority-low{background:#e5e7eb;color:#374151;}
.priority-medium{background:#dbeafe;color:#1d4ed8;}
.priority-high{background:#fef3c7;color:#92400e;}
.priority-urgent{background:#fee2e2;color:#b91c1c;}
.info-box{background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:14px;}
.timeline-item{border-left:3px solid #b1e96f;padding-left:15px;margin-bottom:18px;}
.reply-box{background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;}
</style>

<div class="container-fluid mt-4">

    <div class="card report-card mb-4">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Ticket Detail Report</h4>
                    <div class="text-muted small">
                        Search by ticket number, subject, user, email, or organization.
                    </div>
                </div>

                <?php if($ticket): ?>
                    <a
                        href="<?= BASE_URL ?>/reports/ticket-detail/print/<?= $ticket['id']; ?>"
                        target="_blank"
                        class="btn btn-primary-custom">
                        <i class="bi bi-printer me-1"></i>
                        Print Report
                    </a>
                <?php endif; ?>
            </div>

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="ticketSearchInput"
                    class="form-control search-input"
                    placeholder="Search ticket number, subject, user, email, organization..."
                    autocomplete="off">
            </div>

            <div id="ticketSearchResults" class="mt-3"></div>

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

                <div class="card report-card mb-4">
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
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
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
                                        <div class="border rounded p-2 mb-2 bg-light d-flex justify-content-between align-items-center">
                                            <div style="max-width:75%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"
                                                 title="<?= htmlspecialchars($attachment['original_name']); ?>">
                                                📎 <?= htmlspecialchars($attachment['original_name']); ?>
                                            </div>

                                            <a href="<?= BASE_URL ?>/attachments/ticket/download/<?= $attachment['id']; ?>"
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

                <div class="card report-card">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">Conversation</h5>

                        <?php if(empty($replies)): ?>
                            <div class="alert alert-info">No replies found.</div>
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
                                                <div class="border rounded p-2 mb-2 bg-white d-flex justify-content-between align-items-center">
                                                    <div style="max-width:75%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"
                                                         title="<?= htmlspecialchars($replyAttachment['original_name']); ?>">
                                                        📎 <?= htmlspecialchars($replyAttachment['original_name']); ?>
                                                    </div>

                                                    <a href="<?= BASE_URL ?>/attachments/reply/download/<?= $replyAttachment['id']; ?>"
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

                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card report-card mb-4">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">Ticket Information</h5>

                        <div class="mb-4">
                            <small class="text-muted">Organization</small>
                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">User</small>
                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                            </div>
                            <div class="text-muted small">
                                <?= htmlspecialchars($ticket['customer_email'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">Status</small>
                            <div class="mt-2">
                                <span class="badge-soft <?= $statusClass; ?>">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">Priority</small>
                            <div class="mt-2">
                                <span class="badge-soft <?= $priorityClass; ?>">
                                    <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Created At</small>
                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['created_at']); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Closed At</small>
                            <div class="fw-semibold mt-1">
                                <?= !empty($ticket['closed_at']) ? htmlspecialchars($ticket['closed_at']) : '-'; ?>
                            </div>
                        </div>

                        <div>
                            <small class="text-muted">Closed / Resolved By</small>
                            <div class="fw-semibold mt-1">
                                <?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card report-card">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">Status Timeline</h5>

                        <?php if(empty($statusHistory)): ?>
                            <div class="text-muted">No status updates available.</div>
                        <?php else: ?>
                            <?php foreach($statusHistory as $history): ?>
                                <div class="timeline-item">
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['old_status']))); ?>
                                        →
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $history['new_status']))); ?>
                                    </div>

                                    <small class="text-muted">
                                        Updated By <?= htmlspecialchars($history['full_name']); ?>
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

        <div class="alert alert-danger">Ticket not found.</div>

    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('ticketSearchInput');
    const resultsBox = document.getElementById('ticketSearchResults');
    let timer = null;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(timer);

        const keyword = searchInput.value.trim();

        if (keyword.length < 2) {
            resultsBox.innerHTML = '';
            return;
        }

        resultsBox.innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-success"></div>
                <div class="small text-muted mt-2">Searching tickets...</div>
            </div>
        `;

        timer = setTimeout(function () {

            fetch("<?= BASE_URL ?>/reports/ticket-search?q=" + encodeURIComponent(keyword), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.text())
            .then(html => {
                resultsBox.innerHTML = html;
            })
            .catch(() => {
                resultsBox.innerHTML = `
                    <div class="alert alert-danger">
                        Unable to search tickets.
                    </div>
                `;
            });

        }, 350);

    });

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>