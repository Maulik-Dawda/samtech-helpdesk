<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = is_array($user ?? null)
    ? $user
    : [];

$organizations = is_array($organizations ?? null)
    ? $organizations
    : [];

$userId = (int) ($user['id'] ?? 0);
$userName = $user['full_name'] ?? 'User';
$userRole = $user['role'] ?? 'user';
$isActive = !empty($user['is_active']);
$isOrganizationAdmin = !empty($user['is_organization_admin']);
$isAdminAgent = !empty($user['is_admin_agent']);

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

                        <i class="bi bi-people-fill"></i>

                        User Management

                    </div>

                    <h1 class="page-title">
                        Edit User
                    </h1>

                    <p class="page-description">
                        Update account information, role, organization access
                        and permissions for
                        <strong><?= htmlspecialchars($userName); ?></strong>.
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
         EDIT USER FORM
    ========================================================== -->
    <section class="ui-panel">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">
                    User Information
                </h2>

                <p class="ui-panel-subtitle">
                    Review and update the user details below.
                </p>

            </div>

            <div class="ui-panel-actions">

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
                action="<?= BASE_URL ?>/admin/users/update/<?= $userId; ?>"
                class="row g-4">

                <?= Csrf::field(); ?>


                <!-- Full Name -->
                <div class="col-md-6">

                    <label
                        for="full-name"
                        class="form-label">

                        Full Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        id="full-name"
                        name="full_name"
                        class="form-control"
                        value="<?= htmlspecialchars($user['full_name'] ?? ''); ?>"
                        placeholder="Enter full name"
                        maxlength="150"
                        required>

                </div>


                <!-- Email Address -->
                <div class="col-md-6">

                    <label
                        for="email-address"
                        class="form-label">

                        Email Address

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="email"
                        id="email-address"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($user['email'] ?? ''); ?>"
                        placeholder="user@example.com"
                        maxlength="190"
                        required>

                </div>


                <!-- Role -->
                <div class="col-md-6">

                    <label
                        for="roleSelect"
                        class="form-label">

                        User Role

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        id="roleSelect"
                        name="role"
                        class="form-select"
                        required>

                        <option
                            value="user"
                            <?= $userRole === 'user' ? 'selected' : ''; ?>>

                            User

                        </option>

                        <option
                            value="agent"
                            <?= $userRole === 'agent' ? 'selected' : ''; ?>>

                            Agent

                        </option>

                        <?php if (PermissionHelper::isAdmin()): ?>

                            <option
                                value="admin"
                                <?= $userRole === 'admin' ? 'selected' : ''; ?>>

                                Administrator

                            </option>

                        <?php endif; ?>

                    </select>

                    <div class="form-text">
                        Changing the role may change the user's available
                        dashboard and permissions.
                    </div>

                </div>


                <!-- Account Status -->
                <div class="col-md-6">

                    <label class="form-label d-block">
                        Account Status
                    </label>

                    <div class="form-check mt-2">

                        <input
                            type="checkbox"
                            id="active-user"
                            class="form-check-input"
                            name="is_active"
                            value="1"
                            <?= $isActive ? 'checked' : ''; ?>>

                        <label
                            for="active-user"
                            class="form-check-label fw-semibold">

                            Active User

                        </label>

                    </div>

                    <div class="form-text">
                        Inactive users may be prevented from signing in.
                    </div>

                </div>


                <!-- =====================================================
                     ORGANIZATION SECTION
                ====================================================== -->
                <div
                    id="organizationSection"
                    class="col-12">

                    <section class="ui-panel">

                        <div class="ui-panel-header">

                            <div class="ui-panel-title-wrap">

                                <h3 class="ui-panel-title">
                                    Organization Access
                                </h3>

                                <p class="ui-panel-subtitle">
                                    Assign the user to an organization and
                                    optionally grant organization administrator access.
                                </p>

                            </div>

                        </div>

                        <div class="ui-panel-body">

                            <div class="row g-4">

                                <div class="col-md-8">

                                    <label
                                        for="organization-id"
                                        class="form-label">

                                        Organization

                                    </label>

                                    <select
                                        id="organization-id"
                                        name="organization_id"
                                        class="form-select">

                                        <option value="">
                                            Select Organization
                                        </option>

                                        <?php foreach ($organizations as $organization): ?>

                                            <?php
                                            $organizationId = (int) (
                                                $organization['id'] ?? 0
                                            );

                                            $selectedOrganizationId = (int) (
                                                $user['organization_id'] ?? 0
                                            );
                                            ?>

                                            <option
                                                value="<?= $organizationId; ?>"
                                                <?= $organizationId === $selectedOrganizationId
                                                    ? 'selected'
                                                    : ''; ?>>

                                                <?= htmlspecialchars(
                                                    $organization['name'] ?? 'Unnamed Organization'
                                                ); ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-md-4">

                                    <label class="form-label d-block">
                                        Organization Permission
                                    </label>

                                    <div class="form-check mt-2">

                                        <input
                                            type="checkbox"
                                            id="organization-admin"
                                            class="form-check-input"
                                            name="is_organization_admin"
                                            value="1"
                                            <?= $isOrganizationAdmin
                                                ? 'checked'
                                                : ''; ?>>

                                        <label
                                            for="organization-admin"
                                            class="form-check-label fw-semibold">

                                            Organization Administrator

                                        </label>

                                    </div>

                                    <div class="form-text">
                                        Allows the user to manage permitted
                                        users within their organization.
                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>

                </div>


                <!-- =====================================================
                     ADMIN AGENT SECTION
                ====================================================== -->
                <div
                    id="adminAgentSection"
                    class="col-12">

                    <section class="ui-panel">

                        <div class="ui-panel-header">

                            <div class="ui-panel-title-wrap">

                                <h3 class="ui-panel-title">
                                    Agent Permissions
                                </h3>

                                <p class="ui-panel-subtitle">
                                    Configure extended permissions for this agent.
                                </p>

                            </div>

                        </div>

                        <div class="ui-panel-body">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_admin_agent"
                                    id="is_admin_agent"
                                    value="1"
                                    <?= $isAdminAgent ? 'checked' : ''; ?>>

                                <label
                                    class="form-check-label"
                                    for="is_admin_agent">

                                    <strong>
                                        Admin Agent
                                    </strong>

                                    <span class="text-muted small d-block mt-1">
                                        Grants additional administration
                                        permissions while the account continues
                                        to use the Agent login and Agent portal.
                                    </span>

                                </label>

                            </div>

                        </div>

                    </section>

                </div>


                <!-- Form Actions -->
                <div class="col-12">

                    <hr>

                </div>

                <div class="col-12 d-flex flex-wrap justify-content-end gap-2">

                    <a
                        href="<?= BASE_URL ?>/admin/users"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        Update User

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const roleSelect =
        document.getElementById('roleSelect');

    const organizationSection =
        document.getElementById('organizationSection');

    const adminAgentSection =
        document.getElementById('adminAgentSection');

    const organizationSelect =
        document.getElementById('organization-id');

    const organizationAdminCheckbox =
        document.getElementById('organization-admin');

    const adminAgentCheckbox =
        document.getElementById('is_admin_agent');


    function toggleRoleSections() {

        if (!roleSelect) {
            return;
        }

        const role = roleSelect.value;

        if (role === 'user') {

            organizationSection?.classList.remove('d-none');
            adminAgentSection?.classList.add('d-none');

            if (adminAgentCheckbox) {
                adminAgentCheckbox.checked = false;
            }

        } else if (role === 'agent') {

            organizationSection?.classList.add('d-none');
            adminAgentSection?.classList.remove('d-none');

            if (organizationSelect) {
                organizationSelect.value = '';
            }

            if (organizationAdminCheckbox) {
                organizationAdminCheckbox.checked = false;
            }

        } else {

            organizationSection?.classList.add('d-none');
            adminAgentSection?.classList.add('d-none');

            if (organizationSelect) {
                organizationSelect.value = '';
            }

            if (organizationAdminCheckbox) {
                organizationAdminCheckbox.checked = false;
            }

            if (adminAgentCheckbox) {
                adminAgentCheckbox.checked = false;
            }

        }

    }

    roleSelect?.addEventListener(
        'change',
        toggleRoleSections
    );

    toggleRoleSections();

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>