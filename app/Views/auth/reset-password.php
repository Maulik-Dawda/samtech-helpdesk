
<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
?>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-key security-icon icon-2"></i>
    <i class="bi bi-lock security-icon icon-3"></i>
    <i class="bi bi-shield-check security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width:460px;">

            <div class="text-center mb-4">

                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-3">

                <p class="login-subtitle mb-0">
                    Reset Password
                </p>

            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>Password update failed</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-custom alert-success-custom mb-3">
                    <i class="bi bi-check-circle-fill"></i>

                    <div>
                        <strong>Success</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['success']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/reset-password"
                onsubmit="showSamtechLoader('Updating password...')">

                <?= Csrf::field(); ?>

                <div class="mb-3">

                    <label class="form-label">
                        New Password
                    </label>

                    <div class="input-wrap">

                        <i class="bi bi-lock-fill input-icon"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            minlength="8"
                            placeholder="Enter new password"
                            required>

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <small class="text-muted d-block mt-2">
                        Minimum 8 characters required.
                    </small>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Confirm Password
                    </label>

                    <div class="input-wrap">

                        <i class="bi bi-shield-lock-fill input-icon"></i>

                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            class="form-control"
                            minlength="8"
                            placeholder="Confirm new password"
                            required>

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('confirm_password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-login w-100">

                    Update Password

                </button>

            </form>

            <div class="text-center mt-4">

                <a
                    href="<?= BASE_URL ?>/user-login"
                    class="text-decoration-none back-link">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Login

                </a>

            </div>

        </div>

        <div class="footer-text mt-3">
            <i class="bi bi-shield-check text-success me-1"></i>
            © <?= date('Y'); ?> Samtech Solutions. All rights reserved.
        </div>

    </div>

</div>

<style>

.password-toggle{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    border:none;
    background:none;
    color:#64748b;
    cursor:pointer;
}

.form-control{
    padding-right:45px !important;
}

</style>

<script>
function togglePassword(id, btn)
{
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>