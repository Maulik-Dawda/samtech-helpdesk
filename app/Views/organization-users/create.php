<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold mb-1">
                        Add Organization User
                    </h4>

                    <div class="text-muted small">
                        Create a user under your organization.
                    </div>

                </div>

                <div class="card-body p-4">

                    <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/organization-users/create"
                    >

                        <?= Csrf::field(); ?>

                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                            <small class="text-muted">
                                Minimum 8 characters.
                            </small>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="<?= BASE_URL ?>/organization-users"
                                class="btn btn-outline-secondary"
                            >
                                Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom"
                            >
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