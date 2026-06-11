<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
$formattedSecret = trim(chunk_split($secret, 4, ' '));
?>

<style>
    html,
    body {
        height: 100%;
        overflow: hidden;
    }

    .auth-page {
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 15% 22%, rgba(177, 233, 111, .14), transparent 28%),
            radial-gradient(circle at 85% 70%, rgba(76, 175, 80, .13), transparent 30%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 58%, #f1f8ee 100%);
    }

    .auth-card {
        position: relative;
        z-index: 2;
        background: rgba(255,255,255,.94);
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 18px 55px rgba(15,23,42,.10);
        border-top: 4px solid #6cb33f;
        backdrop-filter: blur(14px);
    }

    .auth-logo {
        height: 72px;
        max-width: 300px;
        width: auto;
        object-fit: contain;
    }

    .login-subtitle {
        font-size: 16px;
        color: #64748b;
    }

    .secure-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f0f9eb;
        color: #3b941f;
        border: 1px solid rgba(108,179,63,.25);
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        margin-top: 8px;
    }

    .form-label {
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .setup-steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .setup-step {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
    }

    .step-icon {
        width: 34px;
        height: 34px;
        margin: 0 auto 7px;
        border-radius: 50%;
        background: #f0f9eb;
        color: #3b941f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .step-title {
        color: #111827;
        font-size: 12px;
        font-weight: 800;
    }

    .step-text {
        color: #64748b;
        font-size: 11px;
        line-height: 1.3;
        margin-top: 2px;
    }

    .secret-row {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }

    .secret-box {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #d6dde8;
        border-radius: 12px;
        padding: 13px 14px;
        text-align: center;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #111827;
        word-break: break-word;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-copy {
        width: 50px;
        border: 1px solid #d6dde8;
        background: #fff;
        border-radius: 12px;
        color: #334155;
        font-weight: 800;
    }

    .btn-copy:hover {
        background: #f8fafc;
        color: #3b941f;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        font-size: 16px;
        z-index: 2;
    }

    .auth-card .form-control {
        height: 48px;
        border-radius: 10px;
        border: 1px solid #d6dde8;
        padding-left: 48px;
        font-size: 14px;
        color: #111827;
        box-shadow: none;
    }

    .auth-card .form-control:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .18rem rgba(108,179,63,.14);
    }

    .otp-input {
        text-align: center;
        font-size: 18px !important;
        font-weight: 800;
        letter-spacing: 5px;
        padding-left: 48px !important;
    }

    .btn-login {
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6cb33f, #3b941f);
        border: 0;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(67,160,38,.22);
    }

    .btn-login:hover {
        color: #fff;
        background: linear-gradient(135deg, #5aa231, #2f8318);
    }

    .back-link {
        font-weight: 600;
        font-size: 14px;
        color: #3b941f;
    }

    .back-link:hover {
        color: #2f8318;
    }

    .alert-custom {
        border: 0;
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-custom i {
        font-size: 18px;
        margin-top: 1px;
    }

    .shake {
        animation: shake .45s ease-in-out;
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-6px); }
        80% { transform: translateX(6px); }
        100% { transform: translateX(0); }
    }

    .security-icon {
        position: absolute;
        color: rgba(76,175,80,.13);
        font-size: 34px;
        z-index: 1;
        animation: floatIcon 7s ease-in-out infinite;
    }

    .icon-1 { left: 13%; top: 28%; }
    .icon-2 { right: 13%; top: 52%; animation-delay: 1.2s; }
    .icon-3 { left: 22%; bottom: 17%; animation-delay: 2s; }
    .icon-4 { right: 24%; top: 13%; animation-delay: .6s; }

    @keyframes floatIcon {
        0%,100% { transform: translateY(0); }
        50% { transform: translateY(-14px); }
    }

    .footer-text {
        position: relative;
        z-index: 2;
        color: #64748b;
        font-size: 13px;
    }

    @media(max-height: 780px) {
        .auth-card {
            padding: 20px 26px;
        }

        .auth-logo {
            height: 60px;
        }

        .setup-steps {
            margin-bottom: 14px;
        }

        .step-text {
            display: none;
        }
    }

    @media(max-width: 576px) {
        .setup-steps {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-phone security-icon icon-2"></i>
    <i class="bi bi-key security-icon icon-3"></i>
    <i class="bi bi-fingerprint security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 500px;">

            <div class="text-center mb-3">

                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-2">

                <p class="login-subtitle mb-0">
                    Authenticator Setup
                </p>

                <div class="secure-pill">
                    <i class="bi bi-shield-check"></i>
                    Microsoft Authenticator
                </div>

            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>Setup failed</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="setup-steps">

                <div class="setup-step">
                    <div class="step-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="step-title">
                        Open App
                    </div>
                    <div class="step-text">
                        Microsoft Authenticator
                    </div>
                </div>

                <div class="setup-step">
                    <div class="step-icon">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <div class="step-title">
                        Add Account
                    </div>
                    <div class="step-text">
                        Other account
                    </div>
                </div>

                <div class="setup-step">
                    <div class="step-icon">
                        <i class="bi bi-key"></i>
                    </div>
                    <div class="step-title">
                        Setup Key
                    </div>
                    <div class="step-text">
                        Enter manually
                    </div>
                </div>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Setup Key
                </label>

                <div class="secret-row">

                    <div class="secret-box" id="setupKeyBox">
                        <?= htmlspecialchars($formattedSecret); ?>
                    </div>

                    <button
                        type="button"
                        class="btn-copy"
                        onclick="copySecret()"
                        title="Copy setup key">

                        <i class="bi bi-copy" id="copyIcon"></i>

                    </button>

                </div>

                <small class="text-muted d-block mt-2">
                    Copy this key and paste it in Microsoft Authenticator.
                </small>

            </div>

            <form
                method="POST"
                action="<?= BASE_URL ?>/mfa-setup"
                onsubmit="showSamtechLoader('Activating authenticator...')">

                <?= Csrf::field(); ?>

                <div class="mb-3">

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
                            autocomplete="one-time-code"
                            required
                            autofocus>

                    </div>

                </div>

                <button type="submit" class="btn btn-login w-100">
                    Activate Authenticator
                </button>

            </form>

            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>/logout" class="back-link text-decoration-none">
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

    const icon = document.getElementById('copyIcon');

    icon.classList.remove('bi-copy');
    icon.classList.add('bi-check-circle');

    setTimeout(() => {
        icon.classList.remove('bi-check-circle');
        icon.classList.add('bi-copy');
    }, 1800);
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>