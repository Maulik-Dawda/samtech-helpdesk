<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$users = is_array($users ?? null) ? $users : [];

$totalUsers = count($users);

$totalAdmins = count(array_filter(
    $users,
    static fn($u) => ($u['role'] ?? '') === 'admin'
));

$totalAgents = count(array_filter(
    $users,
    static fn($u) => ($u['role'] ?? '') === 'agent'
));

$totalNormalUsers = count(array_filter(
    $users,
    static fn($u) => ($u['role'] ?? '') === 'user'
));

$totalActive = count(array_filter(
    $users,
    static fn($u) => !empty($u['is_active'])
));

?>

<div class="container-fluid px-0">

    <!-- =======================================================
         PAGE HEADER
    ======================================================== -->

    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">

                        <i class="bi bi-people-fill"></i>

                        User Management

                    </div>

                    <h1 class="page-title">
                        Users
                    </h1>

                    <p class="page-description">
                        Manage administrators, agents and organization users from one place.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/admin/users/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-person-plus-fill me-2"></i>

                        Create User

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =======================================================
         METRICS
    ======================================================== -->

    <section class="content-section">

        <div class="metric-grid">

            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Total Users
                </div>

                <div class="metric-card-value">
                    <?= $totalUsers; ?>
                </div>

            </div>


            <div class="metric-card metric-card-danger">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Administrators
                </div>

                <div class="metric-card-value">
                    <?= $totalAdmins; ?>
                </div>

            </div>


            <div class="metric-card metric-card-info">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-headset"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Agents
                </div>

                <div class="metric-card-value">
                    <?= $totalAgents; ?>
                </div>

            </div>


            <div class="metric-card metric-card-success">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Active Accounts
                </div>

                <div class="metric-card-value">
                    <?= $totalActive; ?>
                </div>

            </div>

        </div>

    </section>



    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success mb-4">

            <i class="bi bi-check-circle me-2"></i>

            <?= htmlspecialchars($_SESSION['success']); ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger mb-4">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['error']); ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <!-- =======================================================
         USER TABLE
    ======================================================== -->

    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">

                    User Directory

                </div>

                <div class="table-card-subtitle">

                    View, edit and manage all registered accounts.

                </div>

            </div>

            <div class="app-badge app-badge-primary">

                <i class="bi bi-people"></i>

                <?= $totalUsers; ?>

            </div>

        </div>


        <div class="table-card-body">

            <?php if (empty($users)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">

                        <i class="bi bi-people"></i>

                    </div>

                    <h3 class="empty-state-title">

                        No users found

                    </h3>

                    <p class="empty-state-description">

                        Create your first user to begin using the Helpdesk.

                    </p>

                    <a
                        href="<?= BASE_URL ?>/admin/users/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-person-plus-fill me-2"></i>

                        Create User

                    </a>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                        <tr>

                            <th>User</th>

                            <th>Role</th>

                            <th>Organization</th>

                            <th>Status</th>

                            <th class="text-end">

                                Actions

                            </th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($users as $user): ?>

                            <?php

                            $role = $user['role'] ?? 'user';

                            $initial = strtoupper(substr($user['full_name'],0,1));

                            ?>

                            <tr>

                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="table-avatar">

                                            <?= htmlspecialchars($initial); ?>

                                        </div>

                                        <div>

                                            <div class="fw-semibold">

                                                <?= htmlspecialchars($user['full_name']); ?>

                                            </div>

                                            <div class="text-muted small">

                                                <?= htmlspecialchars($user['email']); ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <?php if($role === 'admin'): ?>

                                        <span class="status-badge status-closed">

                                            Administrator

                                        </span>

                                    <?php elseif($role === 'agent'): ?>

                                        <span class="status-badge status-progress">

                                            Agent

                                        </span>

                                    <?php else: ?>

                                        <span class="status-badge status-resolved">

                                            User

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $user['organization_name'] ?? '-'
                                    ); ?>

                                </td>


                                <td>

                                    <?php if($user['is_active']): ?>

                                        <span class="status-badge status-resolved">

                                            Active

                                        </span>

                                    <?php else: ?>

                                        <span class="status-badge status-closed">

                                            Inactive

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td class="text-end">

                                    <?php if($role !== 'admin'): ?>

                                        <div class="d-inline-flex gap-2">

                                            <a
                                                href="<?= BASE_URL ?>/admin/users/show/<?= $user['id']; ?>"
                                                class="table-action-btn table-action-view"
                                                title="View">

                                                <i class="bi bi-eye-fill"></i>

                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id']; ?>"
                                                class="table-action-btn table-action-edit"
                                                title="Edit User">

                                                <i class="bi bi-pencil-fill"></i>

                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/admin/users/disable/<?= $user['id']; ?>"
                                                class="table-action-btn"
                                                style="color: #ea580c; border-color: #ffedd5;"
                                                onclick="return confirm('<?= $user['is_active'] ? 'Disable' : 'Enable' ?> this user?')"
                                                title="<?= $user['is_active'] ? 'Disable' : 'Enable' ?> User">

                                                <i class="bi bi-slash-circle-fill"></i>

                                            </a>

                                            <button
                                                type="button"
                                                class="table-action-btn table-action-delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteUserModal<?= $user['id']; ?>"
                                                title="Delete User Everywhere">

                                                <i class="bi bi-trash-fill"></i>

                                            </button>

                                        </div>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade text-start" id="deleteUserModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $user['id']; ?>" aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered">

                                                <div class="modal-content border-0 shadow card-radius">

                                                    <div class="modal-header border-bottom-0 pb-0">

                                                        <h5 class="modal-title fw-bold text-danger" id="deleteUserModalLabel<?= $user['id']; ?>">

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

                                                            <strong>Critical Warning:</strong> This will delete this user <strong>everywhere in the system</strong>, including all created tickets, ticket replies, activity logs, MFA credentials, and user permissions. This action cannot be undone.

                                                        </div>

                                                    </div>

                                                    <div class="modal-footer border-top-0 pt-0 gap-2">

                                                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>

                                                        <form action="<?= BASE_URL ?>/admin/users/delete/<?= $user['id']; ?>" method="POST" class="d-inline">

                                                            <?= Csrf::field(); ?>

                                                            <button type="submit" class="btn btn-danger px-4 fw-bold">

                                                                <i class="bi bi-trash-fill me-1"></i> Confirm & Delete User

                                                            </button>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    <?php else: ?>

                                        <span class="text-muted small">

                                            Protected Account

                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="pagination-wrapper">

                    <div class="pagination-info">

                        Showing <?= $totalUsers; ?>

                        <?= $totalUsers === 1 ? 'user' : 'users'; ?>

                    </div>

                    <a
                        href="<?= BASE_URL ?>/admin/users/create"
                        class="view-all-link">

                        Create new user

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>