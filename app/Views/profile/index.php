<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<?php

$nameParts = preg_split(
    '/\s+/',
    trim($user['full_name'] ?? ''),
    -1,
    PREG_SPLIT_NO_EMPTY
);

$initials = '';

if (!empty($nameParts[0])) {
    $initials .= strtoupper(substr($nameParts[0], 0, 1));
}

if (!empty($nameParts[1])) {
    $initials .= strtoupper(substr($nameParts[1], 0, 1));
}

if ($initials === '') {
    $initials = 'U';
}

$fullName = $user['full_name'] ?? 'Unknown User';
$email = $user['email'] ?? '-';
$role = ucfirst(str_replace('_', ' ', $user['role'] ?? 'user'));
$organization = $user['organization_name'] ?? 'Not assigned';
$userId = $user['id'] ?? '-';

$createdAt = !empty($user['created_at'])
    ? date('d M Y, h:i A', strtotime($user['created_at']))
    : '-';

$mfaEnabled = !empty($user['mfa_secret']);

?>

<div class="container-fluid py-4">

    <!-- Page header -->
    <div class="page-header mb-4">

        <div>
            <h1 class="page-title mb-1">
                My Profile
            </h1>

            <p class="page-description mb-0">
                Manage your account information, permissions and security settings.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="<?= BASE_URL ?>/dashboard"
                class="btn btn-light border"
            >
                <i class="bi bi-arrow-left me-2"></i>
                Back to Dashboard
            </a>

        </div>

    </div>

    <?php if (!empty($_SESSION['success'])): ?>

    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <?= htmlspecialchars($_SESSION['success']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>

    <div class="row g-4">

        <!-- Profile overview -->
        <div class="col-xl-4">

            <div class="ui-panel profile-overview-panel h-100">

                <div class="ui-panel-body text-center">

                    <div class="profile-avatar mx-auto mb-3">
                        <?= htmlspecialchars($initials); ?>
                    </div>

                    <h3 class="profile-name mb-1">
                        <?= htmlspecialchars($fullName); ?>
                    </h3>

                    <p class="profile-email mb-3">
                        <?= htmlspecialchars($email); ?>
                    </p>

                    <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">

                        <span class="app-badge app-badge-success">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Active
                        </span>

                        <span class="app-badge app-badge-neutral">
                            <i class="bi bi-person-badge me-1"></i>
                            <?= htmlspecialchars($role); ?>
                        </span>

                    </div>

                    <div class="profile-summary-list">

                        <div class="profile-summary-item">

                            <div class="profile-summary-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <div class="text-start">

                                <span class="profile-summary-label">
                                    Organization
                                </span>

                                <strong class="profile-summary-value">
                                    <?= htmlspecialchars($organization); ?>
                                </strong>

                            </div>

                        </div>

                        <div class="profile-summary-item">

                            <div class="profile-summary-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>

                            <div class="text-start">

                                <span class="profile-summary-label">
                                    Member Since
                                </span>

                                <strong class="profile-summary-value">
                                    <?= htmlspecialchars($createdAt); ?>
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-8">

            <!-- Profile information -->
            <div class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div>

                        <h5 class="ui-panel-title">
                            <i class="bi bi-person-vcard me-2"></i>
                            Profile Information
                        </h5>

                        <p class="ui-panel-subtitle mb-0">
                            Basic information associated with your account.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="profile-info-card">

                                <div class="profile-info-icon">
                                    <i class="bi bi-person"></i>
                                </div>

                                <div>

                                    <span class="profile-info-label">
                                        Full Name
                                    </span>

                                    <div class="profile-info-value">
                                        <?= htmlspecialchars($fullName); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="profile-info-card">

                                <div class="profile-info-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>

                                <div class="profile-info-content">

                                    <span class="profile-info-label">
                                        Email Address
                                    </span>

                                    <div class="profile-info-value text-break">
                                        <?= htmlspecialchars($email); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="profile-info-card">

                                <div class="profile-info-icon">
                                    <i class="bi bi-person-gear"></i>
                                </div>

                                <div>

                                    <span class="profile-info-label">
                                        Account Role
                                    </span>

                                    <div class="profile-info-value">
                                        <?= htmlspecialchars($role); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="profile-info-card">

                                <div class="profile-info-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                                <div>

                                    <span class="profile-info-label">
                                        Organization
                                    </span>

                                    <div class="profile-info-value">
                                        <?= htmlspecialchars($organization); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="profile-info-card">

                                <div class="profile-info-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>

                                <div>

                                    <span class="profile-info-label">
                                        Account Created
                                    </span>

                                    <div class="profile-info-value">
                                        <?= htmlspecialchars($createdAt); ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Security center -->
            <div class="ui-panel mb-4">

                <div class="ui-panel-header">

                    <div>

                        <h5 class="ui-panel-title">
                            <i class="bi bi-shield-lock me-2"></i>
                            Security Center
                        </h5>

                        <p class="ui-panel-subtitle mb-0">
                            Manage your password and account authentication.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <div class="row g-3">

                        <div class="col-lg-6">

                            <div class="security-setting-card">

                                <div class="security-setting-header">

                                    <div class="security-setting-icon">
                                        <i class="bi bi-key"></i>
                                    </div>

                                    <span class="app-badge app-badge-success">
                                        Protected
                                    </span>

                                </div>

                                <h6 class="security-setting-title">
                                    Account Password
                                </h6>

                                <p class="security-setting-description">
                                    Update your password regularly to keep your account secure.
                                </p>

                                <a
                                    href="<?= BASE_URL ?>/profile/change-password"
                                    class="btn btn-primary w-100"
                                >
                                    <i class="bi bi-key-fill me-2"></i>
                                    Change Password
                                </a>

                            </div>

                        </div>

                        <?php if (
                            in_array(
                                $user['role'] ?? '',
                                ['admin', 'admin_agent', 'agent'],
                                true
                            )
                        ): ?>

                            <div class="col-lg-6">

                                <div class="security-setting-card">

                                    <div class="security-setting-header">

                                        <div class="security-setting-icon">
                                            <i class="bi bi-shield-check"></i>
                                        </div>

                                        <?php if ($mfaEnabled): ?>

                                            <span class="app-badge app-badge-success">
                                                Enabled
                                            </span>

                                        <?php else: ?>

                                            <span class="app-badge app-badge-warning">
                                                Setup Required
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <h6 class="security-setting-title">
                                        Authenticator MFA
                                    </h6>

                                    <p class="security-setting-description">
                                        Reset and configure your authenticator on a new device.
                                    </p>

                                    <form method="POST" action="<?= BASE_URL ?>/profile/reset-authenticator" onsubmit="return confirm('Are you sure you want to reset your Authenticator (2FA)?');">
                                        <?= Csrf::field(); ?>
                                        <button
                                            type="submit"
                                            class="btn btn-outline-warning w-100"
                                        >
                                            <i class="bi bi-arrow-repeat me-2"></i>
                                            Reset Authenticator
                                        </button>
                                    </form>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <!-- Recent activity -->
            <div class="ui-panel">

                <div class="ui-panel-header">

                    <div>

                        <h5 class="ui-panel-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Recent Activity
                        </h5>

                        <p class="ui-panel-subtitle mb-0">
                            Your latest account and helpdesk activity.
                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <?php if (!empty($activities)): ?>

                        <div class="activity-timeline">

                            <?php foreach ($activities as $activity): ?>

                                <?php
                                $activityDate = !empty($activity['created_at'])
                                    ? date(
                                        'd M Y, h:i A',
                                        strtotime($activity['created_at'])
                                    )
                                    : '-';

                                $activityText =
                                    $activity['description']
                                    ?? $activity['action']
                                    ?? 'Account activity';
                                ?>

                                <div class="activity-timeline-item">

                                    <div class="activity-timeline-marker">
                                        <i class="bi bi-check2"></i>
                                    </div>

                                    <div class="activity-timeline-content">

                                        <div class="activity-timeline-title">
                                            <?= htmlspecialchars($activityText); ?>
                                        </div>

                                        <div class="activity-timeline-date">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= htmlspecialchars($activityDate); ?>
                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="empty-state compact-empty-state">

                            <div class="empty-state-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <h6>No recent activity</h6>

                            <p class="mb-0">
                                Your recent account actions will appear here.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>