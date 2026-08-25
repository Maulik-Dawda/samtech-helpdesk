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

        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h4 class="fw-bold mb-1">
                    User Management
                </h4>

                <div class="text-muted small">
                    Manage admins, agents and users.
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                    <input
                        type="search"
                        id="agentUserSearchInput"
                        class="form-control ps-5"
                        style="min-width: 240px;"
                        placeholder="Search users..."
                        autocomplete="off">
                </div>

                <a
                    href="<?= BASE_URL ?>/agent/users/create"
                    class="btn btn-primary-custom">
                    Create User
                </a>
            </div>

        </div>

        <div class="card-body p-4">

            <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
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
                            <th width="240">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="agentUserTableBody">

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

                                    <?php if ($user['role'] !== 'agent' && $user['role'] !== 'admin'): ?>

                                        <a
                                            href="<?= BASE_URL ?>/agent/users/edit/<?= $user['id']; ?>"
                                            class="action-link me-1">
                                            Edit
                                        </a>

                                        <?php if ((int)$user['is_active'] === 1): ?>

                                            <a
                                                href="<?= BASE_URL ?>/agent/users/disable/<?= $user['id']; ?>"
                                                class="action-link me-1"
                                                onclick="return confirm('Are you sure you want to disable this user?')">
                                                Disable
                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="<?= BASE_URL ?>/agent/users/disable/<?= $user['id']; ?>"
                                                class="action-link me-1"
                                                onclick="return confirm('Are you sure you want to enable this user?')">
                                                Enable
                                            </a>

                                        <?php endif; ?>

                                        <button
                                            type="button"
                                            class="action-link text-danger border-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteAgentUserModal<?= $user['id']; ?>"
                                            title="Delete User">
                                            Delete
                                        </button>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade text-start" id="deleteAgentUserModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="deleteAgentUserModalLabel<?= $user['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow card-radius">
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-danger" id="deleteAgentUserModalLabel<?= $user['id']; ?>">
                                                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Delete User Everywhere
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <p class="mb-3 fs-6">
                                                            Are you sure you want to permanently delete user <strong class="text-dark"><?= htmlspecialchars($user['full_name']); ?></strong> (<code><?= htmlspecialchars($user['email']); ?></code>)?
                                                        </p>
                                                        <div class="alert alert-danger bg-danger-subtle text-danger border-0 p-3 mb-0 rounded-3 small">
                                                            <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                                            <strong>Warning:</strong> This will delete this user <strong>everywhere in the system</strong>, including all created tickets and replies. This action cannot be undone.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0 gap-2">
                                                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="<?= BASE_URL ?>/agent/users/delete/<?= $user['id']; ?>" method="POST" class="d-inline">
                                                            <?= Csrf::field(); ?>
                                                            <button type="submit" class="btn btn-danger px-4 fw-bold">
                                                                <i class="bi bi-trash-fill me-1"></i> Confirm & Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>

                                        <span class="text-muted small">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('agentUserSearchInput');
    const tbody = document.getElementById('agentUserTableBody');
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