<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100" style="max-width: 430px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold">Authenticator Verification</h3>
            <p class="text-muted mb-0">Enter the 6-digit code from your authenticator app.</p>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/mfa-verify" onsubmit="showSamtechLoader('Verifying authenticator code...')">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Authenticator Code</label>
                <input type="text" name="code" class="form-control" maxlength="6" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Verify Code
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/mfa-recovery" class="text-decoration-none">
                Lost access to Authenticator?
            </a>
        </div>

    </div>
</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>