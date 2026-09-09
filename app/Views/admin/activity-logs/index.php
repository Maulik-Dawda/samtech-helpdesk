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

$dateFrom = $filters['date_from'] ?? '';
$dateTo = $filters['date_to'] ?? '';
$selectedUserId = $filters['user_id'] ?? '';
$selectedRole = $filters['role'] ?? '';
$actionSearch = $filters['action'] ?? '';
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
                        View, search and filter all system activities, security events, and user actions by date & time range.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTER CARD -->
    <section class="ui-card mb-4">
        <div class="ui-card-body p-4">
            <form method="GET" action="<?= BASE_URL ?>/admin/activity-logs" id="activityLogFilterForm">
                
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-funnel-fill me-1 text-primary"></i> Filter Audit Logs
                    </h6>

                    <!-- Quick Range Presets -->
                    <div class="btn-group btn-group-sm flex-wrap gap-1" role="group">
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickDateRange('today')">Today</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickDateRange('yesterday')">Yesterday</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickDateRange('7days')">Last 7 Days</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickDateRange('30days')">Last 30 Days</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickDateRange('thisMonth')">This Month</button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-muted small fw-semibold">Start Date & Time</label>
                        <input
                            type="datetime-local"
                            name="date_from"
                            id="filterDateFrom"
                            class="form-control form-control-sm"
                            value="<?= htmlspecialchars($dateFrom); ?>">
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-muted small fw-semibold">End Date & Time</label>
                        <input
                            type="datetime-local"
                            name="date_to"
                            id="filterDateTo"
                            class="form-control form-control-sm"
                            value="<?= htmlspecialchars($dateTo); ?>">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small fw-semibold">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id']; ?>" <?= (string)$selectedUserId === (string)$u['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($u['full_name'] ?? $u['email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small fw-semibold">Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="agent" <?= $selectedRole === 'agent' ? 'selected' : ''; ?>>Agent</option>
                            <option value="user" <?= $selectedRole === 'user' ? 'selected' : ''; ?>>User / Customer</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <label class="form-label text-muted small fw-semibold">Action Keywords</label>
                        <input
                            type="text"
                            name="action"
                            class="form-control form-control-sm"
                            placeholder="e.g. Login, Update"
                            value="<?= htmlspecialchars($actionSearch); ?>">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 mt-3 pt-2 border-top">
                    <a href="<?= BASE_URL ?>/admin/activity-logs" class="btn btn-sm btn-light border">
                        <i class="bi bi-x-circle me-1"></i> Clear Filters
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i> Apply Filters
                    </button>
                </div>

            </form>
        </div>
    </section>

    <!-- TABLE CARD WITH RESULTS -->
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
                <div class="app-badge app-badge-primary">
                    <i class="bi bi-list-check me-1"></i> <?= number_format($totalRecords); ?> Total Logs
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
                        No activity records match your date range or filter criteria.
                    </p>
                    <a href="<?= BASE_URL ?>/admin/activity-logs" class="btn btn-sm btn-light border mt-2">
                        Reset Filters
                    </a>
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
                <?php require ROOT_PATH . "/app/Views/partials/pagination.php"; ?>

            <?php endif; ?>

        </div>

    </section>

</div>

<script>
function formatDateForInput(date) {
    const pad = (n) => (n < 10 ? '0' + n : n);
    const yyyy = date.getFullYear();
    const mm = pad(date.getMonth() + 1);
    const dd = pad(date.getDate());
    const hh = pad(date.getHours());
    const min = pad(date.getMinutes());
    return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
}

function setQuickDateRange(rangeType) {
    const fromInput = document.getElementById('filterDateFrom');
    const toInput = document.getElementById('filterDateTo');
    const form = document.getElementById('activityLogFilterForm');
    
    if (!fromInput || !toInput || !form) return;

    const now = new Date();
    let startDate = new Date();
    let endDate = new Date();

    if (rangeType === 'today') {
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(23, 59, 59, 999);
    } else if (rangeType === 'yesterday') {
        startDate.setDate(now.getDate() - 1);
        startDate.setHours(0, 0, 0, 0);
        endDate.setDate(now.getDate() - 1);
        endDate.setHours(23, 59, 59, 999);
    } else if (rangeType === '7days') {
        startDate.setDate(now.getDate() - 7);
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(23, 59, 59, 999);
    } else if (rangeType === '30days') {
        startDate.setDate(now.getDate() - 30);
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(23, 59, 59, 999);
    } else if (rangeType === 'thisMonth') {
        startDate = new Date(now.getFullYear(), now.getMonth(), 1, 0, 0, 0, 0);
        endDate.setHours(23, 59, 59, 999);
    }

    fromInput.value = formatDateForInput(startDate);
    toInput.value = formatDateForInput(endDate);
    form.submit();
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>