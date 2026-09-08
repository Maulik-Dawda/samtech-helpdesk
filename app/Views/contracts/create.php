<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm" style="border-radius: 18px;">
                <div class="card-header bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Create New Contract
                            </h4>
                            <p class="text-muted small mb-0">
                                Set up maintenance contract terms and active dates for an organization.
                            </p>
                        </div>
                        <a href="<?= BASE_URL ?>/contracts" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Contracts
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger mb-4">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/contracts/store">
                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">
                                Organization <span class="text-danger">*</span>
                            </label>
                            <select name="organization_id" id="orgSelect" class="form-select" required>
                                <option value="">Select Organization</option>
                                <?php foreach ($organizations as $org): ?>
                                    <option value="<?= $org['id']; ?>" <?= ($preselectedOrgId === (int)$org['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($org['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">
                                Contract Type <span class="text-danger">*</span>
                            </label>
                            <select name="contract_type" id="contractTypeSelect" class="form-select" required>
                                <option value="annual" selected>Annual Maintenance (12 Months)</option>
                                <option value="half_yearly">Half Yearly (6 Months)</option>
                                <option value="quarterly">Quarterly (3 Months)</option>
                                <option value="monthly">Monthly (1 Month)</option>
                                <option value="custom">Custom Duration</option>
                            </select>
                        </div>

                        <!-- Start Date and End Date in One Line -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-dark small">
                                    Start Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="startDate" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    End Date <span class="text-danger">*</span>
                                    <span id="endDateLockBadge" class="badge bg-light text-muted border ms-1" style="font-weight: normal;">
                                        <i class="bi bi-lock-fill text-secondary"></i> Locked
                                    </span>
                                </label>
                                <input type="date" name="end_date" id="endDate" class="form-control" value="<?= date('Y-m-d', strtotime('+1 year -1 day')); ?>" required readonly style="background-color: #f1f5f9;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="<?= BASE_URL ?>/contracts" class="btn btn-light px-3" style="border-radius: 8px;">Cancel</a>
                            <button type="submit" class="btn btn-primary-custom px-4" style="border-radius: 8px;">
                                <i class="bi bi-check-circle-fill me-1"></i> Create Contract
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('contractTypeSelect');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const lockBadge = document.getElementById('endDateLockBadge');

    function updateEndDateAndLockState() {
        if (!startDate.value) return;

        const start = new Date(startDate.value);
        if (isNaN(start.getTime())) return;

        const type = typeSelect.value;

        if (type === 'custom') {
            // Unlock End Date for custom duration
            endDate.readOnly = false;
            endDate.style.backgroundColor = '#ffffff';
            if (lockBadge) {
                lockBadge.innerHTML = '<i class="bi bi-unlock-fill text-success"></i> Editable';
                lockBadge.className = 'badge bg-success-subtle text-success-emphasis border border-success-subtle ms-1';
            }
        } else {
            // Calculate end date based on type
            let end = new Date(start);
            if (type === 'annual') {
                end.setFullYear(end.getFullYear() + 1);
                end.setDate(end.getDate() - 1);
            } else if (type === 'half_yearly') {
                end.setMonth(end.getMonth() + 6);
                end.setDate(end.getDate() - 1);
            } else if (type === 'quarterly') {
                end.setMonth(end.getMonth() + 3);
                end.setDate(end.getDate() - 1);
            } else if (type === 'monthly') {
                end.setMonth(end.getMonth() + 1);
                end.setDate(end.getDate() - 1);
            }

            const yyyy = end.getFullYear();
            const mm = String(end.getMonth() + 1).padStart(2, '0');
            const dd = String(end.getDate()).padStart(2, '0');
            endDate.value = `${yyyy}-${mm}-${dd}`;

            // Lock End Date
            endDate.readOnly = true;
            endDate.style.backgroundColor = '#f1f5f9';
            if (lockBadge) {
                lockBadge.innerHTML = '<i class="bi bi-lock-fill text-secondary"></i> Locked';
                lockBadge.className = 'badge bg-light text-muted border ms-1';
            }
        }
    }

    typeSelect.addEventListener('change', updateEndDateAndLockState);
    startDate.addEventListener('change', updateEndDateAndLockState);

    // Run on page load
    updateEndDateAndLockState();
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
