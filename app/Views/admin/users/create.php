<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

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

                        <i class="bi bi-person-plus-fill"></i>

                        User Management

                    </div>

                    <h1 class="page-title">
                        Create User
                    </h1>

                    <p class="page-description">
                        Create users, agents, administrators and organization administrators.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/admin/users"
                        class="btn btn-light">

                        <i class="bi bi-arrow-left me-1"></i>

                        Back to Users

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
                    User Information
                </h2>

                <p class="ui-panel-subtitle">
                    Complete the information below to create a new account.
                </p>

            </div>

        </div>

        <div class="ui-panel-body">

            <form
                method="POST"
                action="<?= BASE_URL ?>/admin/users/create"
                class="row g-4">

                <?= Csrf::field(); ?>


                <!-- Full Name -->

                <div class="col-md-6">

                    <label class="form-label">

                        Full Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        placeholder="Enter full name"
                        required>

                </div>


                <!-- Email -->

                <div class="col-md-6">

                    <label class="form-label">

                        Email Address

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="user@example.com"
                        required>

                </div>


                <!-- Password -->

                <div class="col-md-6">

                    <label class="form-label">

                        Password

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required>

                    <div class="form-text">
                        Password must meet your configured security policy.
                    </div>

                </div>


                <!-- Role -->

                <div class="col-md-6">

                    <label class="form-label">

                        User Role

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        id="roleSelect"
                        name="role"
                        class="form-select"
                        required>

                        <option value="user">
                            User
                        </option>

                        <option value="agent">
                            Agent
                        </option>

                        <?php if (PermissionHelper::isAdmin()): ?>

                            <option value="admin">
                                Administrator
                            </option>

                        <?php endif; ?>

                    </select>

                </div>



                <!-- ORGANIZATION -->

                <div
                    id="organizationSection"
                    class="col-12">

                    <div class="row g-4">

                        <div class="col-md-8">

                            <label class="form-label">

                                Organization

                            </label>

                            <select
                                name="organization_id"
                                class="form-select">

                                <option value="">
                                    Select Organization
                                </option>

                                <?php foreach ($organizations as $organization): ?>

                                    <option value="<?= $organization['id']; ?>">

                                        <?= htmlspecialchars($organization['name']); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label d-block">

                                Organization Role

                            </label>

                            <div class="form-check mt-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_organization_admin"
                                    value="1"
                                    id="organizationAdmin">

                                <label
                                    class="form-check-label"
                                    for="organizationAdmin">

                                    Organization Administrator

                                </label>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ADMIN AGENT -->

                <div
                    id="adminAgentSection"
                    class="col-12"
                    style="display:none;">

                    <div class="ui-panel bg-light">

                        <div class="ui-panel-body">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_admin_agent"
                                    id="is_admin_agent"
                                    value="1">

                                <label
                                    class="form-check-label"
                                    for="is_admin_agent">

                                    <strong>

                                        Admin Agent

                                    </strong>

                                    <div class="text-muted small mt-1">

                                        Agent with additional administrative
                                        permissions while still using the Agent
                                        portal.

                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="col-12">

                    <hr>

                </div>



                <div class="col-12 d-flex justify-content-end gap-2">

                    <a
                        href="<?= BASE_URL ?>/admin/users"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">

                        <i class="bi bi-person-plus-fill me-2"></i>

                        Create User

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<script>

function toggleSections() {

    const role =
        document.getElementById('roleSelect').value;

    const organization =
        document.getElementById('organizationSection');

    const adminAgent =
        document.getElementById('adminAgentSection');

    if (role === 'user') {

        organization.style.display = '';

        adminAgent.style.display = 'none';

    }

    else if (role === 'agent') {

        organization.style.display = 'none';

        adminAgent.style.display = '';

    }

    else {

        organization.style.display = 'none';

        adminAgent.style.display = 'none';

    }

}

document
    .getElementById('roleSelect')
    .addEventListener('change', toggleSections);

toggleSections();

</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>