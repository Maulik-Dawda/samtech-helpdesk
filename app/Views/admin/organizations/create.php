<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <section class="ui-panel mb-4">
        <div class="ui-panel-body">
            <div class="page-header mb-0">
                <div class="page-header-content">
                    <div class="app-badge app-badge-primary mb-3">
                        <i class="bi bi-buildings"></i> Organization Management
                    </div>
                    <h1 class="page-title">
                        Create Organization
                    </h1>
                    <p class="page-description">
                        Register a new customer organization that can have users,
                        tickets, organization administrators, and custom branches.
                    </p>
                </div>
                <div class="page-actions">
                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Organizations
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FORM -->
    <section class="ui-panel">
        <div class="ui-panel-header">
            <div class="ui-panel-title-wrap">
                <h2 class="ui-panel-title">
                    Organization Information
                </h2>
                <p class="ui-panel-subtitle">
                    Fill in the required information below.
                </p>
            </div>
        </div>

        <div class="ui-panel-body">

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['error']); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/admin/organizations/create"
                class="row g-4">

                <?= Csrf::field(); ?>

                <div class="col-md-6">
                    <label class="form-label">
                        Organization Name
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter organization name"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="organization@example.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Phone Number
                    </label>
                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        placeholder="+971 XXXXXXXX">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Maximum Users
                    </label>
                    <input
                        type="number"
                        name="max_users"
                        class="form-control"
                        value="3"
                        min="1"
                        required>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Address
                    </label>
                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                        placeholder="Enter organization address"></textarea>
                </div>

                <!-- BRANCHES CHECKBOX & DYNAMIC SECTION -->
                <div class="col-12">
                    <div class="card border-0 bg-light p-3 rounded-3">
                        <div class="form-check form-switch mb-0">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="has_branches"
                                id="hasBranchesToggle"
                                value="1"
                                onchange="toggleBranchesSection()">
                            <label class="form-check-label fw-bold text-dark" for="hasBranchesToggle">
                                <i class="bi bi-diagram-3 me-1 text-primary"></i> Organization Has Branches
                            </label>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Enable this option if the organization operates across multiple branches or locations.
                        </div>

                        <div id="branchesContainerSection" class="mt-3 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-semibold text-dark mb-0">Organization Branches</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBranchRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Branch
                                </button>
                            </div>

                            <div id="branchRowsList" class="d-flex flex-column gap-2">
                                <div class="input-group input-group-sm branch-row">
                                    <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                                    <input type="text" name="branches[]" class="form-control" placeholder="e.g. Main Branch, Downtown Office">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeBranchRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <hr>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">
                        <i class="bi bi-building-add me-2"></i>
                        Create Organization
                    </button>
                </div>

            </form>

        </div>
    </section>

</div>

<script>
function toggleBranchesSection() {
    const toggle = document.getElementById('hasBranchesToggle');
    const section = document.getElementById('branchesContainerSection');
    if (!toggle || !section) return;

    if (toggle.checked) {
        section.classList.remove('d-none');
    } else {
        section.classList.add('d-none');
    }
}

function addBranchRow(initialValue = '') {
    const container = document.getElementById('branchRowsList');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'input-group input-group-sm branch-row';
    div.innerHTML = `
        <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
        <input type="text" name="branches[]" class="form-control" placeholder="e.g. Branch Name" value="${initialValue}">
        <button type="button" class="btn btn-outline-danger" onclick="removeBranchRow(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeBranchRow(btn) {
    const container = document.getElementById('branchRowsList');
    const row = btn.closest('.branch-row');
    if (row) {
        if (container.querySelectorAll('.branch-row').length > 1) {
            row.remove();
        } else {
            row.querySelector('input').value = '';
        }
    }
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>