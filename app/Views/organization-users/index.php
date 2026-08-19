<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>

.page-icon{
    width:60px;
    height:60px;
    border-radius:18px;
    background:#eef9de;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card-radius{
    border-radius:20px;
}

.table-modern thead th{
    background:#f8fafc;
    border:none;
    font-size:13px;
    font-weight:700;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:.04em;
    padding:16px;
}

.table-modern tbody td{
    border-top:1px solid #edf2f7;
    vertical-align:middle;
    padding:18px 16px;
}

.table-modern tbody tr{
    transition:.2s;
}

.table-modern tbody tr:hover{
    background:#fafafa;
}

.avatar-circle{
    width:42px;
    height:42px;
    border-radius:50%;
    background:#b1e96f;
    color:#111827;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:15px;
}

.badge-soft{
    padding:8px 14px;
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

.total-badge{
    background:#eef9de;
    color:#5b8d23;
    padding:6px 14px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
}

.search-box{
    max-width:320px;
}

.empty-state{
    padding:60px 20px;
    text-align:center;
}

.empty-state i{
    font-size:60px;
    color:#d1d5db;
}

</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="d-flex align-items-center">

            <div class="page-icon me-3">

                <i class="bi bi-people-fill fs-3"
                   style="color:#7db343;"></i>

            </div>

            <div>

                <h3 class="fw-bold mb-1">
                    Organization Users
                </h3>

                <p class="text-muted mb-0">
                    Manage users within your organization.
                </p>

            </div>

        </div>

        <a href="<?= BASE_URL ?>/organization-users/create"
           class="btn btn-primary-custom">

            <i class="bi bi-person-plus-fill me-2"></i>

            Add User

        </a>

    </div>

    <?php if(session_status()===PHP_SESSION_NONE) session_start(); ?>

    <?php if(!empty($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">

            <i class="bi bi-check-circle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['success']) ?>

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if(!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['error']) ?>

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <span class="total-badge">

                    Total Users :
                    <?= count($organizationUsers) ?>

                </span>

                <div class="search-box">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search users...">

                </div>

            </div>

            <?php if(empty($organizationUsers)): ?>

                <div class="empty-state">

                    <i class="bi bi-people"></i>

                    <h5 class="fw-bold mt-3">
                        No Users Found
                    </h5>

                    <p class="text-muted">

                        Create your first organization user to get started.

                    </p>

                    <a href="<?= BASE_URL ?>/organization-users/create"
                       class="btn btn-primary-custom">

                        Create User

                    </a>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-modern align-middle">

                        <thead>

                            <tr>

                                <th>User</th>

                                <th>Email</th>

                                <th>Organization</th>

                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach($organizationUsers as $user): ?>

                            <tr>

                                <td>

                                    <div class="d-flex align-items-center">

                                        <div class="avatar-circle me-3">

                                            <?= strtoupper(substr($user['full_name'],0,1)); ?>

                                        </div>

                                        <div>

                                            <div class="fw-semibold">

                                                <?= htmlspecialchars($user['full_name']); ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <?= htmlspecialchars($user['email']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($user['organization_name']); ?>

                                </td>

                                <td>

                                    <?php if((int)$user['is_active']): ?>

                                        <span class="badge-soft status-active">

                                            <i class="bi bi-check-circle-fill me-1"></i>

                                            Active

                                        </span>

                                    <?php else: ?>

                                        <span class="badge-soft status-inactive">

                                            <i class="bi bi-x-circle-fill me-1"></i>

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

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>