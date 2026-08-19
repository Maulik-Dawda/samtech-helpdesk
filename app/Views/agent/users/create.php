<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold">
                        Create User
                    </h4>

                </div>

                <div class="card-body p-4">

                    <form method="POST" action="<?= BASE_URL ?>/agent/users/create">

                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">

                            <label>Role</label>

                        </div>

                        <div id="organizationSection">

                            <div class="mb-3">

                                <label>Organization</label>

                                <select
                                    name="organization_id"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Organization
                                    </option>

                                    <?php foreach($organizations as $organization): ?>

                                        <option
                                            value="<?= $organization['id']; ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $organization['name']
                                            ); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="form-check mb-4">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="is_organization_admin"
                                    value="1"
                                >

                                <label class="form-check-label">
                                    Organization Admin
                                </label>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary-custom"
                        >
                            Create User
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function(){

    let orgSection =
        document.getElementById('organizationSection');

    if(this.value === 'user'){
        orgSection.style.display = 'block';
    }else{
        orgSection.style.display = 'none';
    }

});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>