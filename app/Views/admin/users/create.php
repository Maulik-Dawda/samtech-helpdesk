<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold">
                        Create User
                    </h4>

                </div>

                <div class="card-body p-4">

                    <form method="POST" action="<?= BASE_URL ?>/admin/users/create">

                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">

                            <label>Role</label>

                            <select
                                name="role"
                                class="form-select"
                                id="roleSelect"
                                required>
                                <option value="user">User</option>
                                <option value="agent">Agent</option>
                                <?php if (PermissionHelper::isAdmin()): ?>
                                    <option value="admin">Admin</option>
                                <?php endif; ?>
                            </select>

                        </div>

                        <div id="organizationSection">

                            <div class="mb-3">

                                <label>Organization</label>

                                <select
                                    name="organization_id"
                                    class="form-select">

                                    <option value="">
                                        Select Organization
                                    </option>

                                    <?php foreach ($organizations as $organization): ?>

                                        <option
                                            value="<?= $organization['id']; ?>">
                                            <?= htmlspecialchars(
                                                $organization['name']
                                            ); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="form-check mb-4">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="is_organization_admin"
                                    value="1">

                                <label class="form-check-label">
                                    Organization Admin
                                </label>

                            </div>

                        </div>

                        <div id="adminAgentSection" style="display:none;">

                            <div class="form-check mb-4">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_admin_agent"
                                    id="is_admin_agent"
                                    value="1">

                                <label class="form-check-label" for="is_admin_agent">

                                    Admin Agent

                                    <small class="text-muted d-block">
                                        Agent with extended administration privileges.
                                    </small>

                                </label>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary-custom">
                            Create User
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    function toggleSections() {
        const role = document.getElementById('roleSelect').value;

        const organization =
            document.getElementById('organizationSection');

        const adminAgent =
            document.getElementById('adminAgentSection');

        if (role === "user") {
            organization.style.display = "block";
            adminAgent.style.display = "none";
        } else if (role === "agent") {
            organization.style.display = "none";
            adminAgent.style.display = "block";
        } else {
            organization.style.display = "none";
            adminAgent.style.display = "none";
        }
    }

    document
        .getElementById("roleSelect")
        .addEventListener("change", toggleSections);

    toggleSections();
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>