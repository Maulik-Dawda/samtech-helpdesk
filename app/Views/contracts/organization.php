<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <!-- Header Navigation & Action Row -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= BASE_URL ?>/contracts" class="text-muted text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Contracts
                </a>
                <span class="text-muted small">/</span>
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($organization['name']); ?></span>
            </div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-building text-primary me-2"></i><?= htmlspecialchars($organization['name']); ?>
            </h2>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Modal Trigger: Generate Contract Report -->
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Generate Contract Report
            </button>

            <?php if ($canCreate): ?>
                <a href="<?= BASE_URL ?>/contracts/create?organization_id=<?= $organization['id']; ?>" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle-fill me-1"></i> Add Contract / Renewal
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Card: Company Details & Contract Filter Dropdown -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <h5 class="fw-bold mb-2">Company Overview</h5>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <div>
                            <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($organization['email'] ?? 'N/A'); ?>
                        </div>
                        <div>
                            <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($organization['phone'] ?? 'N/A'); ?>
                        </div>
                        <div>
                            <i class="bi bi-ticket-perforated me-1"></i><?= count($tickets); ?> Tickets in Contract Period
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label class="form-label fw-bold text-dark small">
                        <i class="bi bi-funnel-fill text-primary me-1"></i>Select Contract / Renewal Filter
                    </label>
                    <select class="form-select form-select-lg shadow-none fs-6" style="border-radius: 10px;" onchange="if(this.value) window.location.href = this.value;">
                        <?php foreach ($contracts as $c): ?>
                            <?php
                            $isSel = ((int)$c['id'] === (int)$selectedContract['id']);
                            $url = BASE_URL . "/contracts/organization/" . $organization['id'] . "?contract_id=" . $c['id'];
                            ?>
                            <option value="<?= $url; ?>" <?= $isSel ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($c['contract_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Expiration Timeline Status Card (Green / Orange / Red) -->
    <div class="card border-0 shadow-sm mb-4 <?= $statusInfo['card_class']; ?>" style="border-radius: 18px;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge <?= $statusInfo['badge_class']; ?> px-3 py-2 fs-6">
                            <i class="bi bi-shield-check me-1"></i> <?= htmlspecialchars($statusInfo['status_label']); ?>
                        </span>
                        <span class="badge bg-light text-dark border">
                            <?= ucwords(str_replace('_', ' ', $selectedContract['contract_type'])); ?>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1">
                        <?= htmlspecialchars($selectedContract['contract_name']); ?>
                    </h3>

                    <p class="mb-0 small opacity-75">
                        <i class="bi bi-calendar-range me-1"></i>
                        <strong>Contract Period:</strong> <?= date('F d, Y', strtotime($selectedContract['start_date'])); ?> &mdash; <?= date('F d, Y', strtotime($selectedContract['end_date'])); ?>
                    </p>
                </div>

                <div class="text-md-end">
                    <?php if ($statusInfo['is_expired']): ?>
                        <div class="fs-4 fw-bold text-danger">Contract Expired</div>
                        <div class="small opacity-75">Please create a renewal contract to continue services.</div>
                    <?php else: ?>
                        <div class="fs-2 fw-bold mb-0">
                            <?= (int)$statusInfo['days_left']; ?> <span class="fs-6 fw-normal">Days Remaining</span>
                        </div>
                        <?php if ($statusInfo['color'] === 'orange'): ?>
                            <div class="small fw-semibold text-warning-emphasis">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Expiring Soon &mdash; Action Required
                            </div>
                        <?php else: ?>
                            <div class="small text-success-emphasis">
                                <i class="bi bi-check-circle-fill me-1"></i> Active Contract Term
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets List Section for Contract Timeframe -->
    <div class="card border-0 shadow-sm" style="border-radius: 18px;">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-ticket-detailed-fill text-primary me-2"></i>Tickets Raised in Contract Period
                </h5>
                <p class="text-muted small mb-0">
                    All support tickets created between <?= date('M d, Y', strtotime($selectedContract['start_date'])); ?> and <?= date('M d, Y', strtotime($selectedContract['end_date'])); ?>.
                </p>
            </div>
            <span class="badge bg-primary rounded-pill fs-6"><?= count($tickets); ?> Tickets</span>
        </div>

        <div class="card-body p-0">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-ticket-perforated text-muted fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">No Tickets Found</h6>
                    <p class="text-muted small">No tickets were raised by <?= htmlspecialchars($organization['name']); ?> within this contract timeframe.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ticket No</th>
                                <th>Subject</th>
                                <th>Customer User</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        #<?= htmlspecialchars($t['ticket_no'] ?? $t['id']); ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($t['subject']); ?>">
                                            <?= htmlspecialchars($t['subject']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($t['customer_name'] ?? 'User'); ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($t['assigned_agent_name'] ?? 'Not Assigned'); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= ucfirst($t['priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info">
                                            <?= ucwords(str_replace('_', ' ', $t['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('M d, Y H:i', strtotime($t['created_at'])); ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>/agent/tickets/show/<?= $t['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="bi bi-box-arrow-up-right"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal: Select Contract to Generate PDF Report -->
<div class="modal fade" id="generateReportModal" tabindex="-1" aria-labelledby="generateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 18px;">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold" id="generateReportModalLabel">
                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Generate Contract Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Select which contract period report you want to print for <strong><?= htmlspecialchars($organization['name']); ?></strong>.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Choose Contract Period</label>
                    <select id="reportContractSelect" class="form-select form-select-lg shadow-none w-100 fs-6" style="border-radius: 10px; padding: 12px 16px;">
                        <?php foreach ($contracts as $c): ?>
                            <option value="<?= $c['id']; ?>" <?= ((int)$c['id'] === (int)$selectedContract['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($c['contract_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pe-4 pb-4">
                <button type="button" class="btn btn-light px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4 fw-semibold" style="border-radius: 8px;" onclick="openPdfReport()">
                    <i class="bi bi-printer-fill me-1"></i> Print / Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openPdfReport() {
    const select = document.getElementById('reportContractSelect');
    const contractId = select ? select.value : '';
    if (contractId) {
        const targetUrl = '<?= BASE_URL ?>/contracts/print-report/' + contractId;
        const newWin = window.open(targetUrl, '_blank');
        if (!newWin || newWin.closed || typeof newWin.closed === 'undefined') {
            window.location.href = targetUrl;
        } else {
            const modalEl = document.getElementById('generateReportModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        }
    }
}
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
