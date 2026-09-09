<?php
$unassignedCount = (int)($unassignedCount ?? 0);
if (!empty($unassignedCount) && $unassignedCount > 0):
    $viewUrl = BASE_URL . '/agent/tickets/all-assigned';
?>
    <div class="alert border border-warning-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #fffbeb; border-color: #fef08a !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1">
                    Attention Required: <?= $unassignedCount; ?> Unassigned Ticket<?= $unassignedCount > 1 ? 's' : ''; ?>
                </h6>
                <p class="text-muted small mb-0">
                    There <?= $unassignedCount === 1 ? 'is' : 'are'; ?> <strong><?= $unassignedCount; ?></strong> ticket<?= $unassignedCount > 1 ? 's' : ''; ?> in the system that <?= $unassignedCount === 1 ? 'has' : 'have'; ?> not been assigned to any support agent yet.
                </p>
            </div>
        </div>
        <div>
            <a href="<?= $viewUrl; ?>" class="btn btn-warning fw-bold text-dark rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" style="background: #f59e0b; border-color: #f59e0b; color: #0f172a !important;">
                <i class="bi bi-people-fill"></i> View All Assigned
            </a>
        </div>
    </div>
<?php endif; ?>
