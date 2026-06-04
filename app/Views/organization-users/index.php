<?php require_once "../app/Views/layouts/header.php"; ?>

<style>
.badge-soft{
    padding:7px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

.status-active{
    background:#dcfce7;
    color:#15803d;
}

.status-inactive{
    background:#fee2e2;
    color:#b91c1c;
}

.view-link{
    color:#111827;
    border:1px solid #d1d5db;
    background:transparent;
    border-radius:8px;
    padding:5px 12px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.view-link:hover{
    background:#f3f4f6;
    color:#111827;
}

.card-radius{
    border-radius:18px;
}
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">

            <div>
                <h4 class="fw-bold mb-1">
                    Organization Users
                </h4>

                <div class="text-muted small">
                    Manage users within your organization.
                </div>
            </div>

            <a
                href="<?= BASE_URL ?>/organization-users/create"
                class="btn btn-primary-custom"
            >
                Add User
            </a>

        </div>

        <div class="card-body p-4">

            <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if(isset($_SESSION['success'])): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>

            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>

            <?php endif; ?>

            <?php if(empty($organizationUsers)): ?>

                <div class="alert alert-info">
                    No organization users found.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Organization</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($organizationUsers as $user): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($user['full_name']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($user['email']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($user['organization_name']); ?>
                                    </td>

                                    <td>

                                        <?php if((int)$user['is_active'] === 1): ?>

                                            <span class="badge-soft status-active">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge-soft status-inactive">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once "../app/Views/layouts/footer.php"; ?>