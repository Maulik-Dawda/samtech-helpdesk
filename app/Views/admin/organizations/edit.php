<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$organization = is_array($organization ?? null) ? $organization : [];
$branches = is_array($branches ?? null) ? $branches : [];

$organizationId = (int) ($organization['id'] ?? 0);
$organizationName = $organization['name'] ?? 'Organization';
$hasBranches = !empty($organization['has_branches']) || !empty($branches);

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
                        Edit Organization
                    </h1>
                    <p class="page-description">
                        Update organization information, account status, user limits, and custom branches for
                        <strong><?= htmlspecialchars($organizationName); ?></strong>.
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

    <!-- EDIT ORGANIZATION FORM -->
    <section class="ui-panel">
        <div class="ui-panel-header">
            <div class="ui-panel-title-wrap">
                <h2 class="ui-panel-title">
                    Organization Information
                </h2>
                <p class="ui-panel-subtitle">
                    Review and update the organization details below.
                </p>
            </div>

            <div class="ui-panel-actions">
                <?php if (!empty($organization['is_active'])): ?>
                    <span class="status-badge status-resolved">
                        <i class="bi bi-check-circle me-1"></i> Active
                    </span>
                <?php else: ?>
                    <span class="status-badge status-closed">
                        <i class="bi bi-x-circle me-1"></i> Inactive
                    </span>
                <?php endif; ?>
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
                action="<?= BASE_URL ?>/admin/organizations/update/<?= $organizationId; ?>"
                class="row g-4">

                <?= Csrf::field(); ?>

                <!-- Organization Name -->
                <div class="col-md-6">
                    <label for="organization-name" class="form-label">
                        Organization Name
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="organization-name"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['name'] ?? ''); ?>"
                        placeholder="Enter organization name"
                        maxlength="150"
                        required>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="organization-email" class="form-label">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="organization-email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['email'] ?? ''); ?>"
                        placeholder="organization@example.com"
                        maxlength="190">
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label for="organization-phone" class="form-label">
                        Phone Number
                    </label>
                    <input
                        type="text"
                        id="organization-phone"
                        name="phone"
                        class="form-control"
                        value="<?= htmlspecialchars($organization['phone'] ?? ''); ?>"
                        placeholder="+971 XXXXXXXX"
                        maxlength="30">
                </div>

                <!-- Maximum Users -->
                <div class="col-md-6">
                    <label for="organization-max-users" class="form-label">
                        Maximum Users
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="organization-max-users"
                        name="max_users"
                        class="form-control"
                        value="<?= (int) ($organization['max_users'] ?? 3); ?>"
                        min="1"
                        step="1"
                        required>
                    <div class="form-text">
                        Sets the maximum number of users allowed for this organization.
                    </div>
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label for="organization-address" class="form-label">
                        Address
                    </label>
                    <textarea
                        id="organization-address"
                        name="address"
                        class="form-control"
                        rows="3"
                        placeholder="Enter organization address"
                        maxlength="500"><?= htmlspecialchars($organization['address'] ?? ''); ?></textarea>
                </div>

                <!-- BRANCHES TOGGLE & DYNAMIC SECTION -->
                <div class="col-12">
                    <div class="card border-0 bg-light p-3 rounded-3">
                        <div class="form-check form-switch mb-0">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="has_branches"
                                id="hasBranchesToggle"
                                value="1"
                                <?= $hasBranches ? 'checked' : ''; ?>
                                onchange="toggleBranchesSection()">
                            <label class="form-check-label fw-bold text-dark" for="hasBranchesToggle">
                                <i class="bi bi-diagram-3 me-1 text-primary"></i> Organization Has Branches
                            </label>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Enable this option if the organization operates across multiple branches or locations.
                        </div>

                        <div id="branchesContainerSection" class="mt-3 <?= $hasBranches ? '' : 'd-none'; ?>">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-semibold text-dark mb-0">Organization Branches</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBranchRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Branch
                                </button>
                            </div>

                            <div id="branchRowsList" class="d-flex flex-column gap-2">
                                <?php if (!empty($branches)): ?>
                                    <?php foreach ($branches as $branch): ?>
                                        <div class="input-group input-group-sm branch-row">
                                            <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                                            <input type="text" name="branches[]" class="form-control" placeholder="Branch Name" value="<?= htmlspecialchars($branch['name'] ?? ''); ?>">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeBranchRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="input-group input-group-sm branch-row">
                                        <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                                        <input type="text" name="branches[]" class="form-control" placeholder="e.g. Main Branch, Downtown Office">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeBranchRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Status -->
                <div class="col-12">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            id="organization-status"
                            class="form-check-input"
                            name="is_active"
                            value="1"
                            <?= !empty($organization['is_active']) ? 'checked' : ''; ?>>
                        <label for="organization-status" class="form-check-label fw-semibold">
                            Active Organization
                        </label>
                    </div>
                    <div class="form-text">
                        Inactive organizations may be restricted from accessing helpdesk services.
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-12">
                    <hr>
                </div>

                <div class="col-12 d-flex flex-wrap justify-content-end gap-2">
                    <a
                        href="<?= BASE_URL ?>/admin/organizations"
                        class="btn btn-light">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>
                        Update Organization
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