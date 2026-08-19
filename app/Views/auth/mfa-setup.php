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
        margin: 0;
        padding: 0;
        overflow: hidden;
        background:
            radial-gradient(circle at 15% 22%, rgba(177, 233, 111, .16), transparent 28%),
            radial-gradient(circle at 85% 70%, rgba(76, 175, 80, .14), transparent 30%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 58%, #f1f8ee 100%);
    }

    .auth-page {
        height: 100vh;
        width: 100vw;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
    }

    .auth-card {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, .96);
        border-radius: 20px;
        padding: 22px 28px;
        box-shadow: 0 16px 50px rgba(15, 23, 42, .10);
        border-top: 4px solid #6cb33f;
        backdrop-filter: blur(16px);
        max-height: calc(100vh - 36px);
        display: flex;
        flex-direction: column;
    }

    .auth-header {
        margin-bottom: 14px;
    }

    .auth-logo {
        height: 42px;
        max-width: 200px;
        width: auto;
        object-fit: contain;
    }

    .login-subtitle {
        font-size: 13.5px;
        color: #64748b;
        font-weight: 600;
    }

    .secure-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f0f9eb;
        color: #3b941f;
        border: 1px solid rgba(108, 179, 63, .30);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }

    .form-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        font-size: 13px;
    }

    /* Mode Switcher Tabs */
    .setup-mode-nav {
        background: #f1f5f9;
        padding: 3px;
        border-radius: 12px;
        display: flex;
        gap: 3px;
        margin-bottom: 12px;
    }

    .setup-mode-btn {
        flex: 1;
        padding: 7px 10px;
        border: 0;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        background: transparent;
        transition: all .2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
    }

    .setup-mode-btn.active {
        background: #ffffff;
        color: #3b941f;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .08);
    }

    /* Left Column Container */
    .left-col-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .qr-scanner-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
    }

    .qr-frame-card {
        position: relative;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 8px;
        box-shadow: 0 8px 24px rgba(108, 179, 63, 0.10);
        display: inline-block;
    }

    .corner-bracket {
        position: absolute;
        width: 16px;
        height: 16px;
        border-color: #6cb33f;
        border-style: solid;
        pointer-events: none;
    }

    .corner-tl { top: 2px; left: 2px; border-width: 3px 0 0 3px; border-top-left-radius: 5px; }
    .corner-tr { top: 2px; right: 2px; border-width: 3px 3px 0 0; border-top-right-radius: 5px; }
    .corner-bl { bottom: 2px; left: 2px; border-width: 0 0 3px 3px; border-bottom-left-radius: 5px; }
    .corner-br { bottom: 2px; right: 2px; border-width: 0 3px 3px 0; border-bottom-right-radius: 5px; }

    .qr-image {
        width: 140px;
        height: 140px;
        display: block;
        border-radius: 4px;
    }

    .scanner-badge {
        font-size: 11px;
        font-weight: 700;
        color: #3b941f;
        background: #ffffff;
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
        background: #ffffff;
        border: 1px solid #d6dde8;
        border-radius: 10px;
        padding: 10px 12px;
        text-align: center;
        font-size: 13.5px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #0f172a;
        word-break: break-word;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-copy {
        width: 46px;
        border: 1px solid #d6dde8;
        background: #fff;
        border-radius: 10px;
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

    /* Right Column Form */
    .right-col-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 16px;
        z-index: 2;
    }

    .auth-card .form-control {
        height: 44px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding-left: 44px;
        font-size: 14px;
        color: #0f172a;
        box-shadow: none;
        background: #ffffff;
    }

    .auth-card .form-control:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .18rem rgba(108, 179, 63, .18);
    }

    .otp-input {
        text-align: center;
        font-size: 18px !important;
        font-weight: 800;
        letter-spacing: 5px;
        padding-left: 44px !important;
    }

    .btn-login {
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6cb33f, #3b941f);
        border: 0;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(67, 160, 38, .22);
    }

    .btn-login:hover {
        color: #fff;
        background: linear-gradient(135deg, #5aa231, #2f8318);
    }

    .back-link {
        font-weight: 700;
        font-size: 13px;
        color: #3b941f;
    }

    .back-link:hover {
        color: #2f8318;
    }

    .alert-custom {
        border: 0;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 12.5px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .footer-text {
        color: #64748b;
        font-size: 12px;
        margin-top: 8px;
    }

    @media(max-width: 767px) {
        html, body {
            overflow: auto;
        }
        .auth-page {
            height: auto;
            min-height: 100vh;
        }
        .auth-card {
            max-height: none;
            padding: 20px 16px;
        }
    }
</style>

<div class="auth-page">

    <div class="container d-flex flex-column align-items-center justify-content-center">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 780px;">

            <!-- Compact Header -->
            <div class="text-center auth-header">
                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-1">

                <p class="login-subtitle mb-0">
                    Two-Factor Authentication Setup
                </p>

                <div class="secure-pill">
                    <i class="bi bi-shield-check"></i>
                    Authenticator App Verification
                </div>
            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Setup Failed:</strong> <?= htmlspecialchars($_SESSION['error']); ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- 2-Column Split View -->
            <div class="row g-3 align-items-stretch">

                <!-- Left Column: Tabbed Switcher (Scan QR Code vs Manual Key) -->
                <div class="col-md-6 d-flex flex-column">
                    <div class="left-col-box">
                        <!-- Tabs -->
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
                                <div class="qr-frame-card">
                                    <div class="position-relative d-inline-block">
                                        <span class="corner-bracket corner-tl"></span>
                                        <span class="corner-bracket corner-tr"></span>
                                        <span class="corner-bracket corner-bl"></span>
                                        <span class="corner-bracket corner-br"></span>
                                        <img src="<?= $qrCodeDataUri; ?>" alt="2FA QR Code" class="qr-image">
                                    </div>
                                </div>
                                <div class="scanner-badge">
                                    <i class="bi bi-camera me-1"></i> Scan in App
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tab 2: Manual Key View -->
                        <div id="setupViewManual" class="flex-grow-1 d-flex flex-column justify-content-center" style="display: none;">
                            <label class="form-label text-center">
                                Setup Secret Key
                            </label>
                            <div class="secret-row mb-2">
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
                            <small class="text-muted text-center d-block" style="font-size: 11.5px;">
                                Enter this key manually into Microsoft or Google Authenticator.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Verification Form -->
                <div class="col-md-6">
                    <div class="right-col-box">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i> Complete Setup
                            </h6>
                            <p class="text-muted small mb-3">
                                Scan the QR code or enter the secret key in your authenticator app, then enter the 6-digit code below.
                            </p>

                            <form
                                method="POST"
                                action="<?= BASE_URL ?>/mfa-setup"
                                onsubmit="showSamtechLoader('Activating authenticator...')">

                                <?= Csrf::field(); ?>

                                <div class="mb-3">
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

                                <button type="submit" class="btn btn-login w-100 mb-2">
                                    <i class="bi bi-shield-check me-1"></i> Activate Authenticator
                                </button>
                            </form>
                        </div>

                        <div class="text-center pt-2 border-top">
                            <a href="<?= BASE_URL ?>/logout" class="back-link text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Cancel & Logout
                            </a>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <div class="footer-text">
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
        viewManual.style.display = 'flex';
        viewQr.style.display = 'none';
    }
}

function copySecret() {
    navigator.clipboard.writeText('<?= htmlspecialchars($secret); ?>');

    const icon = document.getElementById('copyIcon');
    icon.classList.remove('bi-copy');
    icon.classList.add('bi-check-circle-fill');

    setTimeout(() => {
        icon.classList.remove('bi-check-circle-fill');
        icon.classList.add('bi-copy');
    }, 2000);
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>