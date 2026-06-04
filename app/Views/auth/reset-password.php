<?php require_once "../app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100" style="max-width: 500px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold">Reset Password</h3>
            <p class="text-muted mb-0">
                Create a new secure password for your account.
            </p>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/reset-password">

            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">New Password</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    minlength="8"
                    required
                >

                <small class="text-muted">
                    Minimum 8 characters.
                </small>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    minlength="8"
                    required
                >
            </div>

            <button
                type="submit"
                class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Update Password
            </button>

        </form>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/user-login" class="text-decoration-none">
                Back to Login
            </a>
        </div>

    </div>
</div>

<?php require_once "../app/Views/layouts/auth-footer.php"; ?>