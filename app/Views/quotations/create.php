<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<div class="container-fluid mt-4 mb-5">

    <!-- Breadcrumb & Title -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/quotations" class="text-decoration-none">Quotations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Quotation</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-plus-fill text-success me-2"></i>Create Quotation Proposal
            </h3>
            <p class="text-muted mb-0 small">Fill out the proposal sections below. You can also add unlimited custom pages, headings, descriptions, and tables.</p>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            <a href="<?= BASE_URL ?>/quotations" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Cancel
            </a>
            <button type="button" class="btn btn-primary-custom" onclick="submitQuotationForm('draft')">
                <i class="bi bi-save me-1"></i>Save as Draft
            </button>
            <button type="button" class="btn btn-success" onclick="submitQuotationForm('sent')">
                <i class="bi bi-check-circle-fill me-1"></i>Save Quotation
            </button>
        </div>
    </div>

    <!-- Error Alerts -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="quotationForm" method="POST" action="<?= BASE_URL ?>/quotations/store">
        <?= Csrf::field(); ?>
        <input type="hidden" name="status" id="quotationStatus" value="draft">
        <input type="hidden" name="custom_pages_json" id="customPagesJson" value="[]">

        <!-- Nav Tabs for Proposal Structure -->
        <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="proposalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="cover-tab" data-bs-toggle="pill" data-bs-target="#coverPage" type="button" role="tab">
                    <i class="bi bi-front me-1"></i>1. Cover Page
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="letter-tab" data-bs-toggle="pill" data-bs-target="#letterPage" type="button" role="tab">
                    <i class="bi bi-envelope-paper me-1"></i>2. Cover Letter
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="about-tab" data-bs-toggle="pill" data-bs-target="#aboutPage" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i>3. About Proposal
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="custom-tab" data-bs-toggle="pill" data-bs-target="#customPages" type="button" role="tab">
                    <i class="bi bi-layers me-1"></i>4. Custom Pages &amp; Tables
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="financial-tab" data-bs-toggle="pill" data-bs-target="#financialPage" type="button" role="tab">
                    <i class="bi bi-cash-stack me-1"></i>5. Financials
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="signoff-tab" data-bs-toggle="pill" data-bs-target="#signoffPage" type="button" role="tab">
                    <i class="bi bi-pen me-1"></i>6. Sign-off &amp; Terms
                </button>
            </li>
        </ul>

        <div class="tab-content" id="proposalTabsContent">

            <!-- TAB 1: COVER PAGE -->
            <div class="tab-pane fade show active" id="coverPage" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Page 1: Proposal Header &amp; Parties</h5>
                        <span class="badge bg-light text-secondary border">Cover Page</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quotation / QTN Number <span class="text-danger">*</span></label>
                                <input type="text" name="quotation_no" class="form-control" value="<?= htmlspecialchars($defaultData['cover']['quotation_no']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quotation Date <span class="text-danger">*</span></label>
                                <input type="date" name="quotation_date" class="form-control" value="<?= htmlspecialchars($defaultData['cover']['quotation_date']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Validity Date</label>
                                <input type="date" name="validity_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Product / Service Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="productNameInput" class="form-control" placeholder="e.g. VULNERABILITY ASSESSMENT AND PENETRATION TESTING - VAPT" required>
                                <small class="text-muted">Will appear as: <em>QUOTATION PROPOSAL FOR [Product Name]</em></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Target / Client Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="companyNameInput" class="form-control" placeholder="e.g. DUBAI AMBULANCE - DCAS" oninput="syncClientCompany(this.value)" required>
                                <small class="text-muted">Will appear as: <em>FOR [Company Name]</em></small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4">
                            <!-- FROM SAMTECH -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-building me-1"></i>From (Service Provider)</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Company Name</label>
                                        <input type="text" name="from_company" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['cover']['from_company']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Contact Person</label>
                                        <input type="text" name="from_contact_person" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['cover']['from_contact_person']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Contact Details (Phone)</label>
                                        <input type="text" name="from_contact_details" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['cover']['from_contact_details']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" name="from_email" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['cover']['from_email']); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- TO CLIENT -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-1"></i>To (Client Organization)</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Client Company Name</label>
                                        <input type="text" name="to_company" id="toCompanyInput" class="form-control form-control-sm" placeholder="e.g. AL ROSTAMANI GROUP">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Contact Person</label>
                                        <input type="text" name="to_contact_person" id="toContactPersonInput" class="form-control form-control-sm" placeholder="e.g. Mr. Mohammed Azam" oninput="syncClientPerson(this.value)">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Contact Details (Phone)</label>
                                        <input type="text" name="to_contact_details" id="toContactDetailsInput" class="form-control form-control-sm" placeholder="e.g. +971 552656109">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" name="to_email" id="toEmailInput" class="form-control form-control-sm" placeholder="e.g. mohammed.azam@alrostamanigroup.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: COVER LETTER -->
            <div class="tab-pane fade" id="letterPage" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-envelope-paper text-primary me-2"></i>Page 2: Cover Letter</h5>
                        <span class="badge bg-light text-secondary border">Transmittal Letter</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Letter Salutation</label>
                            <input type="text" name="letter_salutation" id="letterSalutationInput" class="form-control" placeholder="Dear Azam," value="Dear Client,">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Letter Content / Message</label>
                            <textarea name="letter_body" rows="6" class="form-control"><?= htmlspecialchars($defaultData['letter']['body']); ?></textarea>
                            <small class="text-muted">This message will be formatted on the second page following your standard letterhead.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Closing Sign-off</label>
                            <textarea name="letter_closing" rows="2" class="form-control"><?= htmlspecialchars($defaultData['letter']['closing']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: ABOUT THIS PROPOSAL -->
            <div class="tab-pane fade" id="aboutPage" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Page 3: About this Proposal</h5>
                        <span class="badge bg-light text-secondary border">Standard Scope</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Main Heading</label>
                            <input type="text" name="about_heading" class="form-control fw-bold" value="About this Proposal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Introductory Statement</label>
                            <textarea name="about_intro" id="aboutIntroInput" rows="2" class="form-control"><?= htmlspecialchars($defaultData['about']['intro']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Purpose &amp; Scope</label>
                            <textarea name="about_purpose_scope" rows="5" class="form-control"><?= htmlspecialchars($defaultData['about']['purpose_scope']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confidentiality Clause</label>
                            <textarea name="about_confidentiality" rows="4" class="form-control"><?= htmlspecialchars($defaultData['about']['confidentiality']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Proposal Validity</label>
                            <textarea name="about_validity" rows="2" class="form-control"><?= htmlspecialchars($defaultData['about']['validity']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: CUSTOM DYNAMIC PAGES (Middle Pages with Headings, Descriptions, Tables) -->
            <div class="tab-pane fade" id="customPages" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-layers text-primary me-2"></i>Middle Custom Pages &amp; Tables</h5>
                            <small class="text-muted">Add pages between "About Proposal" and "Financials". Add custom headings, paragraphs, and tables.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addCustomPage()">
                            <i class="bi bi-plus-lg me-1"></i>Add Custom Page
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="customPagesContainer">
                            <!-- Dynamically generated pages will render here -->
                        </div>
                        <div class="text-center p-3 border border-dashed rounded-3 bg-light mt-3" id="noCustomPagesNotice">
                            <i class="bi bi-file-earmark-plus fs-2 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-2">No custom middle pages added yet. (Optional)</p>
                            <button type="button" class="btn btn-sm btn-primary-custom" onclick="addCustomPage()">
                                <i class="bi bi-plus-circle me-1"></i>Add First Custom Page
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: FINANCIALS & COMMERCIALS -->
            <div class="tab-pane fade" id="financialPage" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-primary me-2"></i>Financials &amp; Commercials</h5>
                            <small class="text-muted">Second-to-last page: Pricing table with automatic VAT and totals calculation.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFinancialRow()">
                            <i class="bi bi-plus-circle me-1"></i>Add Pricing Item
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Commercial Scope Subtitle</label>
                            <input type="text" name="financial_scope_subtitle" class="form-control" value="VAPT one time with one revalidation">
                        </div>

                        <!-- Pricing Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle" id="financialsTable">
                                <thead style="background:#205493; color:#ffffff;">
                                    <tr>
                                        <th style="width: 70px;" class="text-center">SN</th>
                                        <th>ITEMS / DESCRIPTION</th>
                                        <th style="width: 140px;" class="text-center">TOTAL NOS</th>
                                        <th style="width: 180px;" class="text-end">TOTAL AMOUNT (AED)</th>
                                        <th style="width: 60px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="financialsTableBody">
                                    <tr>
                                        <td class="text-center"><input type="text" name="financial_items[0][sn]" class="form-control form-control-sm text-center" value="1"></td>
                                        <td><input type="text" name="financial_items[0][item]" class="form-control form-control-sm" value="PUBLIC IP" required></td>
                                        <td><input type="text" name="financial_items[0][qty]" class="form-control form-control-sm text-center" value="40"></td>
                                        <td><input type="number" step="0.01" name="financial_items[0][amount]" class="form-control form-control-sm text-end financial-item-amount" value="18400.00" oninput="calculateFinancialTotals()" required></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeFinancialRow(this)"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><input type="text" name="financial_items[1][sn]" class="form-control form-control-sm text-center" value="2"></td>
                                        <td><input type="text" name="financial_items[1][item]" class="form-control form-control-sm" value="SERVER (PHYSICAL OR VIRTUAL) / NETWORK DEVICES / WIRELESS DEVICES" required></td>
                                        <td><input type="text" name="financial_items[1][qty]" class="form-control form-control-sm text-center" value="107"></td>
                                        <td><input type="number" step="0.01" name="financial_items[1][amount]" class="form-control form-control-sm text-end financial-item-amount" value="13800.00" oninput="calculateFinancialTotals()" required></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeFinancialRow(this)"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><input type="text" name="financial_items[2][sn]" class="form-control form-control-sm text-center" value="3"></td>
                                        <td><input type="text" name="financial_items[2][item]" class="form-control form-control-sm" value="WORK STATION" required></td>
                                        <td><input type="text" name="financial_items[2][qty]" class="form-control form-control-sm text-center" value="450"></td>
                                        <td><input type="number" step="0.01" name="financial_items[2][amount]" class="form-control form-control-sm text-end financial-item-amount" value="42320.00" oninput="calculateFinancialTotals()" required></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeFinancialRow(this)"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold bg-light">
                                        <td colspan="3" class="text-end">TOTAL AMOUNT:</td>
                                        <td class="text-end"><input type="text" name="total_amount" id="subtotalDisplay" class="form-control form-control-sm text-end fw-bold bg-white" readonly value="74,520.00"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold bg-light">
                                        <td colspan="3" class="text-end">
                                            VAT (<input type="number" step="0.1" name="vat_percent" id="vatPercentInput" style="width: 55px; display:inline-block;" class="form-control form-control-sm d-inline-block text-center" value="5" oninput="calculateFinancialTotals()">%):
                                        </td>
                                        <td class="text-end"><input type="text" name="vat_amount" id="vatAmountDisplay" class="form-control form-control-sm text-end fw-bold bg-white" readonly value="3,726.00"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold text-success fs-6" style="background:#f0fdf4;">
                                        <td colspan="3" class="text-end">GRAND TOTAL AMOUNT (AED):</td>
                                        <td class="text-end"><input type="text" name="grand_total" id="grandTotalDisplay" class="form-control form-control-sm text-end fw-bold text-success fs-6 bg-white" readonly value="78,246.00"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Commercial Notes</label>
                                <textarea name="financial_notes" rows="3" class="form-control"><?= htmlspecialchars($defaultData['financials']['notes']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Terms</label>
                                <textarea name="financial_terms" rows="3" class="form-control"><?= htmlspecialchars($defaultData['financials']['terms']); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Deliverables Summary</label>
                                <textarea name="financial_deliverables" rows="3" class="form-control"><?= htmlspecialchars($defaultData['financials']['deliverables']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: SIGNOFF & TERMS -->
            <div class="tab-pane fade" id="signoffPage" role="tabpanel">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pen text-primary me-2"></i>Page 8: Agreement, Deliverables &amp; Sign-off</h5>
                            <small class="text-muted">Final page with Non-Disclosure terms and dual authorized sign-offs.</small>
                        </div>
                        <span class="badge bg-light text-secondary border">Closing Form</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Report Highlights</label>
                                <textarea name="report_highlights" rows="4" class="form-control"><?= htmlspecialchars($defaultData['signoff']['report_highlights']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Report Delivery Formats</label>
                                <textarea name="report_delivery" rows="4" class="form-control"><?= htmlspecialchars($defaultData['signoff']['report_delivery']); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Non-Disclosure Agreement Clause</label>
                                <textarea name="non_disclosure" rows="3" class="form-control"><?= htmlspecialchars($defaultData['signoff']['non_disclosure']); ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-check me-2"></i>Dual Authorized Signatories</h6>
                        <div class="row g-4">
                            <!-- PROVIDER SIGNATORY -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-success mb-2">Samvruddhi Technologies LLC</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Authorized Signatory Name</label>
                                        <input type="text" name="provider_name" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['signoff']['provider_name']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Title / Designation</label>
                                        <input type="text" name="provider_title" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['signoff']['provider_title']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Telephone Number</label>
                                        <input type="text" name="provider_tel" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['signoff']['provider_tel']); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" name="provider_email" class="form-control form-control-sm" value="<?= htmlspecialchars($defaultData['signoff']['provider_email']); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- CLIENT SIGNATORY -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-primary mb-2" id="clientSignatoryHeader">Client Organization</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Authorized Signatory Name</label>
                                        <input type="text" name="client_name" id="clientSignatoryName" class="form-control form-control-sm" placeholder="e.g. Mohammed Azam">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Title / Designation</label>
                                        <input type="text" name="client_title" class="form-control form-control-sm" placeholder="e.g. Procurement Director">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Telephone Number</label>
                                        <input type="text" name="client_tel" id="clientSignatoryTel" class="form-control form-control-sm" placeholder="e.g. +971 552656109">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" name="client_email" id="clientSignatoryEmail" class="form-control form-control-sm" placeholder="e.g. mohammed.azam@alrostamanigroup.com">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Closing Appreciation Message</label>
                                <input type="text" name="closing_message" class="form-control" value="Thank you for having business with us!">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Bar -->
        <div class="card border-0 shadow-sm p-3 bg-white d-flex flex-row justify-content-between align-items-center" style="border-radius:14px; position:sticky; bottom:15px; z-index:100;">
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>Switch tabs to preview and customize all pages of your proposal.
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/quotations" class="btn btn-outline-secondary">Cancel</a>
                <button type="button" class="btn btn-primary-custom" onclick="submitQuotationForm('draft')">
                    <i class="bi bi-save me-1"></i>Save Draft
                </button>
                <button type="button" class="btn btn-success" onclick="submitQuotationForm('sent')">
                    <i class="bi bi-check-circle-fill me-1"></i>Save &amp; Finalize Quotation
                </button>
            </div>
        </div>

    </form>

</div>

<script>
let customPages = [];
let financialRowCount = 3;

function syncClientCompany(name) {
    if (!document.getElementById('toCompanyInput').value || document.getElementById('toCompanyInput').dataset.autoSynced !== 'false') {
        document.getElementById('toCompanyInput').value = name;
        document.getElementById('clientSignatoryHeader').innerText = name || 'Client Organization';
    }
    const introBox = document.getElementById('aboutIntroInput');
    if (introBox) {
        introBox.value = `This proposal is created by SAMVRUDDHI TECHNOLOGIES LLC based on the information provided during information discovery by way of email provided by ${name || '[Company Name]'}`;
    }
}

function syncClientPerson(name) {
    const salutationBox = document.getElementById('letterSalutationInput');
    if (salutationBox) {
        salutationBox.value = `Dear ${name || 'Sir/Madam'},`;
    }
    const clientSignatory = document.getElementById('clientSignatoryName');
    if (clientSignatory && !clientSignatory.value) {
        clientSignatory.value = name;
    }
}

// Financial Calculation
function calculateFinancialTotals() {
    let subtotal = 0;
    document.querySelectorAll('.financial-item-amount').forEach(input => {
        const val = parseFloat(input.value) || 0;
        subtotal += val;
    });

    const vatPercent = parseFloat(document.getElementById('vatPercentInput').value) || 0;
    const vatAmount = subtotal * (vatPercent / 100);
    const grandTotal = subtotal + vatAmount;

    document.getElementById('subtotalDisplay').value = subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('vatAmountDisplay').value = vatAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('grandTotalDisplay').value = grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function addFinancialRow() {
    financialRowCount++;
    const tbody = document.getElementById('financialsTableBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="text-center"><input type="text" name="financial_items[${financialRowCount}][sn]" class="form-control form-control-sm text-center" value="${financialRowCount}"></td>
        <td><input type="text" name="financial_items[${financialRowCount}][item]" class="form-control form-control-sm" placeholder="Item description" required></td>
        <td><input type="text" name="financial_items[${financialRowCount}][qty]" class="form-control form-control-sm text-center" value="1"></td>
        <td><input type="number" step="0.01" name="financial_items[${financialRowCount}][amount]" class="form-control form-control-sm text-end financial-item-amount" value="0.00" oninput="calculateFinancialTotals()" required></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeFinancialRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    calculateFinancialTotals();
}

function removeFinancialRow(btn) {
    const tr = btn.closest('tr');
    if (document.querySelectorAll('#financialsTableBody tr').length > 1) {
        tr.remove();
        calculateFinancialTotals();
    } else {
        alert('Quotation must have at least one pricing item.');
    }
}

// Custom Pages & Tables Builder
function addCustomPage(pageTitle = 'Custom Proposal Section') {
    const pageIndex = customPages.length;
    const pageObj = {
        title: pageTitle,
        blocks: []
    };
    customPages.push(pageObj);
    renderCustomPages();
}

function removeCustomPage(pageIndex) {
    if (confirm('Are you sure you want to remove this custom page?')) {
        customPages.splice(pageIndex, 1);
        renderCustomPages();
    }
}

function addBlockToPage(pageIndex, type) {
    if (type === 'heading') {
        customPages[pageIndex].blocks.push({
            type: 'heading',
            level: 'h3',
            content: 'New Section Heading'
        });
    } else if (type === 'description') {
        customPages[pageIndex].blocks.push({
            type: 'description',
            content: 'Enter description or paragraph text here...'
        });
    } else if (type === 'table') {
        customPages[pageIndex].blocks.push({
            type: 'table',
            columns: ['SL. NO.', 'TOOL NAME', 'DESCRIPTION'],
            rows: [
                ['1.', 'Nessus', 'Nessus vulnerability scanner is an active scanner...'],
                ['2.', 'Nmap', 'Nmap ("Network Mapper") is a free and open source utility...']
            ]
        });
    }
    renderCustomPages();
}

function removeBlockFromPage(pageIndex, blockIndex) {
    customPages[pageIndex].blocks.splice(blockIndex, 1);
    renderCustomPages();
}

function addTableRow(pageIndex, blockIndex) {
    const block = customPages[pageIndex].blocks[blockIndex];
    const newRow = new Array(block.columns.length).fill('');
    newRow[0] = (block.rows.length + 1) + '.';
    block.rows.push(newRow);
    renderCustomPages();
}

function removeTableRow(pageIndex, blockIndex, rowIndex) {
    customPages[pageIndex].blocks[blockIndex].rows.splice(rowIndex, 1);
    renderCustomPages();
}

function addTableColumn(pageIndex, blockIndex) {
    const colName = prompt('Enter new column name:', 'NEW COLUMN');
    if (colName) {
        const block = customPages[pageIndex].blocks[blockIndex];
        block.columns.push(colName);
        block.rows.forEach(r => r.push(''));
        renderCustomPages();
    }
}

function removeTableColumn(pageIndex, blockIndex, colIndex) {
    const block = customPages[pageIndex].blocks[blockIndex];
    if (block.columns.length <= 1) {
        alert('Table must have at least one column.');
        return;
    }
    if (confirm('Delete column "' + block.columns[colIndex] + '"?')) {
        block.columns.splice(colIndex, 1);
        block.rows.forEach(r => r.splice(colIndex, 1));
        renderCustomPages();
    }
}

function renderCustomPages() {
    const container = document.getElementById('customPagesContainer');
    const notice = document.getElementById('noCustomPagesNotice');
    
    if (customPages.length === 0) {
        container.innerHTML = '';
        notice.style.display = 'block';
        return;
    }
    notice.style.display = 'none';

    let html = '';
    customPages.forEach((page, pIdx) => {
        html += `
        <div class="card border border-2 border-primary-subtle shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                    <span class="badge bg-primary">Page ${pIdx + 4}</span>
                    <input type="text" class="form-control form-control-sm fw-bold w-50" value="${escapeHtml(page.title)}" onchange="customPages[${pIdx}].title = this.value">
                </div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBlockToPage(${pIdx}, 'heading')"><i class="bi bi-type-h1 me-1"></i>Add Heading</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addBlockToPage(${pIdx}, 'description')"><i class="bi bi-text-paragraph me-1"></i>Add Text</button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="addBlockToPage(${pIdx}, 'table')"><i class="bi bi-table me-1"></i>Add Table</button>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeCustomPage(${pIdx})" title="Delete Page"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <div class="card-body p-3">
        `;

        if (page.blocks.length === 0) {
            html += `<p class="text-muted small text-center my-3">This page is empty. Use the buttons above to add headings, descriptions, or tables.</p>`;
        } else {
            page.blocks.forEach((block, bIdx) => {
                html += `<div class="p-3 mb-3 border rounded-3 bg-white position-relative">`;
                html += `<button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 m-2 p-0" onclick="removeBlockFromPage(${pIdx}, ${bIdx})" title="Remove Block"><i class="bi bi-x-circle-fill fs-5"></i></button>`;

                if (block.type === 'heading') {
                    html += `
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-type-h1 me-1"></i>Section Heading</label>
                        <input type="text" class="form-control fw-bold" value="${escapeHtml(block.content)}" onchange="customPages[${pIdx}].blocks[${bIdx}].content = this.value">
                    `;
                } else if (block.type === 'description') {
                    html += `
                        <label class="form-label small fw-bold text-secondary"><i class="bi bi-text-paragraph me-1"></i>Description / Paragraph</label>
                        <textarea class="form-control" rows="3" onchange="customPages[${pIdx}].blocks[${bIdx}].content = this.value">${escapeHtml(block.content)}</textarea>
                    `;
                } else if (block.type === 'table') {
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-success mb-0"><i class="bi bi-table me-1"></i>Custom Table</label>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="addTableColumn(${pIdx}, ${bIdx})"><i class="bi bi-plus me-1"></i>Add Column</button>
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="addTableRow(${pIdx}, ${bIdx})"><i class="bi bi-plus me-1"></i>Add Row</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-1">
                                <thead class="table-light">
                                    <tr>
                    `;
                    block.columns.forEach((col, cIdx) => {
                        html += `
                            <th>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="text" class="form-control form-control-sm fw-bold" value="${escapeHtml(col)}" onchange="customPages[${pIdx}].blocks[${bIdx}].columns[${cIdx}] = this.value">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeTableColumn(${pIdx}, ${bIdx}, ${cIdx})" title="Delete Column"><i class="bi bi-dash-circle"></i></button>
                                </div>
                            </th>
                        `;
                    });
                    html += `<th style="width:40px;" class="text-center">Del</th></tr></thead><tbody>`;

                    block.rows.forEach((row, rIdx) => {
                        html += `<tr>`;
                        row.forEach((cell, cIdx) => {
                            html += `<td><input type="text" class="form-control form-control-sm" value="${escapeHtml(cell)}" onchange="customPages[${pIdx}].blocks[${bIdx}].rows[${rIdx}][${cIdx}] = this.value"></td>`;
                        });
                        html += `<td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeTableRow(${pIdx}, ${bIdx}, ${rIdx})"><i class="bi bi-trash"></i></button></td></tr>`;
                    });
                    html += `</tbody></table></div>`;
                }

                html += `</div>`;
            });
        }

        html += `</div></div>`;
    });

    container.innerHTML = html;
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function submitQuotationForm(status) {
    document.getElementById('quotationStatus').value = status;
    document.getElementById('customPagesJson').value = JSON.stringify(customPages);
    document.getElementById('quotationForm').submit();
}

window.addEventListener('DOMContentLoaded', () => {
    calculateFinancialTotals();
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
