<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
    .card-radius {
        border-radius: 18px;
    }

    .action-link {
        color: #111827;
        border: 1px solid #d1d5db;
        background: transparent;
        border-radius: 8px;
        padding: 6px 12px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }

    .action-link:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .badge-soft {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .role-admin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-agent {
        background: #ede9fe;
        color: #6d28d9;
    }

    .role-user {
        background: #dcfce7;
        color: #15803d;
    }
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-header bg-white p-4 d-flex justify-content-between">

            <div>
                <h4 class="fw-bold mb-1">
                    User Management
                </h4>

                <div class="text-muted small">
                    Manage admins, agents and users.
                </div>
            </div>

            <a
                href="<?= BASE_URL ?>/agent/users/create"
                class="btn btn-primary-custom">
                Create User
            </a>

        </div>

        <div class="card-body p-4">

            <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Organization</th>
                            <th>Status</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user['email']); ?>
                                </td>

                                <td>

                                    <?php
                                    $roleClass = match ($user['role']) {
                                        'admin' => 'role-admin',
                                        'agent' => 'role-agent',
                                        default => 'role-user'
                                    };
                                    ?>

                                    <span class="badge-soft <?= $roleClass ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $user['organization_name'] ?? '-'
                                    ); ?>
                                </td>

                                <td>

                                    <?php if ($user['is_active']): ?>

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if ($user['role'] !== 'agent'): ?>

                                        <?php if ((int)$user['is_active'] === 1): ?>

                                            <a
                                                href="<?= BASE_URL ?>/agent/users/disable/<?= $user['id']; ?>"
                                                class="action-link"
                                                onclick="return confirm('Are you sure you want to disable this user?')">
                                                Disable
                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="<?= BASE_URL ?>/agent/users/disable/<?= $user['id']; ?>"
                                                class="action-link"
                                                onclick="return confirm('Are you sure you want to enable this user?')">
                                                Enable
                                            </a>

                                        <?php endif; ?>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            Protected
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>