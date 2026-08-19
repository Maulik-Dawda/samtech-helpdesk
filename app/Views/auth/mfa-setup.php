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
        padding: 24px 30px;
        box-shadow: 0 16px 50px rgba(15, 23, 42, .10);
        border-top: 4px solid #6cb33f;
        backdrop-filter: blur(16px);
        max-height: calc(100vh - 40px);
        display: flex;
        flex-direction: column;
    }

    .auth-header {
        margin-bottom: 16px;
    }

    .auth-logo {
        height: 46px;
        max-width: 220px;
        width: auto;
        object-fit: contain;
    }

    .login-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
    }

    .secure-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f9eb;
        color: #3b941f;
        border: 1px solid rgba(108, 179, 63, .30);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
    }

    .form-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        font-size: 13px;
    }

    /* Left Column QR Scanner */
    .qr-scanner-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
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

    /* Right Column Form */
    .right-col-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
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

    .secret-box-small {
        background: #ffffff;
        border: 1px solid #d6dde8;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #0f172a;
        font-family: monospace;
    }

    .btn-copy-sm {
        border: 1px solid #d6dde8;
        background: #fff;
        border-radius: 8px;
        color: #3b941f;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 8px;
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
        padding: 10px 14px;
        font-size: 13px;
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
        margin-top: 10px;
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

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 760px;">

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

                <!-- Left Column: Pure QR Code Scanner (No Key text inside scanner box) -->
                <div class="col-md-5 d-flex flex-column">
                    <div class="qr-scanner-box">
                        <div class="text-center mb-2">
                            <span class="fw-bold text-dark small">Scan QR Code</span>
                        </div>
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
                </div>

                <!-- Right Column: Verification Form & Manual Key Option -->
                <div class="col-md-7">
                    <div class="right-col-box">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i> Complete Setup
                            </h6>
                            <p class="text-muted small mb-3">
                                Scan the QR code with Microsoft Authenticator or Google Authenticator and enter the code below.
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

                        <!-- Manual Key Fallback Collapsible -->
                        <div class="pt-2 border-top mt-2">
                            <details style="cursor: pointer;">
                                <summary class="text-muted small fw-semibold">
                                    Can't scan? Use manual setup key
                                </summary>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <div class="secret-box-small flex-grow-1 text-center" id="setupKeyBox">
                                        <?= htmlspecialchars($formattedSecret); ?>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn-copy-sm"
                                        onclick="copySecret()"
                                        title="Copy setup key">
                                        <i class="bi bi-copy" id="copyIcon"></i> Copy
                                    </button>
                                </div>
                            </details>

                            <div class="text-center mt-2">
                                <a href="<?= BASE_URL ?>/logout" class="back-link text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel & Logout
                                </a>
                            </div>
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