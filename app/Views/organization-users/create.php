<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid py-4">

    <div class="row justify-content-center">

        <div class="col-xl-7 col-lg-8">

            <div class="d-flex align-items-center mb-4">

                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width:60px;height:60px;background:#eef9de;">

                    <i class="bi bi-person-plus-fill fs-3"
                        style="color:#7db343;"></i>

                </div>

                <div>

                    <h3 class="fw-bold mb-1">
                        Add Organization User
                    </h3>

                    <p class="text-muted mb-0">
                        Create a new user under your organization.
                    </p>

                </div>

            </div>

            <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if (!empty($_SESSION['error'])): ?>

                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <?= htmlspecialchars($_SESSION['error']) ?>

                    <button class="btn-close"
                        data-bs-dismiss="alert"></button>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <div class="card border-0 shadow-sm"
                style="border-radius:20px;">

                <div class="card-body p-5">

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/organization-users/create">

                        <?= Csrf::field(); ?>

                        <div class="form-floating mb-4">

                            <input
                                type="text"
                                class="form-control"
                                id="full_name"
                                name="full_name"
                                placeholder="Full Name"
                                required>

                            <label for="full_name">
                                <i class="bi bi-person me-2"></i>
                                Full Name
                            </label>

                        </div>

                        <div class="form-floating mb-4">

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Email Address"
                                required>

                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>
                                Email Address
                            </label>

                        </div>

                        <div class="form-floating mb-3">

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Password"
                                required>

                            <label for="password">
                                <i class="bi bi-lock me-2"></i>
                                Password
                            </label>

                        </div>

                        <div class="alert border-0"
                            style="background:#f8fafc;border-radius:14px;">

                            <i class="bi bi-shield-lock me-2 text-success"></i>

                            Password must contain at least
                            <strong>8 characters</strong>,
                            uppercase, lowercase,
                            number and special character.

                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">

                            <a
                                href="<?= BASE_URL ?>/organization-users"
                                class="btn btn-light px-4">

                                <i class="bi bi-arrow-left me-2"></i>

                                Back

                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom px-4">

                                <i class="bi bi-person-check-fill me-2"></i>

                                Create User

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>