<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Quotation.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Helpers/PermissionHelper.php";

class QuotationController extends Controller
{
    private function quotationGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!PermissionHelper::has('manage_quotations')) {
            http_response_code(403);
            require_once ROOT_PATH . "/app/Views/errors/403.php";
            exit;
        }
    }

    public function index()
    {
        $this->quotationGuard();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $quotationModel = new Quotation();
        $quotations = $quotationModel->getAll($search, $status, $limit, $offset);
        $totalQuotations = $quotationModel->countAll($search, $status);
        $totalPages = ceil($totalQuotations / $limit);

        $this->view('quotations/index', [
            'quotations' => $quotations,
            'search' => $search,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalQuotations' => $totalQuotations
        ]);
    }

    public function create()
    {
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $suggestedNo = $quotationModel->generateQuotationNo();

        // Default proposal template data matching corporate standard
        $defaultData = [
            'cover' => [
                'quotation_no' => $suggestedNo,
                'product_name' => '',
                'company_name' => '',
                'quotation_date' => date('Y-m-d'),
                'from_company' => 'SAMVRUDDHI TECHNOLOGIES LLC',
                'from_contact_person' => 'Ms. Lenie',
                'from_contact_details' => '+971 4 3554245',
                'from_email' => 'sales@samvruddhi.com',
                'to_company' => '',
                'to_contact_person' => '',
                'to_contact_details' => '',
                'to_email' => ''
            ],
            'letter' => [
                'salutation' => '',
                'body' => "Thank you for considering Samvruddhi Technologies your IT Solution and Service provider.\n\nShould you require anything further details, please feel free to contact us.\n\nLooking forward to receiving your confirmation",
                'closing' => "Best Regards,\nSamvruddhi Technologies"
            ],
            'about' => [
                'heading' => 'About this Proposal',
                'intro' => "This proposal is created by SAMVRUDDHI TECHNOLOGIES LLC based on the information provided during information discovery by way of email provided by [Company Name]",
                'purpose_scope' => "This document details the proposal made for conducting comprehensive solution services specific to customer requirements.\n\nThe terms, including effort, pricing & scope mentioned in this proposal are specific to the details mentioned here-in. Any changes, including additions, alterations or removal of components or locations under scope will affect the proposal terms and will have to be incorporated either by way of generating a new version of the proposal or by appending an addendum to such effect.",
                'confidentiality' => "This document contains sensitive information proprietary and confidential to Samvruddhi Technologies and client. Access to this document should be controlled and the information shall not be disclosed outside without authorization as well as transmitted, or duplicated, used in whole or in part other than its intended purpose.",
                'validity' => "This proposal is valid until end of 30 days from date of issue as long as there is no change in the scope. SAMVRUDDHI has the right to withdraw the terms in this proposal at any time without cause or obligation."
            ],
            'custom_pages' => [],
            'financials' => [
                'scope_subtitle' => 'Scope of Work & Commercials',
                'items' => [
                    [
                        'sn' => '1',
                        'item' => 'Standard Implementation & Service Deployment',
                        'qty' => '1',
                        'amount' => '0.00'
                    ]
                ],
                'total_amount' => 0.00,
                'vat_percent' => 5.0,
                'vat_amount' => 0.00,
                'grand_total' => 0.00,
                'notes' => "1. One revalidation / support review within 3 months will be provided free of cost if customer needs review.\n2. Standard terms and conditions apply.",
                'terms' => "Payment Terms: 100% With LPO.",
                'deliverables' => "The final deliverables, documentation and reports are presented to you in a comprehensive report which not only provides prioritized deliverables but also provides effective recommendations."
            ],
            'signoff' => [
                'report_highlights' => "• Prioritized list of deliverables and documentation provided upon project completion.\n• Summarized and detailed results and analysis report.\n• Recommendations and next steps action plan.",
                'report_delivery' => "1. Comprehensive final report.\n2. Summarized management report.",
                'non_disclosure' => "Both Parties agree to keep the terms and conditions of this agreement confidential and shall not disclose any such terms and conditions to any third party without containing prior written consent; provided, however that this agreement maybe disclosed on a need-to-know basis to both party's attorneys and accountants who agree to be bound by this confidential provision.",
                'provider_name' => 'Mr. Vipul Davda',
                'provider_title' => 'Managing Director',
                'provider_tel' => '04 435 54245',
                'provider_email' => 'vipul@samvruddhi.com',
                'client_name' => '',
                'client_title' => '',
                'client_tel' => '',
                'client_email' => '',
                'closing_message' => 'Thank you for having business with us!'
            ]
        ];

        $this->view('quotations/create', [
            'defaultData' => $defaultData,
            'suggestedNo' => $suggestedNo
        ]);
    }

    public function store()
    {
        Csrf::verify();
        $this->quotationGuard();

        $quotationNo = trim($_POST['quotation_no'] ?? '');
        $productName = trim($_POST['product_name'] ?? '');
        $companyName = trim($_POST['company_name'] ?? '');
        $quotationDate = trim($_POST['quotation_date'] ?? date('Y-m-d'));
        $validityDate = !empty($_POST['validity_date']) ? trim($_POST['validity_date']) : null;
        $status = $_POST['status'] ?? 'draft';

        if (empty($quotationNo) || empty($productName) || empty($companyName)) {
            $_SESSION['error'] = "Quotation Number, Product Name, and Company Name are required.";
            header("Location: " . BASE_URL . "/quotations/create");
            exit;
        }

        $proposalData = $this->buildProposalDataFromPost();
        $grandTotal = (float)($proposalData['financials']['grand_total'] ?? 0.0);

        $quotationModel = new Quotation();

        // Check if quotation number already exists
        $existing = $quotationModel->findByQuotationNo($quotationNo);
        if ($existing) {
            $_SESSION['error'] = "Quotation number '{$quotationNo}' already exists. Please choose a unique number.";
            header("Location: " . BASE_URL . "/quotations/create");
            exit;
        }

        $createdId = $quotationModel->create([
            'quotation_no' => $quotationNo,
            'company_name' => $companyName,
            'product_name' => $productName,
            'quotation_date' => $quotationDate,
            'validity_date' => $validityDate,
            'status' => $status,
            'grand_total' => $grandTotal,
            'proposal_data' => $proposalData,
            'created_by' => $_SESSION['auth_user_id'] ?? null
        ]);

        if (!$createdId) {
            $_SESSION['error'] = "Failed to save quotation. Please try again.";
            header("Location: " . BASE_URL . "/quotations/create");
            exit;
        }

        $_SESSION['success'] = "Quotation '{$quotationNo}' created successfully.";
        header("Location: " . BASE_URL . "/quotations/show/" . $createdId);
        exit;
    }

    public function show($id)
    {
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $quotation = $quotationModel->findById((int)$id);

        if (!$quotation) {
            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;
        }

        $proposalData = json_decode($quotation['proposal_data'], true) ?: [];

        $this->view('quotations/show', [
            'quotation' => $quotation,
            'proposalData' => $proposalData
        ]);
    }

    public function edit($id)
    {
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $quotation = $quotationModel->findById((int)$id);

        if (!$quotation) {
            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;
        }

        $proposalData = json_decode($quotation['proposal_data'], true) ?: [];

        $this->view('quotations/edit', [
            'quotation' => $quotation,
            'proposalData' => $proposalData
        ]);
    }

    public function update($id)
    {
        Csrf::verify();
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $quotation = $quotationModel->findById((int)$id);

        if (!$quotation) {
            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;
        }

        $quotationNo = trim($_POST['quotation_no'] ?? '');
        $productName = trim($_POST['product_name'] ?? '');
        $companyName = trim($_POST['company_name'] ?? '');
        $quotationDate = trim($_POST['quotation_date'] ?? date('Y-m-d'));
        $validityDate = !empty($_POST['validity_date']) ? trim($_POST['validity_date']) : null;
        $status = $_POST['status'] ?? 'draft';

        if (empty($quotationNo) || empty($productName) || empty($companyName)) {
            $_SESSION['error'] = "Quotation Number, Product Name, and Company Name are required.";
            header("Location: " . BASE_URL . "/quotations/edit/" . $id);
            exit;
        }

        // Check if quotation number is changed and conflicts with another record
        $existing = $quotationModel->findByQuotationNo($quotationNo);
        if ($existing && (int)$existing['id'] !== (int)$id) {
            $_SESSION['error'] = "Quotation number '{$quotationNo}' is already in use by another quotation.";
            header("Location: " . BASE_URL . "/quotations/edit/" . $id);
            exit;
        }

        $proposalData = $this->buildProposalDataFromPost();
        $grandTotal = (float)($proposalData['financials']['grand_total'] ?? 0.0);

        $updated = $quotationModel->update((int)$id, [
            'quotation_no' => $quotationNo,
            'company_name' => $companyName,
            'product_name' => $productName,
            'quotation_date' => $quotationDate,
            'validity_date' => $validityDate,
            'status' => $status,
            'grand_total' => $grandTotal,
            'proposal_data' => $proposalData
        ]);

        if (!$updated) {
            $_SESSION['error'] = "Failed to update quotation. Please try again.";
            header("Location: " . BASE_URL . "/quotations/edit/" . $id);
            exit;
        }

        $_SESSION['success'] = "Quotation '{$quotationNo}' updated successfully.";
        header("Location: " . BASE_URL . "/quotations/show/" . $id);
        exit;
    }

    public function delete($id)
    {
        Csrf::verify();
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $quotation = $quotationModel->findById((int)$id);

        if (!$quotation) {
            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;
        }

        $quotationModel->delete((int)$id);

        $_SESSION['success'] = "Quotation '{$quotation['quotation_no']}' deleted successfully.";
        header("Location: " . BASE_URL . "/quotations");
        exit;
    }

    public function printPdf($id)
    {
        $this->quotationGuard();

        $quotationModel = new Quotation();
        $quotation = $quotationModel->findById((int)$id);

        if (!$quotation) {
            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;
        }

        $proposalData = json_decode($quotation['proposal_data'], true) ?: [];

        $this->view('quotations/print', [
            'quotation' => $quotation,
            'proposalData' => $proposalData
        ]);
    }

    private function buildProposalDataFromPost(): array
    {
        // Parse custom pages JSON or array
        $customPages = [];
        if (!empty($_POST['custom_pages_json'])) {
            $decoded = json_decode($_POST['custom_pages_json'], true);
            if (is_array($decoded)) {
                $customPages = $decoded;
            }
        }

        // Financial items
        $financialItems = [];
        $rawItems = $_POST['financial_items'] ?? [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $item) {
                if (!empty($item['item']) || !empty($item['amount'])) {
                    $financialItems[] = [
                        'sn' => trim($item['sn'] ?? ''),
                        'item' => trim($item['item'] ?? ''),
                        'qty' => trim($item['qty'] ?? '1'),
                        'amount' => (float)str_replace(',', '', $item['amount'] ?? 0)
                    ];
                }
            }
        }

        if (empty($financialItems)) {
            $financialItems[] = [
                'sn' => '1',
                'item' => trim($_POST['product_name'] ?? 'Solution Deployment'),
                'qty' => '1',
                'amount' => (float)str_replace(',', '', $_POST['total_amount'] ?? 0)
            ];
        }

        $totalAmount = (float)str_replace(',', '', $_POST['total_amount'] ?? 0);
        $vatPercent = (float)($_POST['vat_percent'] ?? 5.0);
        $vatAmount = round($totalAmount * ($vatPercent / 100), 2);
        $grandTotal = round($totalAmount + $vatAmount, 2);

        return [
            'cover' => [
                'quotation_no' => trim($_POST['quotation_no'] ?? ''),
                'product_name' => trim($_POST['product_name'] ?? ''),
                'company_name' => trim($_POST['company_name'] ?? ''),
                'quotation_date' => trim($_POST['quotation_date'] ?? date('Y-m-d')),
                'validity_date' => trim($_POST['validity_date'] ?? ''),
                'from_company' => trim($_POST['from_company'] ?? 'SAMVRUDDHI TECHNOLOGIES LLC'),
                'from_contact_person' => trim($_POST['from_contact_person'] ?? 'Ms. Lenie'),
                'from_contact_details' => trim($_POST['from_contact_details'] ?? '+971 4 3554245'),
                'from_email' => trim($_POST['from_email'] ?? 'sales@samvruddhi.com'),
                'to_company' => trim($_POST['to_company'] ?? $_POST['company_name'] ?? ''),
                'to_contact_person' => trim($_POST['to_contact_person'] ?? ''),
                'to_contact_details' => trim($_POST['to_contact_details'] ?? ''),
                'to_email' => trim($_POST['to_email'] ?? '')
            ],
            'letter' => [
                'salutation' => trim($_POST['letter_salutation'] ?? 'Dear Sir/Madam,'),
                'body' => trim($_POST['letter_body'] ?? ''),
                'closing' => trim($_POST['letter_closing'] ?? "Best Regards,\nSamvruddhi Technologies")
            ],
            'about' => [
                'heading' => trim($_POST['about_heading'] ?? 'About this Proposal'),
                'intro' => trim($_POST['about_intro'] ?? ''),
                'purpose_scope' => trim($_POST['about_purpose_scope'] ?? ''),
                'confidentiality' => trim($_POST['about_confidentiality'] ?? ''),
                'validity' => trim($_POST['about_validity'] ?? '')
            ],
            'custom_pages' => $customPages,
            'financials' => [
                'scope_subtitle' => trim($_POST['financial_scope_subtitle'] ?? 'Scope of Work & Commercials'),
                'items' => $financialItems,
                'total_amount' => $totalAmount,
                'vat_percent' => $vatPercent,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'notes' => trim($_POST['financial_notes'] ?? ''),
                'terms' => trim($_POST['financial_terms'] ?? 'Payment Terms: 100% With LPO.'),
                'deliverables' => trim($_POST['financial_deliverables'] ?? '')
            ],
            'signoff' => [
                'report_highlights' => trim($_POST['report_highlights'] ?? ''),
                'report_delivery' => trim($_POST['report_delivery'] ?? ''),
                'non_disclosure' => trim($_POST['non_disclosure'] ?? ''),
                'provider_name' => trim($_POST['provider_name'] ?? 'Mr. Vipul Davda'),
                'provider_title' => trim($_POST['provider_title'] ?? 'Managing Director'),
                'provider_tel' => trim($_POST['provider_tel'] ?? '04 435 54245'),
                'provider_email' => trim($_POST['provider_email'] ?? 'vipul@samvruddhi.com'),
                'client_name' => trim($_POST['client_name'] ?? ''),
                'client_title' => trim($_POST['client_title'] ?? ''),
                'client_tel' => trim($_POST['client_tel'] ?? ''),
                'client_email' => trim($_POST['client_email'] ?? ''),
                'closing_message' => trim($_POST['closing_message'] ?? 'Thank you for having business with us!')
            ]
        ];
    }
}
