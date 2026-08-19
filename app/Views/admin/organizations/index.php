<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$organizations = is_array($organizations ?? null)
    ? $organizations
    : [];

$totalOrganizations = count($organizations);

$activeOrganizations = count(array_filter(
    $organizations,
    static fn(array $organization): bool =>
        !empty($organization['is_active'])
));

$inactiveOrganizations = $totalOrganizations - $activeOrganizations;

$totalUserCapacity = array_sum(array_map(
    static fn(array $organization): int =>
        (int) ($organization['max_users'] ?? 0),
    $organizations
));

?>

<div class="container-fluid px-0">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->
    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-buildings"></i>
                        Organization Management
                    </div>

                    <h1 class="page-title">
                        Organizations
                    </h1>

                    <p class="page-description">
                        Create, review and manage customer organizations,
                        account status and user limits.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/admin/organizations/create"
                        class="btn btn-primary-custom">

                        <i class="bi bi-building-add me-2"></i>
                        Create Organization

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         ORGANIZATION METRICS
    ========================================================== -->
    <section class="content-section">

        <div class="metric-grid">

            <div class="metric-card">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-buildings-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Total Organizations
                </div>

                <div class="metric-card-value">
                    <?= $totalOrganizations; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-collection"></i>
                    All registered organizations
                </div>

            </div>


            <div class="metric-card metric-card-success">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Active Organizations
                </div>

                <div class="metric-card-value">
                    <?= $activeOrganizations; ?>
                </div>

                <div class="metric-card-meta positive">
                    <i class="bi bi-shield-check"></i>
                    Currently active
                </div>

            </div>


            <div class="metric-card metric-card-danger">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Inactive Organizations
                </div>

                <div class="metric-card-value">
                    <?= $inactiveOrganizations; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-shield-x"></i>
                    Currently inactive
                </div>

            </div>


            <div class="metric-card metric-card-info">

                <div class="metric-card-header">

                    <div class="metric-card-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                </div>

                <div class="metric-card-label">
                    Total User Capacity
                </div>

                <div class="metric-card-value">
                    <?= $totalUserCapacity; ?>
                </div>

                <div class="metric-card-meta">
                    <i class="bi bi-person-check"></i>
                    Combined maximum users
                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         SUCCESS MESSAGE
    ========================================================== -->
    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success mb-4">

            <i class="bi bi-check-circle me-2"></i>

            <?= htmlspecialchars($_SESSION['success']); ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>


    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger mb-4">

            <i class="bi bi-exclamation-circle me-2"></i>

            <?= htmlspecialchars($_SESSION['error']); ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <!-- =========================================================
         ORGANIZATION TABLE
    ========================================================== -->
    <section class="table-card content-section">

        <div class="table-card-header">

            <div>

                <div class="table-card-title">
                    Organization Directory
                </div>

                <div class="table-card-subtitle">
                    View and update registered customer organizations.
                </div>

            </div>

            <div class="app-badge app-badge-primary">

                <i class="bi bi-building"></i>

                <?= $totalOrganizations; ?>
                <?= $totalOrganizations === 1 ? 'Organization' : 'Organizations'; ?>

            </div>

        </div>

        <div class="table-card-body">

            <?php if (empty($organizations)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <h3 class="empty-state-title">
                        No organizations found
                    </h3>

                    <p class="empty-state-description">
                        Create your first organization to start adding users
                        and managing support tickets.
                    </p>

                    <div class="empty-state-action">

                        <a
                            href="<?= BASE_URL ?>/admin/organizations/create"
                            class="btn btn-primary-custom">

                            <i class="bi bi-building-add me-2"></i>
                            Create Organization

                        </a>

                    </div>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Organization</th>
                                <th>Contact Information</th>
                                <th>Maximum Users</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($organizations as $organization): ?>

                                <?php
                                $organizationId = (int) (
                                    $organization['id'] ?? 0
                                );

                                $organizationName = trim(
                                    (string) ($organization['name'] ?? '')
                                );

                                $organizationEmail = trim(
                                    (string) ($organization['email'] ?? '')
                                );

                                $organizationPhone = trim(
                                    (string) ($organization['phone'] ?? '')
                                );

                                $maximumUsers = (int) (
                                    $organization['max_users'] ?? 0
                                );

                                $isActive = !empty(
                                    $organization['is_active']
                                );

                                $initial = $organizationName !== ''
                                    ? strtoupper(
                                        mb_substr($organizationName, 0, 1)
                                    )
                                    : 'O';
                                ?>

                                <tr>

                                    <td data-label="Organization">

                                        <div class="d-flex align-items-center gap-3">

                                            <div class="table-avatar">
                                                <?= htmlspecialchars($initial); ?>
                                            </div>

                                            <div>

                                                <div class="fw-bold">
                                                    <?= htmlspecialchars(
                                                        $organizationName !== ''
                                                            ? $organizationName
                                                            : 'Unnamed Organization'
                                                    ); ?>
                                                </div>

                                                <div class="text-muted small mt-1">
                                                    Organization ID:
                                                    <?= $organizationId; ?>
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <td data-label="Contact Information">

                                        <?php if (
                                            $organizationEmail !== ''
                                            || $organizationPhone !== ''
                                        ): ?>

                                            <div>

                                                <?php if (
                                                    $organizationEmail !== ''
                                                ): ?>

                                                    <div class="d-flex align-items-center gap-2">

                                                        <i class="bi bi-envelope text-muted"></i>

                                                        <a
                                                            href="mailto:<?= htmlspecialchars($organizationEmail); ?>"
                                                            class="text-decoration-none">

                                                            <?= htmlspecialchars($organizationEmail); ?>

                                                        </a>

                                                    </div>

                                                <?php endif; ?>

                                                <?php if (
                                                    $organizationPhone !== ''
                                                ): ?>

                                                    <div class="d-flex align-items-center gap-2 mt-1">

                                                        <i class="bi bi-telephone text-muted"></i>

                                                        <span>
                                                            <?= htmlspecialchars($organizationPhone); ?>
                                                        </span>

                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                No contact details
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td data-label="Maximum Users">

                                        <div class="d-flex align-items-center gap-2">

                                            <i class="bi bi-people text-muted"></i>

                                            <span class="fw-semibold">
                                                <?= $maximumUsers; ?>
                                            </span>

                                            <span class="text-muted small">
                                                <?= $maximumUsers === 1
                                                    ? 'user'
                                                    : 'users'; ?>
                                            </span>

                                        </div>

                                    </td>


                                    <td data-label="Status">

                                        <?php if ($isActive): ?>

                                            <span class="status-badge status-resolved">

                                                <i class="bi bi-check-circle me-1"></i>

                                                Active

                                            </span>

                                        <?php else: ?>

                                            <span class="status-badge status-closed">

                                                <i class="bi bi-x-circle me-1"></i>

                                                Inactive

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td
                                        data-label="Action"
                                        class="text-end">

                                        <a
                                            href="<?= BASE_URL ?>/admin/organizations/edit/<?= $organizationId; ?>"
                                            class="table-action-btn table-action-edit ms-auto"
                                            title="Edit organization"
                                            aria-label="Edit organization">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="pagination-wrapper">

                    <div class="pagination-info">

                        Showing
                        <?= $totalOrganizations; ?>
                        <?= $totalOrganizations === 1
                            ? 'organization'
                            : 'organizations'; ?>

                    </div>

                    <a
                        href="<?= BASE_URL ?>/admin/organizations/create"
                        class="view-all-link">

                        Create new organization

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>