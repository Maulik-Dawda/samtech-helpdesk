<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4">

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white p-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold mb-1">
                        Ticket Reports
                    </h4>

                    <small class="text-muted">
                        Filter and print ticket reports.
                    </small>
                </div>

                <?php if (
                    $_SESSION['auth_user_role'] === 'admin'
                    ||
                    PermissionHelper::has('print_ticket_reports')
                ): ?>

                    <a
                        href="<?= BASE_URL ?>/reports/tickets/print?<?= http_build_query($filters); ?>"
                        target="_blank"
                        id="printReportBtn"
                        class="btn btn-primary-custom">
                        Print Report
                    </a>

                <?php endif; ?>

            </div>

        </div>

        <div class="card-body">

            <form id="ticketReportFilterForm">


                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Organization
                        </label>

                        <select
                            name="organization_id"
                            class="form-select">

                            <option value="">
                                All Organizations
                            </option>

                            <?php foreach ($organizations as $organization): ?>

                                <option
                                    value="<?= $organization['id']; ?>"
                                    <?= $filters['organization_id'] == $organization['id']
                                        ? 'selected'
                                        : ''; ?>>
                                    <?= htmlspecialchars($organization['name']); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            User
                        </label>

                        <select
                            name="user_id"
                            class="form-select">

                            <option value="">
                                All Users
                            </option>

                            <?php foreach ($users as $user): ?>

                                <option
                                    value="<?= $user['id']; ?>"
                                    <?= $filters['user_id'] == $user['id']
                                        ? 'selected'
                                        : ''; ?>>
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Agent
                        </label>

                        <select
                            name="agent_id"
                            class="form-select">

                            <option value="">
                                All Agents
                            </option>

                            <?php foreach ($agents as $agent): ?>

                                <option
                                    value="<?= $agent['id']; ?>"
                                    <?= $filters['agent_id'] == $agent['id']
                                        ? 'selected'
                                        : ''; ?>>
                                    <?= htmlspecialchars($agent['full_name']); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">
                            <option value="">All</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Priority
                        </label>

                        <select
                            name="priority"
                            class="form-select">
                            <option value="">All</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['date_from']); ?>">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['date_to']); ?>">

                    </div>

                    <div class="col-md-3 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary-custom w-100">
                            Apply Filters
                        </button>

                    </div>

                </div>

            </form>

            <hr>

            <div class="table-responsive">

                <div id="ticketReportTable">
                    <?php require "../app/Views/reports/partials/ticket-table.php"; ?>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('ticketReportFilterForm');
        const tableWrapper = document.getElementById('ticketReportTable');
        const printBtn = document.getElementById('printReportBtn');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            tableWrapper.innerHTML = `
            <div class="text-center p-4">
                Loading report...
            </div>
        `;

            fetch('<?= BASE_URL ?>/reports/tickets/filter?' + queryString)
                .then(response => response.text())
                .then(html => {
                    tableWrapper.innerHTML = html;

                    if (printBtn) {
                        printBtn.href =
                            '<?= BASE_URL ?>/reports/tickets/print?' + queryString;
                    }
                })
                .catch(() => {
                    tableWrapper.innerHTML = `
                    <div class="alert alert-danger">
                        Unable to load report data.
                    </div>
                `;
                });
        });

    });
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>