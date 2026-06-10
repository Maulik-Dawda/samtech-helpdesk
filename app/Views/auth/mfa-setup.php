<?php require_once ROOT_PATH . "/app/Views/layouts/auth-header.php"; ?>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card w-100" style="max-width: 520px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold mb-2">Set Up Authenticator</h3>
            <p class="text-muted mb-0">
                Use Microsoft Authenticator to secure your account.
            </p>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <strong>Setup Instructions</strong>

            <ol class="mb-0 mt-2 ps-3">
                <li>Open Microsoft Authenticator on your phone.</li>
                <li>Tap <strong>+</strong> to add an account.</li>
                <li>Select <strong>Other account</strong>.</li>
                <li>Choose <strong>Enter setup key manually</strong>.</li>
                <li>Enter the setup key shown below.</li>
                <li>Enter the generated 6-digit code below.</li>
            </ol>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Setup Key</label>

            <?php
            $formattedSecret = trim(chunk_split($secret, 4, ' '));
            ?>

            <div class="p-3 border rounded bg-light text-center">
                <div class="fw-bold fs-5 text-break" style="letter-spacing: 2px;">
                    <?= htmlspecialchars($formattedSecret); ?>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-sm btn-outline-secondary w-100 mt-2"
                onclick="navigator.clipboard.writeText('<?= htmlspecialchars($secret); ?>')">
                Copy Setup Key
            </button>

            <p class="small text-muted mt-2 mb-0">
                Copy the key and paste it directly in Microsoft Authenticator to avoid mistakes.
            </p>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/mfa-setup">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Enter 6-digit Code</label>
                <input
                    type="text"
                    name="code"
                    class="form-control text-center"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    placeholder="123456"
                    required
                    autofocus>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Verify & Enable MFA
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/logout" class="text-decoration-none text-muted small">
                Cancel and logout
            </a>
        </div>

    </div>
</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/auth-footer.php"; ?>