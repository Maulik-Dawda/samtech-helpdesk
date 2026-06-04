<?php require_once "../app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100" style="max-width: 460px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold">Verify OTP</h3>
            <p class="text-muted mb-0">
                Enter the verification OTP to continue resetting your password.
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

        <form method="POST" action="<?= BASE_URL ?>/forgot-password-verify">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Verification OTP</label>

                <input
                    type="text"
                    name="otp"
                    class="form-control text-center"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    placeholder="123456"
                    required
                    autofocus
                >
            </div>

            <button
                type="submit"
                class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Verify OTP
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/forgot-password" class="text-decoration-none">
                Back
            </a>
        </div>

    </div>
</div>

<?php require_once "../app/Views/layouts/auth-footer.php"; ?>