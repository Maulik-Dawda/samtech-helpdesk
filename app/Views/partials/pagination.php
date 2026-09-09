<?php
$queryParams = $_GET;
unset($queryParams['page']);

$baseQuery = http_build_query($queryParams);

if (!function_exists('paginationUrl')) {
    function paginationUrl($pageNumber, $baseQuery)
    {
        $query = $baseQuery ? $baseQuery . '&page=' . $pageNumber : 'page=' . $pageNumber;
        return '?' . $query;
    }
}
?>

<?php if ($totalPages > 1): ?>

    <div class="pagination-wrapper border-top-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="pagination-info text-muted small">
            Showing Page <strong><?= $page; ?></strong> of <strong><?= $totalPages; ?></strong>
            <?php if (!empty($totalRecords)): ?>
                <span class="ms-1">(<?= number_format($totalRecords); ?> total entries)</span>
            <?php endif; ?>
        </div>

        <ul class="pagination mb-0">

            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page > 1 ? paginationUrl($page - 1, $baseQuery) : '#'; ?>" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            ?>

            <?php if ($start > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= paginationUrl(1, $baseQuery); ?>">1</a>
                </li>

                <?php if ($start > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?= paginationUrl($i, $baseQuery); ?>">
                        <?= $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>

                <li class="page-item">
                    <a class="page-link" href="<?= paginationUrl($totalPages, $baseQuery); ?>">
                        <?= $totalPages; ?>
                    </a>
                </li>
            <?php endif; ?>

            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page < $totalPages ? paginationUrl($page + 1, $baseQuery) : '#'; ?>" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>

        </ul>
    </div>

<?php endif; ?>