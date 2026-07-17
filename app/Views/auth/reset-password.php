
<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
?>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-key security-icon icon-2"></i>
    <i class="bi bi-lock security-icon icon-3"></i>
    <i class="bi bi-shield-check security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width:460px;">

            <div class="text-center mb-4">

                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-3">

                <p class="login-subtitle mb-0">
                    Reset Password
                </p>

            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>Password update failed</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-custom alert-success-custom mb-3">
                    <i class="bi bi-check-circle-fill"></i>

                    <div>
                        <strong>Success</strong>
                        <div>
                            <?= htmlspecialchars($_SESSION['success']); ?>
                        </div>
                    </div>

                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/reset-password"
                onsubmit="showSamtechLoader('Updating password...')">

                <?= Csrf::field(); ?>

                <div class="mb-3">

                    <label class="form-label">
                        New Password
                    </label>

                    <div class="input-wrap">

                        <i class="bi bi-lock-fill input-icon"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            minlength="8"
                            placeholder="Enter new password"
                            required>

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <small class="text-muted d-block mt-2">
                        Minimum 8 characters required.
                    </small>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Confirm Password
                    </label>

                    <div class="input-wrap">

                        <i class="bi bi-shield-lock-fill input-icon"></i>

                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            class="form-control"
                            minlength="8"
                            placeholder="Confirm new password"
                            required>

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('confirm_password', this)">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-login w-100">

                    Update Password

                </button>

            </form>

            <div class="text-center mt-4">

                <a
                    href="<?= BASE_URL ?>/user-login"
                    class="text-decoration-none back-link">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Login

                </a>

            </div>

        </div>

        <div class="footer-text mt-3">
            <i class="bi bi-shield-check text-success me-1"></i>
            © <?= date('Y'); ?> Samtech Solutions. All rights reserved.
        </div>

    </div>

</div>

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
        background: rgba(255, 255, 255, .94);
        border-radius: 20px;
        padding: 26px 30px;
        box-shadow: 0 18px 55px rgba(15, 23, 42, .10);
        border-top: 4px solid #6cb33f;
        backdrop-filter: blur(14px);
    }

    .auth-logo {
        height: 85px;
        max-width: 320px;
        width: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .login-subtitle {
        font-size: 16px;
        color: #64748b;
    }

    .form-label {
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
        font-size: 14px;
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
        padding-right: 50px;
        font-size: 14px;
        color: #111827;
        box-shadow: none;
    }

    .auth-card .form-control:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .18rem rgba(108, 179, 63, .14);
    }

    .password-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        border-radius: 8px;
        transition: .2s;
        z-index: 5;
    }

    .password-toggle:hover {
        background: #f0f9eb;
        color: #3b941f;
    }

    .password-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 .18rem rgba(108,179,63,.14);
    }

    .btn-login {
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg,#6cb33f,#3b941f);
        border: 0;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(67,160,38,.22);
    }

    .btn-login:hover {
        color:#fff;
        background:linear-gradient(135deg,#5aa231,#2f8318);
    }

    .back-link {
        font-weight:600;
        font-size:14px;
        color:#3b941f;
    }

    .back-link:hover {
        color:#2f8318;
    }

    .alert-custom {
        border:0;
        border-radius:12px;
        padding:11px 14px;
        font-size:14px;
        display:flex;
        align-items:flex-start;
        gap:10px;
    }

    .alert-error {
        background:#fee2e2;
        color:#991b1b;
    }

    .alert-success-custom {
        background:#dcfce7;
        color:#166534;
    }

    .alert-custom i {
        font-size:18px;
        margin-top:1px;
    }

    .shake {
        animation:shake .45s ease-in-out;
    }

    @keyframes shake {
        0% { transform:translateX(0);}
        20% { transform:translateX(-8px);}
        40% { transform:translateX(8px);}
        60% { transform:translateX(-6px);}
        80% { transform:translateX(6px);}
        100% { transform:translateX(0);}
    }

    .security-icon {
        position:absolute;
        color:rgba(76,175,80,.13);
        font-size:34px;
        z-index:1;
        animation:floatIcon 7s ease-in-out infinite;
    }

    .icon-1 { left:13%; top:28%; }
    .icon-2 { right:13%; top:52%; animation-delay:1.2s; }
    .icon-3 { left:22%; bottom:17%; animation-delay:2s; }
    .icon-4 { right:24%; top:13%; animation-delay:.6s; }

    @keyframes floatIcon {
        0%,100% {
            transform:translateY(0);
        }
        50% {
            transform:translateY(-14px);
        }
    }

    .footer-text {
        position:relative;
        z-index:2;
        color:#64748b;
        font-size:13px;
    }

    @media (max-height:760px){

        .auth-card{
            padding:22px 28px;
        }

        .auth-logo{
            height:70px;
        }

        .mb-4{
            margin-bottom:1rem!important;
        }

    }
</style>

<script>
function togglePassword(id, btn)
{
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>