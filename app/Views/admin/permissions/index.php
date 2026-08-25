<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
.card-radius{
    border-radius:18px;
}

.action-link{
    color:#111827;
    border:1px solid #d1d5db;
    background:transparent;
    border-radius:8px;
    padding:6px 12px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.action-link:hover{
    background:#f3f4f6;
    color:#111827;
}
</style>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm card-radius">

        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h4 class="fw-bold mb-1">
                    Permission Management
                </h4>

                <div class="text-muted small">
                    Assign module permissions to users and agents.
                </div>
            </div>

            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                <input
                    type="search"
                    id="permissionsSearchInput"
                    class="form-control ps-5"
                    style="min-width: 250px;"
                    placeholder="Search permissions..."
                    autocomplete="off">
            </div>

        </div>

        <div class="card-body p-4">

            <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if(isset($_SESSION['success'])): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>

            <?php endif; ?>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Organization</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="permissionsTableBody">

                        <?php foreach($users as $user): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user['email']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(ucfirst($user['role'])); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $user['organization_name'] ?? '-'
                                    ); ?>
                                </td>

                                <td>

                                    <a
                                        href="<?= BASE_URL ?>/admin/permissions/edit/<?= $user['id']; ?>"
                                        class="action-link"
                                    >
                                        Manage Permissions
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('permissionsSearchInput');
    const tbody = document.getElementById('permissionsTableBody');
    if (!input || !tbody) return;

    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>