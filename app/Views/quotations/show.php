<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<?php
$cover = $proposalData['cover'] ?? [];
$letter = $proposalData['letter'] ?? [];
$about = $proposalData['about'] ?? [];
$custom = $proposalData['custom_pages'] ?? [];
$fin = $proposalData['financials'] ?? [];
$sign = $proposalData['signoff'] ?? [];

$statusBadge = match($quotation['status']) {
    'accepted' => 'bg-success',
    'sent' => 'bg-primary',
    'rejected' => 'bg-danger',
    default => 'bg-secondary'
};
?>

<div class="container-fluid mt-4 mb-5">

    <!-- Header & Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/quotations" class="text-decoration-none">Quotations</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($quotation['quotation_no']); ?></li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <?= htmlspecialchars($quotation['quotation_no']); ?>
                <span class="badge <?= $statusBadge; ?> text-uppercase fs-6"><?= htmlspecialchars($quotation['status']); ?></span>
            </h3>
            <p class="text-muted mb-0 small"><?= htmlspecialchars($quotation['product_name']); ?> for <strong><?= htmlspecialchars($quotation['company_name']); ?></strong></p>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            <a href="<?= BASE_URL ?>/quotations" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
            <a href="<?= BASE_URL ?>/quotations/edit/<?= $quotation['id']; ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i>Edit Quotation
            </a>
            <a href="<?= BASE_URL ?>/quotations/print/<?= $quotation['id']; ?>" target="_blank" class="btn btn-success">
                <i class="bi bi-printer-fill me-1"></i>Print / Download PDF
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Left Column: Summary Card & Details -->
        <div class="col-lg-8">

            <!-- Proposal Overview Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Proposal Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small">Proposal / Product Name</div>
                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($quotation['product_name']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Client Organization</div>
                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($quotation['company_name']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Quotation Date</div>
                            <div class="fw-semibold text-dark"><?= date('d F Y', strtotime($quotation['quotation_date'])); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Validity Date</div>
                            <div class="fw-semibold text-dark"><?= !empty($quotation['validity_date']) ? date('d F Y', strtotime($quotation['validity_date'])) : 'Not specified'; ?></div>
                        </div>
                    </div>

                    <hr>

                    <!-- From & To Parties -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-success mb-2"><i class="bi bi-building me-1"></i>From (Service Provider)</h6>
                                <div class="fw-bold"><?= htmlspecialchars($cover['from_company'] ?? 'SAMVRUDDHI TECHNOLOGIES LLC'); ?></div>
                                <div class="small text-muted">Contact: <?= htmlspecialchars($cover['from_contact_person'] ?? '-'); ?></div>
                                <div class="small text-muted">Phone: <?= htmlspecialchars($cover['from_contact_details'] ?? '-'); ?></div>
                                <div class="small text-muted">Email: <?= htmlspecialchars($cover['from_email'] ?? '-'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-person-badge me-1"></i>To (Client Organization)</h6>
                                <div class="fw-bold"><?= htmlspecialchars($cover['to_company'] ?? $quotation['company_name']); ?></div>
                                <div class="small text-muted">Contact: <?= htmlspecialchars($cover['to_contact_person'] ?? '-'); ?></div>
                                <div class="small text-muted">Phone: <?= htmlspecialchars($cover['to_contact_details'] ?? '-'); ?></div>
                                <div class="small text-muted">Email: <?= htmlspecialchars($cover['to_email'] ?? '-'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financials Summary Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Financials &amp; Commercials</h5>
                    <span class="text-muted small"><?= htmlspecialchars($fin['scope_subtitle'] ?? ''); ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead style="background:#205493; color:#ffffff;">
                                <tr>
                                    <th style="width: 50px;" class="text-center">SN</th>
                                    <th>ITEMS / SCOPE</th>
                                    <th style="width: 100px;" class="text-center">QTY</th>
                                    <th style="width: 160px;" class="text-end">AMOUNT (AED)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($fin['items'])): ?>
                                    <?php foreach ($fin['items'] as $item): ?>
                                        <tr>
                                            <td class="text-center"><?= htmlspecialchars($item['sn'] ?? '1'); ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($item['item'] ?? ''); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($item['qty'] ?? '1'); ?></td>
                                            <td class="text-end fw-bold">AED <?= number_format((float)($item['amount'] ?? 0), 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-semibold">
                                    <td colspan="3" class="text-end">TOTAL AMOUNT:</td>
                                    <td class="text-end">AED <?= number_format((float)($fin['total_amount'] ?? 0), 2); ?></td>
                                </tr>
                                <tr class="fw-semibold">
                                    <td colspan="3" class="text-end">VAT (<?= htmlspecialchars($fin['vat_percent'] ?? 5); ?>%):</td>
                                    <td class="text-end">AED <?= number_format((float)($fin['vat_amount'] ?? 0), 2); ?></td>
                                </tr>
                                <tr class="fw-bold text-success fs-6" style="background:#f0fdf4;">
                                    <td colspan="3" class="text-end">GRAND TOTAL AMOUNT:</td>
                                    <td class="text-end">AED <?= number_format((float)($fin['grand_total'] ?? 0), 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if (!empty($fin['notes']) || !empty($fin['terms'])): ?>
                        <div class="row g-3 mt-2">
                            <?php if (!empty($fin['notes'])): ?>
                                <div class="col-md-6">
                                    <div class="small fw-bold text-secondary mb-1">Notes:</div>
                                    <div class="small text-muted p-2 bg-light rounded border"><?= nl2br(htmlspecialchars($fin['notes'])); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($fin['terms'])): ?>
                                <div class="col-md-6">
                                    <div class="small fw-bold text-secondary mb-1">Terms:</div>
                                    <div class="small text-muted p-2 bg-light rounded border"><?= nl2br(htmlspecialchars($fin['terms'])); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Custom Pages Breakdown -->
            <?php if (!empty($custom)): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-layers text-primary me-2"></i>Custom Middle Pages (<?= count($custom); ?>)</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php foreach ($custom as $cIdx => $cPage): ?>
                            <div class="p-3 mb-3 border rounded-3 bg-light">
                                <h6 class="fw-bold text-primary mb-2">Page <?= $cIdx + 4; ?>: <?= htmlspecialchars($cPage['title'] ?? 'Section'); ?></h6>
                                <div class="small text-muted">
                                    Contains <?= count($cPage['blocks'] ?? []); ?> content block(s) (Headings, paragraphs, tables).
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column: Quick Stats & Signatories -->
        <div class="col-lg-4">

            <!-- Grand Total Card -->
            <div class="card border-0 shadow-sm mb-4 bg-success text-white" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <div class="text-white-50 small text-uppercase fw-semibold mb-1">Quotation Grand Total</div>
                    <h2 class="fw-bold mb-0">AED <?= number_format((float)$quotation['grand_total'], 2); ?></h2>
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>/quotations/print/<?= $quotation['id']; ?>" target="_blank" class="btn btn-light btn-sm text-success fw-bold px-3 py-2">
                            <i class="bi bi-printer-fill me-1"></i>Open Print / PDF Preview
                        </a>
                    </div>
                </div>
            </div>

            <!-- Signatory Details Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pen me-2"></i>Authorized Signatories</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <div class="small fw-bold text-success">Samvruddhi Technologies:</div>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($sign['provider_name'] ?? 'Mr. Vipul Davda'); ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($sign['provider_title'] ?? 'Managing Director'); ?></div>
                    </div>
                    <hr>
                    <div>
                        <div class="small fw-bold text-primary"><?= htmlspecialchars($quotation['company_name']); ?>:</div>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($sign['client_name'] ?? '-'); ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($sign['client_title'] ?? '-'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-3 small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created By:</span>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($quotation['created_by_name'] ?? 'System'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created At:</span>
                        <span class="fw-semibold text-dark"><?= date('d M Y, H:i', strtotime($quotation['created_at'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Last Updated:</span>
                        <span class="fw-semibold text-dark"><?= date('d M Y, H:i', strtotime($quotation['updated_at'])); ?></span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
