<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100 border-top border-4" style="max-width: 460px; border-color:#b1e96f !important;">

        <div class="text-center mb-4">
            <h3 class="fw-bold">Admin Portal</h3>
            <p class="text-muted mb-0">Secure Admin Login</p>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if (isset($_SESSION['error'])): ?>
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

        <form method="POST" action="<?= BASE_URL ?>/admin-login">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Admin Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter admin email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Admin Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Login as Admin
            </button>

            <div class="text-end mb-3">
                <a href="<?= BASE_URL ?>/forgot-password" class="text-decoration-none">
                    Forgot Password?
                </a>
            </div>

        </form>

    </div>
</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>