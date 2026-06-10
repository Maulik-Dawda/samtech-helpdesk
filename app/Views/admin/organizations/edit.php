<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold">
                        Edit Organization
                    </h4>

                </div>

                <div class="card-body p-4">

                    <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/admin/organizations/update/<?= $organization['id']; ?>"
                    >

                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label>Organization Name</label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= htmlspecialchars($organization['name']); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($organization['email']); ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= htmlspecialchars($organization['phone']); ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label>Address</label>

                            <textarea
                                name="address"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars($organization['address']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Maximum Users</label>

                            <input
                                type="number"
                                name="max_users"
                                class="form-control"
                                value="<?= htmlspecialchars($organization['max_users']); ?>"
                                min="1"
                                required
                            >
                        </div>

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                value="1"
                                <?= $organization['is_active'] ? 'checked' : ''; ?>
                            >

                            <label class="form-check-label">
                                Active Organization
                            </label>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="<?= BASE_URL ?>/admin/organizations"
                                class="btn btn-outline-secondary"
                            >
                                Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom"
                            >
                                Update Organization
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>