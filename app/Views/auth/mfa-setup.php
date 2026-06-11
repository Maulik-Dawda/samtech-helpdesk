<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
$formattedSecret = trim(chunk_split($secret, 4, ' '));
?>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-phone security-icon icon-2"></i>
    <i class="bi bi-key security-icon icon-3"></i>
    <i class="bi bi-fingerprint security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width:560px;">

            <div class="text-center mb-4">

                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-3">

                <p class="login-subtitle mb-0">
                    Authenticator Setup
                </p>

            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>Setup Failed</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="setup-card mb-4">

                <div class="setup-title">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Setup Instructions
                </div>

                <ol class="mb-0 mt-3 ps-3">
                    <li>Open Microsoft Authenticator.</li>
                    <li>Tap <strong>+</strong> to add account.</li>
                    <li>Select <strong>Other Account</strong>.</li>
                    <li>Choose <strong>Enter Setup Key Manually</strong>.</li>
                    <li>Enter the key shown below.</li>
                    <li>Enter generated 6-digit code.</li>
                </ol>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Setup Key
                </label>

                <div class="secret-box">

                    <?= htmlspecialchars($formattedSecret); ?>

                </div>

                <button
                    type="button"
                    class="btn btn-copy mt-2 w-100"
                    onclick="copySecret()">

                    <i class="bi bi-copy me-2"></i>
                    Copy Setup Key

                </button>

                <small class="text-muted d-block mt-2">
                    Copy and paste directly into Microsoft Authenticator.
                </small>

            </div>

            <form
                method="POST"
                action="<?= BASE_URL ?>/mfa-setup"
                onsubmit="showSamtechLoader('Verifying authenticator setup...')">

                <?= Csrf::field(); ?>

                <div class="mb-4">

                    <label class="form-label">
                        Verification Code
                    </label>

                    <div class="input-wrap">

                        <i class="bi bi-fingerprint input-icon"></i>

                        <input
                            type="text"
                            name="code"
                            class="form-control otp-input"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            placeholder="000000"
                            required
                            autofocus>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-login w-100">

                    Verify & Enable MFA

                </button>

            </form>

            <div class="text-center mt-4">

                <a
                    href="<?= BASE_URL ?>/logout"
                    class="back-link text-decoration-none">

                    Cancel & Logout

                </a>

            </div>

        </div>

        <div class="footer-text mt-3">
            <i class="bi bi-shield-check text-success me-1"></i>
            © <?= date('Y'); ?> Samtech Solutions. All rights reserved.
        </div>

    </div>

</div>

<script>
function copySecret()
{
    navigator.clipboard.writeText('<?= htmlspecialchars($secret); ?>');

    const btn = document.querySelector('.btn-copy');

    btn.innerHTML =
        '<i class="bi bi-check-circle me-2"></i>Copied';

    setTimeout(() => {

        btn.innerHTML =
            '<i class="bi bi-copy me-2"></i>Copy Setup Key';

    }, 2000);
}
</script>

<style>

.setup-card{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:16px;
}

.setup-title{
    font-weight:700;
    color:#111827;
}

.secret-box{
    background:#f8fafc;
    border:2px dashed #b1e96f;
    border-radius:12px;
    padding:18px;
    text-align:center;
    font-size:18px;
    font-weight:800;
    letter-spacing:3px;
    color:#111827;
    word-break:break-word;
}

.btn-copy{
    border:1px solid #d6dde8;
    background:#fff;
    border-radius:10px;
    height:44px;
    font-weight:700;
}

.btn-copy:hover{
    background:#f8fafc;
}

.otp-input{
    text-align:center;
    font-size:20px !important;
    letter-spacing:6px;
    font-weight:800;
}

</style>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>