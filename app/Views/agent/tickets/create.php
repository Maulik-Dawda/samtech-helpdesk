<?php

require_once ROOT_PATH . "/app/Views/layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$organizations = is_array($organizations ?? null)
    ? $organizations
    : [];

?>

<div class="container-fluid px-0">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <section class="ui-panel mb-4">

        <div class="ui-panel-body">

            <div class="page-header mb-0">

                <div class="page-header-content">

                    <div class="app-badge app-badge-primary mb-3">

                        <i class="bi bi-ticket-perforated-fill"></i>

                        Ticket Management

                    </div>

                    <h1 class="page-title">
                        Create New Ticket
                    </h1>

                    <p class="page-description">
                        Create a support ticket on behalf of an organization and
                        attach supporting files if required.
                    </p>

                </div>

                <div class="page-actions">

                    <a
                        href="<?= BASE_URL ?>/tickets"
                        class="btn btn-light">

                        <i class="bi bi-arrow-left me-2"></i>

                        My Tickets

                    </a>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
         FORM
    ========================================================== -->

    <section class="ui-panel">

        <div class="ui-panel-header">

            <div class="ui-panel-title-wrap">

                <h2 class="ui-panel-title">

                    Ticket Information

                </h2>

                <p class="ui-panel-subtitle">

                    Complete the information below to create a new support ticket.

                </p>

            </div>

        </div>

        <div class="ui-panel-body">

            <?php if (isset($_SESSION['error'])): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($_SESSION['error']); ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>


            <form
                method="POST"
                action="<?= BASE_URL ?>/agent/tickets/store"
                enctype="multipart/form-data"
                class="row g-4">

                <?= Csrf::field(); ?>


                <!-- Organization -->

                <div class="col-md-6">

                    <label class="form-label">

                        Organization

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="organization_id"
                        class="form-select"
                        required>

                        <option value="">
                            Select Organization
                        </option>

                        <?php foreach ($organizations as $organization): ?>

                            <option
                                value="<?= $organization['id']; ?>">

                                <?= htmlspecialchars($organization['name']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Priority -->

                <div class="col-md-6">

                    <label class="form-label">

                        Priority

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="priority"
                        class="form-select"
                        required>

                        <option value="low">

                            Low

                        </option>

                        <option
                            value="medium"
                            selected>

                            Medium

                        </option>

                        <option value="high">

                            High

                        </option>

                        <option value="urgent">

                            Urgent

                        </option>

                    </select>

                    <div class="form-text">

                        Choose the urgency level of this ticket.

                    </div>

                </div>


                <!-- Assign Agent (Do Not Include Admin Agent) -->

                <div class="col-md-6">

                    <label class="form-label">

                        Assign Agent

                    </label>

                    <select
                        name="assigned_agent_id"
                        class="form-select">

                        <option value="">
                            Select Agent (Unassigned)
                        </option>

                        <?php foreach (($agents ?? []) as $agentItem): ?>

                            <option value="<?= $agentItem['id']; ?>">

                                <?= htmlspecialchars($agentItem['full_name']); ?> (<?= htmlspecialchars($agentItem['email']); ?>)

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div class="form-text">

                        Select a support agent to handle this ticket (Admin agents excluded).

                    </div>

                </div>



                <!-- Subject -->

                <div class="col-12">

                    <label class="form-label">

                        Subject

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="subject"
                        class="form-control"
                        maxlength="255"
                        placeholder="Enter ticket subject"
                        required>

                </div>



                <!-- Description -->

                <div class="col-12">

                    <label class="form-label">

                        Description

                        <span class="text-danger">*</span>

                    </label>

                    <textarea
                        name="description"
                        rows="8"
                        class="form-control"
                        placeholder="Describe the issue in detail..."
                        required></textarea>

                </div>



                <!-- Attachments -->

                <div class="col-12">

                    <label class="form-label">

                        Attachments

                    </label>

                    <input
                        type="file"
                        name="attachments[]"
                        class="form-control"
                        multiple
                        accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                    <div class="form-text mt-2">

                        <strong>Supported formats:</strong>

                        JPG, JPEG, PNG, GIF, WEBP,
                        PDF, DOC, DOCX,
                        XLS, XLSX,
                        TXT,
                        ZIP,
                        RAR

                        <br>

                        <strong>Multiple files allowed</strong>,
                        <strong>5 MB</strong> each.

                    </div>

                </div>



                <div class="col-12">

                    <hr>

                </div>



                <div class="col-12 d-flex justify-content-end gap-2">

                    <a
                        href="<?= BASE_URL ?>/tickets"
                        class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Create Ticket

                    </button>

                </div>

            </form>

        </div>

    </section>

</div>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>