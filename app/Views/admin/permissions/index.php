<?php require_once "../app/Views/layouts/header.php"; ?>

<style>
.card-radius{
    border-radius:18px;
}

.action-link{
    color:#111827;
    border:1px solid #d1d5db;
    background:transparent;
    border-radius:8px;
    padding:6px 12px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.action-link:hover{
    background:#f3f4f6;
    color:#111827;
}
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-header bg-white p-4">

            <h4 class="fw-bold mb-1">
                Permission Management
            </h4>

            <div class="text-muted small">
                Assign module permissions to users and agents.
            </div>

        </div>

        <div class="card-body p-4">

            <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if(isset($_SESSION['success'])): ?>

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
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach($users as $user): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user['email']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(ucfirst($user['role'])); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $user['organization_name'] ?? '-'
                                    ); ?>
                                </td>

                                <td>

                                    <a
                                        href="<?= BASE_URL ?>/admin/permissions/edit/<?= $user['id']; ?>"
                                        class="action-link"
                                    >
                                        Manage Permissions
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once "../app/Views/layouts/footer.php"; ?>