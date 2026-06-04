<?php require_once "../app/Views/layouts/header.php"; ?>

<style>
.card-radius{border-radius:18px;}
.badge-soft{
    padding:7px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}
.role-admin{background:#fee2e2;color:#991b1b;}
.role-agent{background:#ede9fe;color:#6d28d9;}
.role-user{background:#dcfce7;color:#15803d;}
.log-action{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:8px 10px;
    font-weight:600;
}
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius mb-4">
        <div class="card-header bg-white p-4">
            <h4 class="fw-bold mb-1">Activity Logs</h4>
            <div class="text-muted small">
                Track user activity, login actions, ticket actions and security events.
            </div>
        </div>

        <div class="card-body p-4">

            <form method="GET" action="<?= BASE_URL ?>/admin/activity-logs">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>

                            <?php foreach($users as $user): ?>
                                <option
                                    value="<?= $user['id']; ?>"
                                    <?= $filters['user_id'] == $user['id'] ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($user['full_name'] . ' - ' . $user['email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin" <?= $filters['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="agent" <?= $filters['role'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
                            <option value="user" <?= $filters['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Action</label>
                        <input
                            type="text"
                            name="action"
                            class="form-control"
                            placeholder="Example: Login, Ticket, Password"
                            value="<?= htmlspecialchars($filters['action']); ?>"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['date_from']); ?>"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['date_to']); ?>"
                        >
                    </div>

                    <div class="col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            Apply Filters
                        </button>

                        <a href="<?= BASE_URL ?>/admin/activity-logs" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card border-0 shadow-sm card-radius">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Logs</h5>
                <span class="text-muted small">
                    Showing latest <?= count($logs); ?> records
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if(empty($logs)): ?>

                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No activity logs found.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach($logs as $log): ?>

                                <?php
                                    $roleClass = match($log['role'] ?? ''){
                                        'admin' => 'role-admin',
                                        'agent' => 'role-agent',
                                        'user' => 'role-user',
                                        default => 'role-user'
                                    };
                                ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($log['created_at']); ?>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($log['full_name'] ?? 'System'); ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($log['email'] ?? '-'); ?>
                                        </small>
                                    </td>

                                    <td>
                                        <span class="badge-soft <?= $roleClass; ?>">
                                            <?= htmlspecialchars(ucfirst($log['role'] ?? 'System')); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="log-action">
                                            <?= htmlspecialchars($log['action']); ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($log['ip_address'] ?? '-'); ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<?php require_once "../app/Views/layouts/footer.php"; ?>