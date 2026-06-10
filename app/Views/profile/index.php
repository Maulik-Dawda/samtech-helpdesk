<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>

.profile-card{
    border:none;
    border-radius:24px;
}

.profile-avatar{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#b1e96f;
    color:#111827;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    font-weight:800;
}

.info-label{
    color:#6b7280;
    font-size:13px;
    margin-bottom:4px;
}

.info-value{
    font-weight:600;
    color:#111827;
}

.permission-badge{
    background:#eef2ff;
    color:#4338ca;
    border-radius:999px;
    padding:8px 12px;
    display:inline-block;
    margin:4px;
    font-size:12px;
    font-weight:600;
}

.info-box{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:16px;
}

</style>

<?php

$nameParts = explode(' ', trim($user['full_name'] ?? ''));

$initials = '';

if (!empty($nameParts[0])) {
    $initials .= strtoupper(substr($nameParts[0],0,1));
}

if (!empty($nameParts[1])) {
    $initials .= strtoupper(substr($nameParts[1],0,1));
}

if (empty($initials)) {
    $initials = 'U';
}

?>

<div class="container-fluid mt-4">

    <div class="row">

        <div class="col-lg-4">

            <div class="card shadow-sm profile-card">

                <div class="card-body text-center p-4">

                    <div class="profile-avatar mx-auto mb-3">
                        <?= htmlspecialchars($initials); ?>
                    </div>

                    <h4 class="fw-bold mb-1">
                        <?= htmlspecialchars($user['full_name']); ?>
                    </h4>

                    <div class="text-muted mb-3">
                        <?= ucfirst(htmlspecialchars($user['role'])); ?>
                    </div>

                    <span class="badge bg-success">
                        Active Account
                    </span>

                </div>

            </div>

        </div>

        <div class="col-lg-8">

            <div class="card shadow-sm profile-card">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Profile Information
                    </h5>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    Full Name
                                </div>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    Email Address
                                </div>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['email']); ?>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    Role
                                </div>

                                <div class="info-value">
                                    <?= ucfirst(htmlspecialchars($user['role'])); ?>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    Organization
                                </div>

                                <div class="info-value">
                                    <?= htmlspecialchars(
                                        $user['organization_name'] ?? '-'
                                    ); ?>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    User ID
                                </div>

                                <div class="info-value">
                                    #<?= htmlspecialchars($user['id']); ?>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <div class="info-label">
                                    Account Created
                                </div>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['created_at']); ?>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <?php if(!empty($permissions)): ?>

                <div class="card shadow-sm profile-card mt-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">
                            Assigned Permissions
                        </h5>

                        <?php foreach($permissions as $permission): ?>

                            <span class="permission-badge">
                                <?= htmlspecialchars(
                                    $permission['permission_name']
                                ); ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>

            <div class="card shadow-sm profile-card mt-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        Security
                    </h5>

                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="<?= BASE_URL ?>/forgot-password"
                            class="btn btn-outline-primary"
                        >
                            Change Password
                        </a>

                        <?php if(
                            $user['role'] === 'admin'
                            || $user['role'] === 'agent'
                        ): ?>

                            <a
                                href="<?= BASE_URL ?>/mfa-recovery"
                                class="btn btn-outline-warning"
                            >
                                Reset MFA
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>