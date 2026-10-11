<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success me-2"></i>Quotations
            </h3>
            <p class="text-muted mb-0 small">Generate, customize, and print client proposals on official letterhead.</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="<?= BASE_URL ?>/quotations/create" class="btn btn-primary-custom d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-2"></i>Create New Quotation
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

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-body p-3">
            <form method="GET" action="<?= BASE_URL ?>/quotations" class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search by Quotation No, Company Name, or Product Name..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <select name="status" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="sent" <?= $status === 'sent' ? 'selected' : ''; ?>>Sent</option>
                        <option value="accepted" <?= $status === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <?php if (!empty($search) || !empty($status)): ?>
                        <a href="<?= BASE_URL ?>/quotations" class="btn btn-outline-secondary" title="Reset Filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Quotations Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius:18px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead class="table-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width: 140px;">Quotation No</th>
                            <th class="py-3">Company Name</th>
                            <th class="py-3">Product / Scope</th>
                            <th class="py-3" style="width: 110px;">Date</th>
                            <th class="py-3 text-end" style="width: 130px;">Grand Total</th>
                            <th class="py-3 text-center" style="width: 100px;">Status</th>
                            <th class="py-3" style="width: 130px;">Created By</th>
                            <th class="pe-4 py-3 text-end" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quotations)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No quotations found. Click <strong>"Create New Quotation"</strong> to generate your first proposal.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quotations as $q): ?>
                                <?php
                                $statusBadge = match($q['status']) {
                                    'accepted' => 'bg-success',
                                    'sent' => 'bg-primary',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        <a href="<?= BASE_URL ?>/quotations/show/<?= $q['id']; ?>" class="text-decoration-none text-dark hover-primary">
                                            <?= htmlspecialchars($q['quotation_no']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($q['company_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($q['product_name']); ?>">
                                            <?= htmlspecialchars($q['product_name']); ?>
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <?= date('d M Y', strtotime($q['quotation_date'])); ?>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        AED <?= number_format((float)$q['grand_total'], 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $statusBadge; ?> text-uppercase" style="font-size: 11px; padding: 5px 8px;">
                                            <?= htmlspecialchars($q['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($q['created_by_name'] ?? 'System'); ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= BASE_URL ?>/quotations/show/<?= $q['id']; ?>" class="btn btn-sm btn-outline-info" title="View Proposal">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/quotations/edit/<?= $q['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit Proposal">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/quotations/print/<?= $q['id']; ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Print / Download PDF">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $q['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade text-start" id="deleteModal<?= $q['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete quotation <strong><?= htmlspecialchars($q['quotation_no']); ?></strong> for <strong><?= htmlspecialchars($q['company_name']); ?></strong>? This action cannot be undone.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="<?= BASE_URL ?>/quotations/delete/<?= $q['id']; ?>" class="d-inline">
                                                            <?= Csrf::field(); ?>
                                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="small text-muted">
                        Showing <?= count($quotations); ?> of <?= $totalQuotations; ?> quotations
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $currentPage == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>/quotations?page=<?= $i; ?>&search=<?= urlencode($search); ?>&status=<?= urlencode($status); ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
