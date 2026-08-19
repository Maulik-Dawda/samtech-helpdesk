<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid py-4">

    <div class="page-header mb-4">

        <div>

            <h1 class="page-title mb-1">
                Change Password
            </h1>

            <p class="page-description mb-0">
                Update your account password to keep your account secure.
            </p>

        </div>

        <div>

            <a
                href="<?= BASE_URL ?>/profile"
                class="btn btn-light border">

                <i class="bi bi-arrow-left me-2"></i>
                Back to Profile

            </a>

        </div>

    </div>

    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['success']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= htmlspecialchars($_SESSION['error']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <div class="row justify-content-center">

        <div class="col-xl-7">

            <div class="ui-panel">

                <div class="ui-panel-header">

                    <div>

                        <h5 class="ui-panel-title">

                            <i class="bi bi-key-fill me-2"></i>

                            Password Information

                        </h5>

                        <p class="ui-panel-subtitle mb-0">

                            Enter your current password before choosing a new one.

                        </p>

                    </div>

                </div>

                <div class="ui-panel-body">

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/profile/change-password"
                        onsubmit="showSamtechLoader('Updating password...')">

                        <?= Csrf::field(); ?>

                        <!-- Current Password -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Current Password

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-lock-fill"></i>

                                </span>

                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control"
                                    placeholder="Enter current password"
                                    required>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('current_password',this)">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                        <!-- New Password -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                New Password

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-shield-lock-fill"></i>

                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    minlength="8"
                                    placeholder="Enter new password"
                                    required>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('password',this)">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                        <!-- Confirm Password -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Confirm Password

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-check-circle-fill"></i>

                                </span>

                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    class="form-control"
                                    minlength="8"
                                    placeholder="Confirm new password"
                                    required>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('confirm_password',this)">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                        <div class="alert alert-info">

                            <i class="bi bi-info-circle-fill me-2"></i>

                            <strong>Password Requirements</strong>

                            <ul class="mb-0 mt-2">

                                <li>Minimum 8 characters</li>

                                <li>One uppercase letter</li>

                                <li>One lowercase letter</li>

                                <li>One number</li>

                                <li>One special character</li>

                            </ul>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">

                            <a
                                href="<?= BASE_URL ?>/profile"
                                class="btn btn-light border">

                                Cancel

                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-check-circle-fill me-2"></i>

                                Update Password

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function togglePassword(id,button)
{
    const input=document.getElementById(id);

    const icon=button.querySelector("i");

    if(input.type==="password")
    {
        input.type="text";

        icon.classList.remove("bi-eye");

        icon.classList.add("bi-eye-slash");
    }
    else
    {
        input.type="password";

        icon.classList.remove("bi-eye-slash");

        icon.classList.add("bi-eye");
    }
}

</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>