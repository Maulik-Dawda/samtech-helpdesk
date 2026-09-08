<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <!-- Header & Action Row -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-file-text-fill text-primary me-2"></i>Organization Contracts
            </h2>
            <p class="text-muted small mb-0">
                Manage organization maintenance contracts, renewals, and timeframe ticket reports.
            </p>
        </div>

        <?php if ($canCreate): ?>
            <div>
                <a href="<?= BASE_URL ?>/contracts/create" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle-fill me-1"></i> Create Contract
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($organizationsWithContracts)): ?>

        <!-- Empty State -->
        <div class="card border-0 shadow-sm text-center p-5" style="border-radius: 18px;">
            <div class="py-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-text text-muted fs-1"></i>
                </div>
                <h4 class="fw-bold mb-2">No Contracts Created Yet</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
                    There are no contracts recorded in the system. Create a contract for an organization to manage maintenance periods and track tickets.
                </p>

                <?php if ($canCreate): ?>
                    <a href="<?= BASE_URL ?>/contracts/create" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add First Contract
                    </a>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        <!-- Organizations Contracts List -->
        <div class="card border-0 shadow-sm" style="border-radius: 18px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Organization</th>
                                <th>Latest Contract</th>
                                <th>Type</th>
                                <th>Contract Dates</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($organizationsWithContracts as $item): ?>
                                <?php
                                $latest = $item['latest_contract'];
                                $statusInfo = $item['status_info'];
                                $orgId = $item['organization_id'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <a href="<?= BASE_URL ?>/contracts/organization/<?= $orgId; ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                            <i class="bi bi-building me-2 text-primary"></i>
                                            <?= htmlspecialchars($item['organization_name']); ?>
                                        </a>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars($item['organization_email'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($latest['contract_name'] ?? 'Contract'); ?>
                                        </div>
                                        <div class="small text-muted">
                                            Total Contracts: <?= (int)$item['total_contracts']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= ucwords(str_replace('_', ' ', $latest['contract_type'] ?? 'annual')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-medium">
                                            <?= !empty($latest['start_date']) ? date('M d, Y', strtotime($latest['start_date'])) : '-'; ?>
                                            &rarr;
                                            <?= !empty($latest['end_date']) ? date('M d, Y', strtotime($latest['end_date'])) : '-'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($statusInfo): ?>
                                            <span class="badge <?= $statusInfo['badge_class']; ?>">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                <?= htmlspecialchars($statusInfo['status_label']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Contract</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>/contracts/organization/<?= $orgId; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-folder2-open me-1"></i> View Contracts &amp; Tickets
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
