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
        min-height: 100vh;
        background:
            radial-gradient(circle at 15% 22%, rgba(177, 233, 111, .16), transparent 28%),
            radial-gradient(circle at 85% 70%, rgba(76, 175, 80, .14), transparent 30%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 58%, #f1f8ee 100%);
    }

    .auth-page {
        min-height: 100vh;
        position: relative;
        padding: 30px 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .auth-card {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, .96);
        border-radius: 24px;
        padding: 32px 32px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .12);
        border-top: 5px solid #6cb33f;
        backdrop-filter: blur(16px);
    }

    .auth-logo {
        height: 64px;
        max-width: 280px;
        width: auto;
        object-fit: contain;
    }

    .login-subtitle {
        font-size: 15px;
        color: #64748b;
        font-weight: 600;
    }

    .secure-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f0f9eb;
        color: #3b941f;
        border: 1px solid rgba(108, 179, 63, .30);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        margin-top: 8px;
    }

    .form-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }

    /* Mode Switcher Tabs */
    .setup-mode-nav {
        background: #f1f5f9;
        padding: 4px;
        border-radius: 14px;
        display: flex;
        gap: 4px;
        margin-bottom: 22px;
    }

    .setup-mode-btn {
        flex: 1;
        padding: 10px 14px;
        border: 0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        background: transparent;
        transition: all .2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
    }

    .setup-mode-btn.active {
        background: #ffffff;
        color: #3b941f;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
    }

    /* Scanner Frame Styling */
    .qr-scanner-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .qr-scanner-card {
        position: relative;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(108, 179, 63, 0.12);
        transition: all .3s ease;
    }

    .qr-scanner-card:hover {
        border-color: #6cb33f;
        box-shadow: 0 12px 36px rgba(108, 179, 63, 0.22);
    }

    .qr-frame {
        position: relative;
        display: inline-block;
        padding: 12px;
        border-radius: 14px;
        background: #f8fafc;
    }

    .corner-bracket {
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #6cb33f;
        border-style: solid;
        pointer-events: none;
    }

    .corner-tl { top: 4px; left: 4px; border-width: 3px 0 0 3px; border-top-left-radius: 8px; }
    .corner-tr { top: 4px; right: 4px; border-width: 3px 3px 0 0; border-top-right-radius: 8px; }
    .corner-bl { bottom: 4px; left: 4px; border-width: 0 0 3px 3px; border-bottom-left-radius: 8px; }
    .corner-br { bottom: 4px; right: 4px; border-width: 0 3px 3px 0; border-bottom-right-radius: 8px; }

    .qr-image {
        width: 170px;
        height: 170px;
        display: block;
        border-radius: 8px;
    }

    .scanner-badge {
        font-size: 11px;
        font-weight: 700;
        color: #3b941f;
        background: #f0f9eb;
        border: 1px solid rgba(108, 179, 63, 0.25);
        padding: 4px 10px;
        border-radius: 20px;
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Secret Key Box */
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
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #0f172a;
        word-break: break-word;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-copy {
        width: 52px;
        border: 1px solid #d6dde8;
        background: #fff;
        border-radius: 12px;
        color: #334155;
        font-weight: 800;
        transition: all .2s ease;
        cursor: pointer;
    }

    .btn-copy:hover {
        background: #f0f9eb;
        color: #3b941f;
        border-color: #6cb33f;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 18px;
        z-index: 2;
    }

    .auth-card .form-control {
        height: 50px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding-left: 48px;
        font-size: 15px;
        color: #0f172a;
        box-shadow: none;
        transition: all .2s ease;
    }

    .auth-card .form-control:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .22rem rgba(108, 179, 63, .18);
    }

    .otp-input {
        text-align: center;
        font-size: 20px !important;
        font-weight: 800;
        letter-spacing: 6px;
        padding-left: 48px !important;
    }

    .btn-login {
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6cb33f, #3b941f);
        border: 0;
        color: #fff;
        font-size: 15px;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(67, 160, 38, .25);
        transition: all .2s ease;
    }

    .btn-login:hover {
        color: #fff;
        background: linear-gradient(135deg, #5aa231, #2f8318);
        box-shadow: 0 14px 28px rgba(67, 160, 38, .32);
    }

    .back-link {
        font-weight: 700;
        font-size: 14px;
        color: #3b941f;
    }

    .back-link:hover {
        color: #2f8318;
    }

    .alert-custom {
        border: 0;
        border-radius: 12px;
        padding: 12px 16px;
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
        color: rgba(76, 175, 80, .14);
        font-size: 36px;
        z-index: 1;
        animation: floatIcon 7s ease-in-out infinite;
    }

    .icon-1 { left: 10%; top: 25%; }
    .icon-2 { right: 10%; top: 50%; animation-delay: 1.2s; }
    .icon-3 { left: 18%; bottom: 15%; animation-delay: 2s; }
    .icon-4 { right: 20%; top: 12%; animation-delay: .6s; }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-14px); }
    }

    .footer-text {
        position: relative;
        z-index: 2;
        color: #64748b;
        font-size: 13px;
    }

    @media(max-width: 576px) {
        .auth-card {
            padding: 24px 20px;
        }
    }
