<?php require_once "../app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold mb-1">
                        Edit User
                    </h4>

                    <div class="text-muted small">
                        Update user details and permissions.
                    </div>

                </div>

                <div class="card-body p-4">

                    <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/admin/users/update/<?= $user['id']; ?>"
                    >

                        <?= Csrf::field(); ?>

                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                value="<?= htmlspecialchars($user['full_name']); ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($user['email']); ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Role
                            </label>

                            <select
                                name="role"
                                class="form-select"
                                id="roleSelect"
                                required
                            >

                                <option
                                    value="user"
                                    <?= $user['role'] === 'user' ? 'selected' : ''; ?>
                                >
                                    User
                                </option>

                                <option
                                    value="agent"
                                    <?= $user['role'] === 'agent' ? 'selected' : ''; ?>
                                >
                                    Agent
                                </option>

                            </select>

                        </div>

                        <div id="organizationSection">

                            <div class="mb-3">

                                <label class="form-label">
                                    Organization
                                </label>

                                <select
                                    name="organization_id"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Organization
                                    </option>

                                    <?php foreach($organizations as $organization): ?>

                                        <option
                                            value="<?= $organization['id']; ?>"
                                            <?= $organization['id'] == $user['organization_id']
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            <?= htmlspecialchars(
                                                $organization['name']
                                            ); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="form-check mb-3">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="is_organization_admin"
                                    value="1"
                                    <?= (int)$user['is_organization_admin'] === 1
                                        ? 'checked'
                                        : ''; ?>
                                >

                                <label class="form-check-label">
                                    Organization Admin
                                </label>

                            </div>

                        </div>

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                value="1"
                                <?= (int)$user['is_active'] === 1
                                    ? 'checked'
                                    : ''; ?>
                            >

                            <label class="form-check-label">
                                Active User
                            </label>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="<?= BASE_URL ?>/admin/users"
                                class="btn btn-outline-secondary"
                            >
                                Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom"
                            >
                                Update User
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function toggleOrganizationSection()
{
    let roleSelect =
        document.getElementById('roleSelect');

    let organizationSection =
        document.getElementById('organizationSection');

    if(roleSelect.value === 'user')
    {
        organizationSection.style.display = 'block';
    }
    else
    {
        organizationSection.style.display = 'none';
    }
}

document
    .getElementById('roleSelect')
    .addEventListener('change', toggleOrganizationSection);

toggleOrganizationSection();

</script>

<?php require_once "../app/Views/layouts/footer.php"; ?>