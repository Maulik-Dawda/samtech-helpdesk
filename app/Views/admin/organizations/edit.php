<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$organization = is_array($organization ?? null)
    ? $organization
    : [];

$organizationId = (int) ($organization['id'] ?? 0);
$organizationName = $organization['name'] ?? 'Organization';

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
                        Edit Organization
                    </h1>

                    <p class="page-description">
                        Update the organization information, user limit and
                        account status for
                        <strong><?= htmlspecialchars($organizationName); ?></strong>.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">

                        <i class="bi bi-arrow-left me-1"></i>

                        Back to Organizations

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         EDIT ORGANIZATION FORM
    ========================================================== -->
    <section class="ui-panel">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">
                    Organization Information
                </h2>

                <p class="ui-panel-subtitle">
                    Review and update the organization details below.
                </p>

            </div>

            <div class="ui-panel-actions">

                <?php if (!empty($organization['is_active'])): ?>

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

            </div>

        </div>

        <div class="ui-panel-body">

            <?php if (isset($_SESSION['error'])): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($_SESSION['error']); ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>


            <form
                method="POST"
                action="<?= BASE_URL ?>/admin/organizations/update/<?= $organizationId; ?>"
                class="row g-4">

                <?= Csrf::field(); ?>


                <!-- Organization Name -->
                <div class="col-md-6">

                    <label
                        for="organization-name"
                        class="form-label">

                        Organization Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        id="organization-name"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['name'] ?? ''); ?>"
                        placeholder="Enter organization name"
                        maxlength="150"
                        required>

                </div>


                <!-- Email -->
                <div class="col-md-6">

                    <label
                        for="organization-email"
                        class="form-label">

                        Email Address

                    </label>

                    <input
                        type="email"
                        id="organization-email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['email'] ?? ''); ?>"
                        placeholder="organization@example.com"
                        maxlength="190">

                </div>


                <!-- Phone -->
                <div class="col-md-6">

                    <label
                        for="organization-phone"
                        class="form-label">

                        Phone Number

                    </label>

                    <input
                        type="text"
                        id="organization-phone"
                        name="phone"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['phone'] ?? ''); ?>"
                        placeholder="+971 XXXXXXXX"
                        maxlength="30">

                </div>


                <!-- Maximum Users -->
                <div class="col-md-6">

                    <label
                        for="organization-max-users"
                        class="form-label">

                        Maximum Users

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="number"
                        id="organization-max-users"
                        name="max_users"
                        class="form-control"
                        value="<?= (int) ($organization['max_users'] ?? 3); ?>"
                        min="1"
                        step="1"
                        required>

                    <div class="form-text">
                        Sets the maximum number of users allowed for this organization.
                    </div>

                </div>


                <!-- Address -->
                <div class="col-12">

                    <label
                        for="organization-address"
                        class="form-label">

                        Address

                    </label>

                    <textarea
                        id="organization-address"
                        name="address"
                        class="form-control"
                        rows="4"
                        placeholder="Enter organization address"
                        maxlength="500"><?= htmlspecialchars($organization['address'] ?? ''); ?></textarea>

                </div>


                <!-- Organization Status -->
                <div class="col-12">

                    <div class="form-check">

                        <input
                            type="checkbox"
                            id="organization-status"
                            class="form-check-input"
                            name="is_active"
                            value="1"
                            <?= !empty($organization['is_active']) ? 'checked' : ''; ?>>

                        <label
                            for="organization-status"
                            class="form-check-label fw-semibold">

                            Active Organization

                        </label>

                    </div>

                    <div class="form-text">
                        Inactive organizations may be restricted from accessing
                        helpdesk services.
                    </div>

                </div>


                <!-- Actions -->
                <div class="col-12">

                    <hr>

                </div>

                <div class="col-12 d-flex flex-wrap justify-content-end gap-2">

                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">

                        <i class="bi bi-check-circle me-2"></i>

                        Update Organization

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>