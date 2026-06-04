<?php require_once "../app/Views/layouts/header.php"; ?>

<div class="container mt-5">
    <div class="card p-4 shadow-sm border-0">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Create New Ticket</h4>

            <a href="<?= BASE_URL ?>/tickets" class="btn btn-sm btn-outline-secondary">
                My Tickets
            </a>
        </div>

        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/tickets/store" enctype="multipart/form-data">
            <?= Csrf::field(); ?>

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input
                    type="text"
                    name="subject"
                    class="form-control"
                    maxlength="255"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                    name="description"
                    class="form-control"
                    rows="6"
                    required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Attachments</label>

                <input
                    type="file"
                    name="attachments[]"
                    class="form-control"
                    multiple
                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                <small class="text-muted">
                    Allowed files: JPG, PNG, PDF, Word, Excel, TXT, ZIP, RAR. Maximum 3 files, 5MB per file.
                </small>
            </div>

            <div class="mb-4">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary-custom px-4">
                Create Ticket
            </button>
        </form>

    </div>
</div>

<?php require_once "../app/Views/layouts/footer.php"; ?>