<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

$ticket = is_array($ticket ?? null)
    ? $ticket
    : [];

$replies = is_array($replies ?? null)
    ? $replies
    : [];

$attachments = is_array($attachments ?? null)
    ? $attachments
    : [];

$replyAttachments = is_array($replyAttachments ?? null)
    ? $replyAttachments
    : [];

$statusHistory = is_array($statusHistory ?? null)
    ? $statusHistory
    : [];

$ticketId = (int) ($ticket['id'] ?? 0);

$ticketNumber = trim(
    (string) ($ticket['ticket_no'] ?? '')
);

$subject = trim(
    (string) ($ticket['subject'] ?? '')
);

$description = trim(
    (string) ($ticket['description'] ?? '')
);

$customerName = trim(
    (string) ($ticket['customer_name'] ?? '')
);

$customerEmail = trim(
    (string) ($ticket['customer_email'] ?? '')
);

$organizationName = trim(
    (string) ($ticket['organization_name'] ?? '')
);

$priority = strtolower(
    (string) ($ticket['priority'] ?? 'medium')
);

$status = strtolower(
    (string) ($ticket['status'] ?? 'open')
);

$createdAt = !empty($ticket['created_at'])
    ? (string) $ticket['created_at']
    : '-';

$updatedAt = !empty($ticket['updated_at'])
    ? (string) $ticket['updated_at']
    : '-';

$closedAt = !empty($ticket['closed_at'])
    ? (string) $ticket['closed_at']
    : null;

$closedByAgent = trim(
    (string) ($ticket['closed_by_agent_name'] ?? '')
);

$statusLabel = ucwords(
    str_replace('_', ' ', $status)
);

$priorityLabel = ucfirst($priority);

$isClosed = $status === 'closed';

$totalReplies = count($replies);
$totalAttachments = count($attachments);

foreach ($replyAttachments as $items) {
    if (is_array($items)) {
        $totalAttachments += count($items);
    }
}

/**
 * Returns the shared ticket status class.
 */
function getAgentShowStatusClass(string $status): string
{
    return match (strtolower($status)) {
        'open' => 'status-open',
        'in_progress', 'in progress' => 'status-progress',
        'pending' => 'status-pending',
        'resolved' => 'status-resolved',
        'closed' => 'status-closed',
        default => 'status-open',
    };
}

/**
 * Returns the shared ticket priority class.
 */
function getAgentShowPriorityClass(string $priority): string
{
    return match (strtolower($priority)) {
        'low' => 'status-open',
        'medium' => 'status-progress',
        'high' => 'status-pending',
        'urgent' => 'status-closed',
        default => 'status-progress',
    };
}

/**
 * Returns the icon for an attachment based on its filename.
 */
function getAgentAttachmentIcon(string $filename): string
{
    $extension = strtolower(
        pathinfo($filename, PATHINFO_EXTENSION)
    );

    return match ($extension) {
        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'bi-file-earmark-image',
        'pdf' => 'bi-file-earmark-pdf',
        'doc', 'docx' => 'bi-file-earmark-word',
        'xls', 'xlsx' => 'bi-file-earmark-excel',
        'zip', 'rar' => 'bi-file-earmark-zip',
        'txt' => 'bi-file-earmark-text',
        default => 'bi-file-earmark',
    };
}

/**
 * Returns a readable role label.
 */
function getAgentReplyRoleLabel(string $role): string
{
    return match (strtolower($role)) {
        'admin' => 'Administrator',
        'agent' => 'Agent',
        'user' => 'Customer',
        default => ucfirst($role),
    };
}

/**
 * Returns a shared badge class for a reply role.
 */
function getAgentReplyRoleClass(string $role): string
{
    return match (strtolower($role)) {
        'admin' => 'status-closed',
        'agent' => 'status-progress',
        'user' => 'status-resolved',
        default => 'status-open',
    };
}

?>

