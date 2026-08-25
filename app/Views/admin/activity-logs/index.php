<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logs = is_array($logs ?? null) ? $logs : [];
$users = is_array($users ?? null) ? $users : [];
$filters = is_array($filters ?? null) ? $filters : [];

$page = (int)($page ?? 1);
$totalPages = (int)($totalPages ?? 1);
$totalRecords = (int)($totalRecords ?? count($logs));
?>

<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <section class="ui-panel mb-4">
        <div class="ui-panel-body">
            <div class="page-header mb-0">
                <div class="page-header-content">
                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-clock-history"></i> System Audit
                    </div>
                    <h1 class="page-title">Activity Logs</h1>
                    <p class="page-description">
                        View, search and filter all system activities, authentication events, and user actions.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- TABLE CARD WITH SEARCH & FILTERS -->
    <section class="table-card content-section">

        <div class="table-card-header">
            <div>
                <div class="table-card-title">
                    System Audit Trail
                </div>
                <div class="table-card-subtitle">
                    Real-time log of security events and system interactions.
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                    <input
                        type="search"
                        id="activityLogSearchInput"
                        class="form-control ps-5"
                        style="min-width: 260px;"
                        placeholder="Search logs..."
                        autocomplete="off">
                </div>

                <div class="app-badge app-badge-primary">
                    <i class="bi bi-list-check me-1"></i> <?= $totalRecords; ?> Total Logs
                </div>
            </div>
        </div>

        <div class="table-card-body">

            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="empty-state-title">No Activity Logs Found</h3>
                    <p class="empty-state-description">
                        No activity records match your criteria.
                    </p>
                </div>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody id="activityLogTableBody">
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars($log['user_name'] ?? $log['user_id'] ?? 'System / Guest'); ?>
                                        </div>
                                        <?php if (!empty($log['email'])): ?>
                                            <div class="text-muted small"><?= htmlspecialchars($log['email']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-soft status-resolved">
                                            <?= htmlspecialchars(ucfirst($log['role'] ?? 'User')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">
                                            <?= htmlspecialchars($log['action'] ?? ''); ?>
                                        </div>
                                        <?php if (!empty($log['details'])): ?>
                                            <div class="text-muted small"><?= htmlspecialchars($log['details']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?></code>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <?= date('M d, Y H:i:s', strtotime($log['created_at'] ?? 'now')); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper mt-3">
                        <div class="pagination-info">
                            Showing Page <strong><?= $page; ?></strong> of <strong><?= $totalPages; ?></strong>
                        </div>
                        <ul class="pagination mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>/admin/activity-logs?page=<?= $page - 1; ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>/admin/activity-logs?page=<?= $i; ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>/admin/activity-logs?page=<?= $page + 1; ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('activityLogSearchInput');
    const tbody = document.getElementById('activityLogTableBody');
    if (!input || !tbody) return;

    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>