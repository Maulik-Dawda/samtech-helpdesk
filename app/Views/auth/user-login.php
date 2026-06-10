<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
?>

<style>
    .auth-page {
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 15% 20%, rgba(177, 233, 111, .16), transparent 28%),
            radial-gradient(circle at 85% 70%, rgba(76, 175, 80, .15), transparent 30%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 55%, #f1f8ee 100%);
    }

    .auth-page::before {
        content: "";
        position: absolute;
        width: 700px;
        height: 700px;
        border: 1px solid rgba(76, 175, 80, .12);
        border-radius: 50%;
        left: -250px;
        bottom: -300px;
    }

    .auth-page::after {
        content: "";
        position: absolute;
        width: 650px;
        height: 650px;
        border: 1px solid rgba(76, 175, 80, .10);
        border-radius: 50%;
        right: -260px;
        top: 120px;
    }

    .auth-card {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, .92);
        border-radius: 24px;
        padding: 42px 36px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .10);
        border-top: 4px solid #6cb33f;
        backdrop-filter: blur(14px);
    }

    .auth-logo {
        height: 72px;
        max-width: 260px;
        object-fit: contain;
    }

    .form-label {
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        font-size: 18px;
        z-index: 2;
    }

    .auth-card .form-control,
    .auth-card .form-select {
        min-height: 54px;
        border-radius: 10px;
        border: 1px solid #d6dde8;
        padding-left: 52px;
        font-size: 15px;
        color: #111827;
        box-shadow: none;
    }

    .auth-card .form-select {
        padding-right: 42px;
    }

    .auth-card .form-control:focus,
    .auth-card .form-select:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .2rem rgba(108, 179, 63, .16);
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #334155;
        font-size: 18px;
        z-index: 3;
    }

    .btn-login {
        min-height: 54px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6cb33f, #3b941f);
        border: 0;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(67, 160, 38, .24);
    }

    .btn-login:hover {
        color: #fff;
        background: linear-gradient(135deg, #5aa231, #2f8318);
    }

    .shake {
        animation: shake .48s ease-in-out;
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        20% { transform: translateX(-10px); }
        40% { transform: translateX(10px); }
        60% { transform: translateX(-8px); }
        80% { transform: translateX(8px); }
        100% { transform: translateX(0); }
    }

    .security-icon {
        position: absolute;
        color: rgba(76, 175, 80, .16);
        font-size: 42px;
        z-index: 1;
        animation: floatIcon 7s ease-in-out infinite;
    }

    .security-icon.icon-1 { left: 13%; top: 28%; }
    .security-icon.icon-2 { right: 13%; top: 52%; animation-delay: 1.2s; }
    .security-icon.icon-3 { left: 22%; bottom: 18%; animation-delay: 2s; }
    .security-icon.icon-4 { right: 24%; top: 14%; animation-delay: .6s; }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-16px); }
    }

    .footer-text {
        position: relative;
        z-index: 2;
        color: #64748b;
        font-size: 14px;
    }

    .forgot-link {
        font-weight: 600;
    }
</style>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-lock security-icon icon-2"></i>
    <i class="bi bi-key security-icon icon-3"></i>
    <i class="bi bi-fingerprint security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 460px;">

            <div class="text-center mb-4">

                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-3"
                >

                <p class="text-muted mb-0 fs-5">
                    User / Agent Login
                </p>

            </div>

            <?php if ($hasError): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/user-login">

                <?= Csrf::field(); ?>

                <div class="mb-4">
                    <label class="form-label">Login As</label>

                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>

                        <select name="login_type" class="form-select" required>
                            <option value="user" selected>User Login</option>
                            <option value="agent">Agent Login</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Email Address</label>

                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter your email address"
                            autocomplete="email"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>

                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="text-end mb-4">
                    <a href="<?= BASE_URL ?>/forgot-password" class="text-decoration-none forgot-link">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Login
                </button>

            </form>

        </div>

        <div class="footer-text mt-4">
            <i class="bi bi-shield-check text-success me-1"></i>
            © <?= date('Y'); ?> Samtech Solutions. All rights reserved.
        </div>

    </div>

</div>

<script>
function togglePassword()
{
    const password = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');

    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>