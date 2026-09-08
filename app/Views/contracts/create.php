<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

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
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/contracts/store">
                        <?= Csrf::field(); ?>

                        <div class="mb-3">
                            <label class="form-label">
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
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

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Start Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="startDate" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                End Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="end_date" id="endDate" class="form-control" value="<?= date('Y-m-d', strtotime('+1 year -1 day')); ?>" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= BASE_URL ?>/contracts" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary-custom">
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

    function autoCalculateEndDate() {
        if (!startDate.value) return;

        const start = new Date(startDate.value);
        if (isNaN(start.getTime())) return;

        const type = typeSelect.value;
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

        if (type !== 'custom') {
            const yyyy = end.getFullYear();
            const mm = String(end.getMonth() + 1).padStart(2, '0');
            const dd = String(end.getDate()).padStart(2, '0');
            endDate.value = `${yyyy}-${mm}-${dd}`;
        }
    }

    typeSelect.addEventListener('change', autoCalculateEndDate);
    startDate.addEventListener('change', autoCalculateEndDate);
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