<div class="container-fluid px-0">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">

                        <i class="bi bi-ticket-perforated-fill"></i>

                        Ticket Details

                    </div>

                    <h1 class="page-title">
                        <?= htmlspecialchars(
                            $subject !== ''
                                ? $subject
                                : 'Untitled Ticket'
                        ); ?>
                    </h1>

                    <p class="page-description">

                        <span class="fw-semibold">
                            <?= htmlspecialchars(
                                $ticketNumber !== ''
                                    ? $ticketNumber
                                    : 'Ticket'
                            ); ?>
                        </span>

                        · Review the request, communicate with the customer and
                        manage the ticket status.

                    </p>

                </div>

                <div class="page-actions d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">

                    <span class="status-badge <?= getAgentShowStatusClass($status); ?>">

                        <?= htmlspecialchars($statusLabel); ?>

                    </span>

                    <a
                        href="<?= BASE_URL ?>/reports/print-ticket-detail/<?= $ticketId; ?>"
                        target="_blank"
                        class="btn btn-outline-secondary text-nowrap">

                        <i class="bi bi-printer-fill me-1"></i>

                        Print / Download Report

                    </a>

                    <a
                        href="<?= BASE_URL ?>/agent/tickets"
                        class="btn btn-light text-nowrap">

                        <i class="bi bi-arrow-left me-2"></i>

                        Back to Tickets

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         TICKET METRICS
    ========================================================== -->
    <section class="content-section">

        <div class="metric-grid">

            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-flag-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Priority
                </div>

                <div class="metric-card-value">
                    <?= htmlspecialchars($priorityLabel); ?>
                </div>

                <div class="metric-card-meta">
                    Current urgency level
                </div>

            </div>


            <div class="metric-card metric-card-info">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Status
                </div>

                <div class="metric-card-value">
                    <?= htmlspecialchars($statusLabel); ?>
                </div>

                <div class="metric-card-meta">
                    Current ticket state
                </div>

            </div>


            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Replies
                </div>

                <div class="metric-card-value">
                    <?= $totalReplies; ?>
                </div>

                <div class="metric-card-meta">
                    Conversation messages
                </div>

            </div>


            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-paperclip"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Attachments
                </div>

                <div class="metric-card-value">
                    <?= $totalAttachments; ?>
                </div>

                <div class="metric-card-meta">
                    Attached ticket files
                </div>

            </div>

        </div>

    </section>


    <div class="row g-4">

        <!-- =====================================================
             MAIN CONTENT
        ====================================================== -->
        <div class="col-xl-8">

            <!-- Ticket Description -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Ticket Description
                        </h2>

                        <p class="ui-panel-subtitle">
                            Original issue submitted for this support request.
                        </p>

                    </div>

                    <span class="status-badge <?= getAgentShowPriorityClass($priority); ?>">

                        <i class="bi bi-flag me-1"></i>

                        <?= htmlspecialchars($priorityLabel); ?>
                        Priority

                    </span>

                </div>

                <div class="ui-panel-body">

                    <div class="mb-4">

                        <?php if ($description !== ''): ?>

                            <div class="lh-lg text-break">
                                <?= nl2br(
                                    htmlspecialchars($description)
                                ); ?>
                            </div>

                        <?php else: ?>

                            <div class="text-muted">
                                No ticket description was provided.
                            </div>

                        <?php endif; ?>

                    </div>


                    <?php if (!empty($attachments)): ?>

                        <div class="border-top pt-4">

                            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">

                                <div>

                                    <div class="fw-semibold">
                                        Ticket Attachments
                                    </div>

                                    <div class="text-muted small">
                                        Files submitted with the original ticket.
                                    </div>

                                </div>

                                <span class="app-badge app-badge-primary">

                                    <i class="bi bi-paperclip"></i>

                                    <?= count($attachments); ?>

                                </span>

                            </div>

                            <div class="list-group list-group-flush">

                                <?php foreach ($attachments as $attachment): ?>

                                    <?php
                                    $attachmentId = (int) (
                                        $attachment['id'] ?? 0
                                    );

                                    $attachmentName = trim(
                                        (string) (
                                            $attachment['original_name'] ??
                                            'Attachment'
                                        )
                                    );
                                    ?>

                                    <div class="list-group-item px-0 py-3">

                                        <div class="d-flex align-items-center justify-content-between gap-3">

                                            <div class="d-flex align-items-center gap-3 min-w-0">

                                                <div class="table-avatar flex-shrink-0">

                                                    <i class="bi <?= getAgentAttachmentIcon($attachmentName); ?>"></i>

                                                </div>

                                                <div class="min-w-0">

                                                    <div
                                                        class="fw-semibold text-truncate"
                                                        title="<?= htmlspecialchars($attachmentName); ?>">

                                                        <?= htmlspecialchars($attachmentName); ?>

                                                    </div>

                                                    <div class="text-muted small">

                                                        <?= htmlspecialchars(
                                                            strtoupper(
                                                                pathinfo(
                                                                    $attachmentName,
                                                                    PATHINFO_EXTENSION
                                                                ) ?: 'FILE'
                                                            )
                                                        ); ?>

                                                    </div>

                                                </div>

                                            </div>

                                            <a
                                                href="<?= BASE_URL ?>/attachments/ticket/download/<?= $attachmentId; ?>"
                                                class="table-action-btn table-action-view flex-shrink-0"
                                                title="Download attachment"
                                                aria-label="Download attachment">

                                                <i class="bi bi-download"></i>

                                            </a>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


            <!-- Conversation -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Conversation
                        </h2>

                        <p class="ui-panel-subtitle">
                            Communication between the customer and support team.
                        </p>

                    </div>

                    <span class="app-badge app-badge-primary">

                        <i class="bi bi-chat-dots"></i>

                        <?= $totalReplies; ?>
                        <?= $totalReplies === 1 ? 'Reply' : 'Replies'; ?>

                    </span>

                </div>

                <div class="ui-panel-body">

                    <?php if (empty($replies)): ?>

                        <div class="empty-state">

                            <div class="empty-state-icon">
                                <i class="bi bi-chat-left-text"></i>
                            </div>

                            <h3 class="empty-state-title">
                                No replies yet
                            </h3>

                            <p class="empty-state-description">
                                Start the conversation by sending the first reply.
                            </p>

                        </div>

                    <?php else: ?>

                        <div class="list-group list-group-flush">

                            <?php foreach ($replies as $reply): ?>

                                <?php
                                $replyId = (int) ($reply['id'] ?? 0);

                                $replyName = trim(
                                    (string) (
                                        $reply['full_name'] ??
                                        'Unknown User'
                                    )
                                );

                                $replyRole = strtolower(
                                    (string) ($reply['role'] ?? 'user')
                                );

                                $replyMessage = trim(
                                    (string) ($reply['message'] ?? '')
                                );

                                $replyCreatedAt = !empty($reply['created_at'])
                                    ? (string) $reply['created_at']
                                    : '-';

                                $replyInitial = strtoupper(
                                    substr(
                                        $replyName !== ''
                                            ? $replyName
                                            : 'U',
                                        0,
                                        1
                                    )
                                );

                                $currentReplyAttachments =
                                    $replyAttachments[$replyId] ?? [];

                                if (!is_array($currentReplyAttachments)) {
                                    $currentReplyAttachments = [];
                                }
                                ?>

                                <div class="list-group-item px-0 py-4">

                                    <div class="d-flex align-items-start gap-3">

                                        <div class="table-avatar flex-shrink-0">

                                            <?= htmlspecialchars($replyInitial); ?>

                                        </div>

                                        <div class="flex-grow-1 min-w-0">

                                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">

                                                <div>

                                                    <div class="d-flex flex-wrap align-items-center gap-2">

                                                        <span class="fw-semibold">

                                                            <?= htmlspecialchars($replyName); ?>

                                                        </span>

                                                        <span class="status-badge <?= getAgentReplyRoleClass($replyRole); ?>">

                                                            <?= htmlspecialchars(
                                                                getAgentReplyRoleLabel(
                                                                    $replyRole
                                                                )
                                                            ); ?>

                                                        </span>

                                                    </div>

                                                </div>

                                                <div class="text-muted small text-md-end">

                                                    <i class="bi bi-clock me-1"></i>

                                                    <?= htmlspecialchars($replyCreatedAt); ?>

                                                </div>

                                            </div>

                                            <div class="lh-lg text-break">

                                                <?= $replyMessage !== ''
                                                    ? nl2br(
                                                        htmlspecialchars(
                                                            $replyMessage
                                                        )
                                                    )
                                                    : '<span class="text-muted">No message content.</span>'; ?>

                                            </div>


                                            <?php if (!empty($currentReplyAttachments)): ?>

                                                <div class="mt-4">

                                                    <div class="fw-semibold small mb-2">

                                                        <i class="bi bi-paperclip me-1"></i>

                                                        Attachments

                                                    </div>

                                                    <div class="list-group">

                                                        <?php foreach ($currentReplyAttachments as $replyAttachment): ?>

                                                            <?php
                                                            $replyAttachmentId = (int) (
                                                                $replyAttachment['id'] ?? 0
                                                            );

                                                            $replyAttachmentName = trim(
                                                                (string) (
                                                                    $replyAttachment['original_name'] ??
                                                                    'Attachment'
                                                                )
                                                            );
                                                            ?>

                                                            <div class="list-group-item">

                                                                <div class="d-flex align-items-center justify-content-between gap-3">

                                                                    <div class="d-flex align-items-center gap-2 min-w-0">

                                                                        <i class="bi <?= getAgentAttachmentIcon($replyAttachmentName); ?> fs-5 flex-shrink-0"></i>

                                                                        <span
                                                                            class="text-truncate"
                                                                            title="<?= htmlspecialchars($replyAttachmentName); ?>">

                                                                            <?= htmlspecialchars($replyAttachmentName); ?>

                                                                        </span>

                                                                    </div>

                                                                    <a
                                                                        href="<?= BASE_URL ?>/attachments/reply/download/<?= $replyAttachmentId; ?>"
                                                                        class="table-action-btn table-action-view flex-shrink-0"
                                                                        title="Download attachment"
                                                                        aria-label="Download attachment">

                                                                        <i class="bi bi-download"></i>

                                                                    </a>

                                                                </div>

                                                            </div>

                                                        <?php endforeach; ?>

                                                    </div>

                                                </div>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


            <!-- Reply Form -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Send Reply
                        </h2>

                        <p class="ui-panel-subtitle">
                            Respond to the customer and optionally attach files.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <?php if (!$isClosed): ?>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/agent/tickets/reply/<?= $ticketId; ?>"
                            enctype="multipart/form-data">

                            <?= Csrf::field(); ?>

                            <div class="mb-4">

                                <label
                                    for="reply-message"
                                    class="form-label">

                                    Reply Message

                                    <span class="text-danger">*</span>

                                </label>

                                <textarea
                                    id="reply-message"
                                    name="message"
                                    rows="7"
                                    class="form-control"
                                    placeholder="Write your response to the customer..."
                                    required></textarea>

                            </div>

                            <div class="mb-4">

                                <label
                                    for="reply-attachments"
                                    class="form-label">

                                    Attachments

                                </label>

                                <input
                                    type="file"
                                    id="reply-attachments"
                                    name="attachments[]"
                                    class="form-control"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                                <div class="form-text mt-2">

                                    JPG, PNG, PDF, Word, Excel, TXT, ZIP and RAR.
                                    Multiple files allowed, 5 MB per file.

                                </div>

                            </div>

                            <div class="d-flex justify-content-end">

                                <button
                                    type="submit"
                                    class="btn btn-primary-custom">

                                    <i class="bi bi-send-fill me-2"></i>

                                    Send Reply

                                </button>

                            </div>

                        </form>

                    <?php else: ?>

                        <div class="alert alert-success mb-0">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            This ticket is closed. New replies are disabled.

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </div>


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->
        <div class="col-xl-4">

            <!-- Ticket Details -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Ticket Details
                        </h2>

                        <p class="ui-panel-subtitle">
                            Customer and request information.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="list-group list-group-flush">

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Ticket Number
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars(
                                    $ticketNumber !== ''
                                        ? $ticketNumber
                                        : '-'
                                ); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Customer
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars(
                                    $customerName !== ''
                                        ? $customerName
                                        : '-'
                                ); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Email
                            </div>

                            <div class="fw-semibold text-end text-break">

                                <?php if ($customerEmail !== ''): ?>

                                    <a
                                        href="mailto:<?= htmlspecialchars($customerEmail); ?>"
                                        class="text-decoration-none">

                                        <?= htmlspecialchars($customerEmail); ?>

                                    </a>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </div>

                        </div>

                        <?php if ($organizationName !== ''): ?>

                            <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                                <div class="text-muted">
                                    Organization
                                </div>

                                <div class="fw-semibold text-end">
                                    <?= htmlspecialchars($organizationName); ?>
                                </div>

                            </div>

                        <?php endif; ?>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Priority
                            </div>

                            <span class="status-badge <?= getAgentShowPriorityClass($priority); ?>">
                                <?= htmlspecialchars($priorityLabel); ?>
                            </span>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Status
                            </div>

                            <span class="status-badge <?= getAgentShowStatusClass($status); ?>">
                                <?= htmlspecialchars($statusLabel); ?>
                            </span>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Created
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($createdAt); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Last Updated
                            </div>

                            <div class="fw-semibold text-end">
                                <?= htmlspecialchars($updatedAt); ?>
                            </div>

                        </div>

                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                            <div class="text-muted">
                                Closed / Resolved By
                            </div>

                            <div class="fw-semibold text-end">

                                <?= htmlspecialchars(
                                    $closedByAgent !== ''
                                        ? $closedByAgent
                                        : 'Not closed yet'
                                ); ?>

                            </div>

                        </div>

                        <?php if ($closedAt !== null): ?>

                            <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">

                                <div class="text-muted">
                                    Closed At
                                </div>

                                <div class="fw-semibold text-end">
                                    <?= htmlspecialchars($closedAt); ?>
                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </section>


            <!-- Status Management -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Status Management
                        </h2>

                        <p class="ui-panel-subtitle">
                            Change the current ticket workflow status.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="mb-4">

                        <label class="form-label">
                            Current Status
                        </label>

                        <div>

                            <span class="status-badge <?= getAgentShowStatusClass($status); ?>">

                                <i class="bi bi-circle-fill me-1"></i>

                                <?= htmlspecialchars($statusLabel); ?>

                            </span>

                        </div>

                    </div>


                    <?php if (!$isClosed): ?>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/agent/tickets/status/<?= $ticketId; ?>"
                            id="statusUpdateForm"
                            data-no-loader="true">

                            <?= Csrf::field(); ?>

                            <div class="mb-4">

                                <label
                                    for="ticketStatus"
                                    class="form-label">

                                    Change Status To

                                </label>

                                <select
                                    name="status"
                                    id="ticketStatus"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select a new status
                                    </option>

                                    <?php if ($status !== 'open'): ?>
                                        <option value="open">
                                            Open
                                        </option>
                                    <?php endif; ?>

                                    <?php if ($status !== 'in_progress'): ?>
                                        <option value="in_progress">
                                            In Progress
                                        </option>
                                    <?php endif; ?>

                                    <?php if ($status !== 'pending'): ?>
                                        <option value="pending">
                                            Pending
                                        </option>
                                    <?php endif; ?>

                                    <?php if ($status !== 'resolved'): ?>
                                        <option value="resolved">
                                            Resolved
                                        </option>
                                    <?php endif; ?>

                                    <?php if ($status !== 'closed' && ($_SESSION['auth_user_role'] ?? '') === 'admin'): ?>
                                        <option value="closed">
                                            Closed
                                        </option>
                                    <?php endif; ?>

                                </select>

                                <?php if (($_SESSION['auth_user_role'] ?? '') !== 'admin'): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i> Note: Only administrators can close tickets.
                                    </small>
                                <?php endif; ?>

                            </div>

                            <input
                                type="hidden"
                                name="resolution_message"
                                id="resolutionMessageInput">

                            <button
                                type="submit"
                                class="btn btn-primary-custom w-100">

                                <i class="bi bi-arrow-repeat me-2"></i>

                                Update Status

                            </button>

                        </form>

                    <?php else: ?>

                        <div class="alert alert-success mb-0">

                            <i class="bi bi-lock-fill me-2"></i>

                            This ticket has been closed.

                        </div>

                    <?php endif; ?>

                </div>

            </section>


            <!-- Status Timeline -->
            <section class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div class="ui-panel-title-wrap">

                        <h2 class="ui-panel-title">
                            Status Timeline
                        </h2>

                        <p class="ui-panel-subtitle">
                            Ticket workflow and status history.
                        </p>

                    </div>

                    <span class="app-badge app-badge-primary">

                        <i class="bi bi-clock-history"></i>

                        <?= count($statusHistory); ?>

                    </span>

                </div>

                <div class="ui-panel-body">

                    <?php if (empty($statusHistory)): ?>

                        <div class="empty-state">

                            <div class="empty-state-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <h3 class="empty-state-title">
                                No status changes
                            </h3>

                            <p class="empty-state-description">
                                Status updates will appear here.
                            </p>

                        </div>

                    <?php else: ?>

                        <div class="list-group list-group-flush">

                            <?php foreach ($statusHistory as $history): ?>

                                <?php
                                $oldStatus = strtolower(
                                    (string) (
                                        $history['old_status'] ?? 'open'
                                    )
                                );

                                $newStatus = strtolower(
                                    (string) (
                                        $history['new_status'] ?? 'open'
                                    )
                                );

                                $changedBy = trim(
                                    (string) (
                                        $history['full_name'] ??
                                        'Unknown User'
                                    )
                                );

                                $historyCreatedAt = !empty(
                                    $history['created_at']
                                )
                                    ? (string) $history['created_at']
                                    : '-';
                                ?>

                                <div class="list-group-item px-0 py-3">

                                    <div class="d-flex align-items-start gap-3">

                                        <div class="table-avatar flex-shrink-0">

                                            <i class="bi bi-arrow-repeat"></i>

                                        </div>

                                        <div class="flex-grow-1">

                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                                                <span class="status-badge <?= getAgentShowStatusClass($oldStatus); ?>">

                                                    <?= htmlspecialchars(
                                                        ucwords(
                                                            str_replace(
                                                                '_',
                                                                ' ',
                                                                $oldStatus
                                                            )
                                                        )
                                                    ); ?>

                                                </span>

                                                <i class="bi bi-arrow-right text-muted"></i>

                                                <span class="status-badge <?= getAgentShowStatusClass($newStatus); ?>">

                                                    <?= htmlspecialchars(
                                                        ucwords(
                                                            str_replace(
                                                                '_',
                                                                ' ',
                                                                $newStatus
                                                            )
                                                        )
                                                    ); ?>

                                                </span>

                                            </div>

                                            <div class="small">

                                                Changed by

                                                <span class="fw-semibold">
                                                    <?= htmlspecialchars($changedBy); ?>
                                                </span>

                                            </div>

                                            <div class="text-muted small mt-1">

                                                <i class="bi bi-calendar3 me-1"></i>

                                                <?= htmlspecialchars($historyCreatedAt); ?>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </div>

    </div>

