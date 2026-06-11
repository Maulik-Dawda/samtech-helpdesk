<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="position-fixed top-0 start-0 w-100 h-100 overflow-hidden" style="z-index:-1;">
        <div class="security-bg security-bg-1">
            <i class="bi bi-shield-lock"></i>
        </div>

        <div class="security-bg security-bg-2">
            <i class="bi bi-key"></i>
        </div>

        <div class="security-bg security-bg-3">
            <i class="bi bi-envelope-check"></i>
        </div>

        <div class="security-bg security-bg-4">
            <i class="bi bi-fingerprint"></i>
        </div>
    </div>

    <div class="auth-card w-100 otp-card" style="max-width: 460px;">

        <div class="text-center mb-4">

            <img
                src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                alt="Samtech"
                class="mb-3"
                style="max-height:80px;">

            <h3 class="fw-bold mb-2">
                Verify Login OTP
            </h3>

            <p class="text-muted mb-0">
                Enter the 6-digit verification code sent to your registered email address.
            </p>

        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger shake-animation">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= BASE_URL ?>/user-login-otp"
              onsubmit="showSamtechLoader('Verifying OTP...')">

            <?= Csrf::field(); ?>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Verification Code
                </label>

                <input
                    type="text"
                    name="otp"
                    class="form-control otp-input text-center"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    placeholder="● ● ● ● ● ●"
                    required
                    autofocus>

                <small class="text-muted">
                    OTP expires in 10 minutes.
                </small>

            </div>

            <button
                type="submit"
                class="btn btn-primary-custom w-100 py-2 fw-semibold">

                <i class="bi bi-shield-check me-2"></i>
                Verify OTP

            </button>

        </form>

        <div class="text-center mt-4">

            <a href="<?= BASE_URL ?>/user-login"
               class="text-decoration-none fw-semibold">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Login

            </a>

        </div>

    </div>

</div>

<style>

.otp-card {
    position: relative;
}

.otp-input {
    height: 58px;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 8px;
    border-radius: 12px;
}

.security-bg {
    position: absolute;
    color: rgba(177,233,111,.08);
    animation: floatIcon 10s infinite ease-in-out;
}

.security-bg i {
    font-size: 90px;
}

.security-bg-1 {
    top: 10%;
    left: 8%;
}

.security-bg-2 {
    top: 65%;
    left: 15%;
}

.security-bg-3 {
    top: 20%;
    right: 12%;
}

.security-bg-4 {
    top: 70%;
    right: 10%;
}

@keyframes floatIcon {

    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-20px);
    }

    100% {
        transform: translateY(0px);
    }
}

.shake-animation {
    animation: shake .35s ease-in-out;
}

@keyframes shake {

    0% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
    100% { transform: translateX(0); }

}

</style>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>
