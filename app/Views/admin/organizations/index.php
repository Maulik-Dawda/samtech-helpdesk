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
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">

            <div>
                <h4 class="fw-bold mb-1">
                    Organization Management
                </h4>

                <div class="text-muted small">
                    Create and manage organizations.
                </div>
            </div>

            <a
                href="<?= BASE_URL ?>/admin/organizations/create"
                class="btn btn-primary-custom"
            >
                Create Organization
            </a>

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
                            <th>Phone</th>
                            <th>Max Users</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach($organizations as $organization): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($organization['name']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($organization['email'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($organization['phone'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($organization['max_users']); ?>
                                </td>

                                <td>

                                    <?php if($organization['is_active']): ?>

                                        <span class="badge-soft status-active">
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="badge-soft status-inactive">
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <a
                                        href="<?= BASE_URL ?>/admin/organizations/edit/<?= $organization['id']; ?>"
                                        class="action-link"
                                    >
                                        Edit
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