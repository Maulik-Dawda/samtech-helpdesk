<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold">
                        Create Organization
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

                    <form method="POST" action="<?= BASE_URL ?>/admin/organizations/create">

                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label>Organization Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-4">
                            <label>Maximum Users</label>
                            <input
                                type="number"
                                name="max_users"
                                class="form-control"
                                value="3"
                                min="1"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary-custom"
                        >
                            Create Organization
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>