</style>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-qr-code-scan security-icon icon-2"></i>
    <i class="bi bi-key security-icon icon-3"></i>
    <i class="bi bi-fingerprint security-icon icon-4"></i>

    <div class="container d-flex flex-column align-items-center justify-content-center py-4">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 480px;">

            <div class="text-center mb-4">
                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-2">

                <p class="login-subtitle mb-0">
                    Two-Factor Authentication Setup
                </p>

                <div class="secure-pill">
                    <i class="bi bi-shield-check"></i>
                    Authenticator App Verification
                </div>
            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-4">
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

            <!-- Navigation Mode Switcher -->
            <div class="setup-mode-nav">
                <button type="button" class="setup-mode-btn active" id="btnModeQr" onclick="switchSetupMode('qr')">
                    <i class="bi bi-qr-code-scan"></i> Scan QR Code
                </button>
                <button type="button" class="setup-mode-btn" id="btnModeManual" onclick="switchSetupMode('manual')">
                    <i class="bi bi-key"></i> Manual Key
                </button>
            </div>

            <!-- Tab 1: QR Code Scanner View -->
            <div id="setupViewQr" class="qr-scanner-wrapper">
                <?php if (!empty($qrCodeDataUri)): ?>
                    <div class="qr-scanner-card">
                        <div class="qr-frame">
                            <span class="corner-bracket corner-tl"></span>
                            <span class="corner-bracket corner-tr"></span>
                            <span class="corner-bracket corner-bl"></span>
                            <span class="corner-bracket corner-br"></span>
                            <img src="<?= $qrCodeDataUri; ?>" alt="2FA Scanner QR Code" class="qr-image">
                        </div>
                    </div>
                    <div class="scanner-badge">
                        <i class="bi bi-camera me-1"></i> Scan with Microsoft or Google Authenticator
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 2: Manual Setup Key View -->
            <div id="setupViewManual" class="mb-4" style="display: none;">
                <label class="form-label">
                    Setup Secret Key
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
                    Enter this secret key manually into your authenticator app if you cannot scan the QR code.
                </small>
            </div>

            <!-- 6-Digit Verification Code Form -->
            <form
                method="POST"
                action="<?= BASE_URL ?>/mfa-setup"
                onsubmit="showSamtechLoader('Activating authenticator...')">

                <?= Csrf::field(); ?>

                <div class="mb-4">
                    <label class="form-label">
                        Enter 6-Digit Verification Code
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

                <button type="submit" class="btn btn-login w-100 mb-3">
                    <i class="bi bi-shield-lock me-1"></i> Activate 2FA Security
                </button>

            </form>

            <div class="text-center">
                <a href="<?= BASE_URL ?>/logout" class="back-link text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Cancel & Logout
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
function switchSetupMode(mode) {
    const btnQr = document.getElementById('btnModeQr');
    const btnManual = document.getElementById('btnModeManual');
    const viewQr = document.getElementById('setupViewQr');
    const viewManual = document.getElementById('setupViewManual');

    if (mode === 'qr') {
        btnQr.classList.add('active');
        btnManual.classList.remove('active');
        viewQr.style.display = 'flex';
        viewManual.style.display = 'none';
    } else {
        btnManual.classList.add('active');
        btnQr.classList.remove('active');
        viewManual.style.display = 'block';
        viewQr.style.display = 'none';
    }
}

function copySecret() {
    navigator.clipboard.writeText('<?= htmlspecialchars($secret); ?>');

    const icon = document.getElementById('copyIcon');
    icon.classList.remove('bi-copy');
    icon.classList.add('bi-check-circle-fill');
    icon.style.color = '#3b941f';

    setTimeout(() => {
        icon.classList.remove('bi-check-circle-fill');
        icon.classList.add('bi-copy');
        icon.style.color = '';
    }, 2000);
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>