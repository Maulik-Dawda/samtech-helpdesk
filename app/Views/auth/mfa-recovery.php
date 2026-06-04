<?php require_once "../app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100" style="max-width: 460px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold">Recover Authenticator</h3>
            <p class="text-muted mb-0">Verify your email and password to reset MFA.</p>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/mfa-recovery">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Account Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Reset Authenticator
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/mfa-verify" class="text-decoration-none small">
                Back to verification
            </a>
        </div>

    </div>
</div>

<?php require_once "../app/Views/layouts/auth-footer.php"; ?>