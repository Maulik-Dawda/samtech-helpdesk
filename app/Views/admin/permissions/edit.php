<?php require_once "../app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius:18px;">

                <div class="card-header bg-white p-4">

                    <h4 class="fw-bold mb-1">
                        Manage Permissions
                    </h4>

                    <div class="text-muted small">
                        <?= htmlspecialchars($user['full_name']); ?>
                        (<?= htmlspecialchars($user['email']); ?>)
                    </div>

                </div>

                <div class="card-body p-4">

                    <form
                        method="POST"
                        action="<?= BASE_URL ?>/admin/permissions/update/<?= $user['id']; ?>"
                    >

                        <?= Csrf::field(); ?>

                        <?php

                        $currentModule = '';

                        foreach($permissions as $permission):

                            if($currentModule !== $permission['module_name']):

                                if($currentModule !== '') {
                                    echo '</div>';
                                }

                                $currentModule = $permission['module_name'];

                                echo '
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-3">
                                        '.htmlspecialchars($currentModule).'
                                    </h5>
                                ';

                            endif;

                        ?>

                            <div class="form-check mb-2">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="permissions[]"
                                    value="<?= $permission['id']; ?>"

                                    <?= in_array(
                                        $permission['id'],
                                        $selectedPermissionIds
                                    ) ? 'checked' : ''; ?>
                                >

                                <label class="form-check-label">

                                    <?= htmlspecialchars(
                                        $permission['permission_name']
                                    ); ?>

                                </label>

                            </div>

                        <?php endforeach; ?>

                        </div>

                        <div class="d-flex justify-content-between mt-4">

                            <a
                                href="<?= BASE_URL ?>/admin/permissions"
                                class="btn btn-outline-secondary"
                            >
                                Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom"
                            >
                                Save Permissions
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once "../app/Views/layouts/footer.php"; ?>