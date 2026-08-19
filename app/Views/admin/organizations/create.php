<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
                        Create Organization
                    </h1>

                    <p class="page-description">
                        Register a new customer organization that can have users,
                        tickets and organization administrators.
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
         FORM
    ========================================================== -->

    <section class="ui-panel">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">
                    Organization Information
                </h2>

                <p class="ui-panel-subtitle">
                    Fill in the required information below.
                </p>

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
                action="<?= BASE_URL ?>/admin/organizations/create"
                class="row g-4">

                <?= Csrf::field(); ?>

                <div class="col-md-6">

                    <label class="form-label">
                        Organization Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter organization name"
                        required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="organization@example.com">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        placeholder="+971 XXXXXXXX">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Maximum Users
                    </label>

                    <input
                        type="number"
                        name="max_users"
                        class="form-control"
                        value="3"
                        min="1"
                        required>

                </div>

                <div class="col-12">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="4"
                        placeholder="Enter organization address"></textarea>

                </div>

                <div class="col-12">

                    <hr>

                </div>

                <div class="col-12 d-flex justify-content-end gap-2">

                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">

                        <i class="bi bi-building-add me-2"></i>

                        Create Organization

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>