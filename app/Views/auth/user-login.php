<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hasError = isset($_SESSION['error']);
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

    .auth-card .form-control,
    .auth-card .form-select {
        height: 48px;
        border-radius: 10px;
        border: 1px solid #d6dde8;
        padding-left: 48px;
        font-size: 14px;
        color: #111827;
        box-shadow: none;
    }

    .auth-card .form-control:focus,
    .auth-card .form-select:focus {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .18rem rgba(108, 179, 63, .14);
    }

    .custom-select {
        position: relative;
    }

    .custom-select-display {
        height: 48px;
        border-radius: 10px;
        border: 1px solid #d6dde8;
        background: #fff;
        padding: 0 42px 0 48px;
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        color: #111827;
    }

    .custom-select-display.active {
        border-color: #6cb33f;
        box-shadow: 0 0 0 .18rem rgba(108, 179, 63, .14);
    }

    .select-arrow {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        pointer-events: none;
    }

    .custom-options {
        position: absolute;
        top: 56px;
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 18px 35px rgba(15, 23, 42, .14);
        border: 1px solid #e5e7eb;
        padding: 8px;
        display: none;
        z-index: 50;
    }

    .custom-options.show {
        display: block;
    }

    .custom-option {
        padding: 11px 12px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #111827;
        font-weight: 600;
    }

    .custom-option:hover,
    .custom-option.selected {
        background: #f0f9eb;
        color: #3b941f;
    }

    .custom-option small {
        display: block;
        color: #64748b;
        font-weight: 400;
        font-size: 12px;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #334155;
        font-size: 17px;
        z-index: 3;
    }

    .btn-login {
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6cb33f, #3b941f);
        border: 0;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(67, 160, 38, .22);
    }

    .btn-login:hover {
        color: #fff;
        background: linear-gradient(135deg, #5aa231, #2f8318);
    }

    .shake {
        animation: shake .45s ease-in-out;
    }

    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-8px);
        }

        40% {
            transform: translateX(8px);
        }

        60% {
            transform: translateX(-6px);
        }

        80% {
            transform: translateX(6px);
        }

        100% {
            transform: translateX(0);
        }
    }

    .security-icon {
        position: absolute;
        color: rgba(76, 175, 80, .13);
        font-size: 34px;
        z-index: 1;
        animation: floatIcon 7s ease-in-out infinite;
    }

    .icon-1 {
        left: 13%;
        top: 28%;
    }

    .icon-2 {
        right: 13%;
        top: 52%;
        animation-delay: 1.2s;
    }

    .icon-3 {
        left: 22%;
        bottom: 17%;
        animation-delay: 2s;
    }

    .icon-4 {
        right: 24%;
        top: 13%;
        animation-delay: .6s;
    }

    @keyframes floatIcon {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-14px);
        }
    }

    .footer-text {
        position: relative;
        z-index: 2;
        color: #64748b;
        font-size: 13px;
    }

    .forgot-link {
        font-weight: 600;
        font-size: 14px;
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

    .alert-success-custom {
        background: #dcfce7;
        color: #166534;
    }

    .alert-custom i {
        font-size: 18px;
        margin-top: 1px;
    }

    @media(max-height: 760px) {
        .auth-card {
            padding: 22px 28px;
        }

        @media(max-height: 760px) {
            .auth-logo {
                height: 70px;
            }
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }
    }
</style>

<div class="auth-page">

    <i class="bi bi-shield-lock security-icon icon-1"></i>
    <i class="bi bi-lock security-icon icon-2"></i>
    <i class="bi bi-key security-icon icon-3"></i>
    <i class="bi bi-fingerprint security-icon icon-4"></i>

    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">

        <div class="auth-card w-100 <?= $hasError ? 'shake' : ''; ?>" style="max-width: 430px;">

            <div class="text-center mb-3">
                <img
                    src="<?= BASE_URL ?>/assets/images/samtech-logo.png"
                    alt="Samtech Helpdesk"
                    class="auth-logo mb-2">

                <p class="login-subtitle mb-0">
                    User / Agent Login
                </p>
            </div>

            <?php if ($hasError): ?>
                <div class="alert-custom alert-error mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>Login failed</strong>
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

            <form method="POST" action="<?= BASE_URL ?>/user-login" onsubmit="showSamtechLoader('Verifying login...')">

                <?= Csrf::field(); ?>

                <div class="mb-3">
                    <label class="form-label">Login As</label>

                    <div class="custom-select" id="loginTypeSelect">
                        <input type="hidden" name="login_type" id="loginTypeValue" value="user">

                        <i class="bi bi-person input-icon"></i>

                        <div class="custom-select-display" id="loginTypeDisplay">
                            User Login
                        </div>

                        <i class="bi bi-chevron-down select-arrow"></i>

                        <div class="custom-options" id="loginTypeOptions">

                            <div class="custom-option selected" data-value="user" data-label="User Login">
                                <i class="bi bi-person"></i>
                                <div>
                                    User Login
                                    <small>Organization users and customers</small>
                                </div>
                            </div>

                            <div class="custom-option" data-value="agent" data-label="Agent Login">
                                <i class="bi bi-headset"></i>
                                <div>
                                    Agent Login
                                    <small>Support agents and staff</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mb-3">
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
                            required>
                    </div>
                </div>

                <div class="mb-2">
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
                            required>

                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <a href="<?= BASE_URL ?>/forgot-password" class="text-decoration-none forgot-link">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Login
                </button>

            </form>

        </div>

        <div class="footer-text mt-3">
            <i class="bi bi-shield-check text-success me-1"></i>
            © <?= date('Y'); ?> Samtech Solutions. All rights reserved.
        </div>

    </div>

</div>

<script>
    function togglePassword() {
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

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('loginTypeSelect');
        const display = document.getElementById('loginTypeDisplay');
        const options = document.getElementById('loginTypeOptions');
        const valueInput = document.getElementById('loginTypeValue');
        const optionItems = document.querySelectorAll('.custom-option');

        display.addEventListener('click', function() {
            options.classList.toggle('show');
            display.classList.toggle('active');
        });

        optionItems.forEach(function(item) {
            item.addEventListener('click', function() {
                const value = this.dataset.value;
                const label = this.dataset.label;

                valueInput.value = value;
                display.textContent = label;

                optionItems.forEach(option => option.classList.remove('selected'));
                this.classList.add('selected');

                options.classList.remove('show');
                display.classList.remove('active');
            });
        });

        document.addEventListener('click', function(event) {
            if (!select.contains(event.target)) {
                options.classList.remove('show');
                display.classList.remove('active');
            }
        });
    });
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>