</div>


<!-- =============================================================
     RESOLUTION MODAL
============================================================== -->
<div
    class="modal fade"
    id="resolutionModal"
    tabindex="-1"
    aria-labelledby="resolutionModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0">

                <div>

                    <h5
                        class="modal-title fw-bold"
                        id="resolutionModalLabel">

                        Close Ticket

                    </h5>

                    <div class="text-muted small mt-1">
                        Add the final resolution before closing this ticket.
                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <label
                    for="resolutionMessageTextarea"
                    class="form-label">

                    Resolution Message

                    <span class="text-danger">*</span>

                </label>

                <textarea
                    id="resolutionMessageTextarea"
                    class="form-control"
                    rows="6"
                    placeholder="Explain how the issue was resolved."></textarea>

                <div
                    class="text-danger small mt-2 d-none"
                    id="resolutionError">

                    <i class="bi bi-exclamation-circle me-1"></i>

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
                    class="btn btn-primary-custom"
                    id="confirmCloseTicket">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    Close Ticket

                </button>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const statusForm =
        document.getElementById('statusUpdateForm');

    const statusSelect =
        document.getElementById('ticketStatus');

    const resolutionInput =
        document.getElementById('resolutionMessageInput');

    const resolutionTextarea =
        document.getElementById('resolutionMessageTextarea');

    const resolutionError =
        document.getElementById('resolutionError');

    const confirmCloseButton =
        document.getElementById('confirmCloseTicket');

    const resolutionModalElement =
        document.getElementById('resolutionModal');

    if (
        !statusForm ||
        !statusSelect ||
        !resolutionInput ||
        !resolutionTextarea ||
        !resolutionError ||
        !confirmCloseButton ||
        !resolutionModalElement
    ) {
        return;
    }

    const resolutionModal =
        new bootstrap.Modal(resolutionModalElement);

    statusForm.addEventListener('submit', function (event) {

        if (statusSelect.value !== 'closed') {
            return;
        }

        if (resolutionInput.value.trim() !== '') {
            return;
        }

        event.preventDefault();

        if (typeof hideSamtechLoader === 'function') {
            hideSamtechLoader();
        }

        resolutionTextarea.value = '';
        resolutionInput.value = '';
        resolutionError.classList.add('d-none');

        resolutionModal.show();

    });

    resolutionTextarea.addEventListener('input', function () {

        if (resolutionTextarea.value.trim() !== '') {
            resolutionError.classList.add('d-none');
        }

    });

    confirmCloseButton.addEventListener('click', function () {

        const resolutionMessage =
            resolutionTextarea.value.trim();

        if (resolutionMessage === '') {

            resolutionError.classList.remove('d-none');
            resolutionTextarea.focus();

            return;

        }

        resolutionInput.value = resolutionMessage;

        resolutionModal.hide();

        if (typeof showSamtechLoader === 'function') {
            showSamtechLoader();
        }

        statusForm.submit();

    });

    resolutionModalElement.addEventListener(
        'hidden.bs.modal',
        function () {

            resolutionError.classList.add('d-none');

        }
    );

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>