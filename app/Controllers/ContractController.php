<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Contract.php";
require_once ROOT_PATH . "/app/Models/Organization.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";

class ContractController extends Controller
{
    private function contractGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['auth_user_role'] ?? '';

        if (!in_array($role, ['admin', 'agent'])) {
            http_response_code(403);
            require_once ROOT_PATH . "/app/Views/errors/403.php";
            exit;
        }
    }

    private function canCreateContract()
    {
        $role = $_SESSION['auth_user_role'] ?? '';
        $isAdminAgent = !empty($_SESSION['is_admin_agent']);

        return ($role === 'admin' || $isAdminAgent);
    }

    public function index()
    {
        $this->contractGuard();

        $contractModel = new Contract();
        $organizationsWithContracts = $contractModel->getAllWithOrganizations();

        $this->view('contracts/index', [
            'organizationsWithContracts' => $organizationsWithContracts,
            'canCreate' => $this->canCreateContract()
        ]);
    }

    public function create()
    {
        $this->contractGuard();

        if (!$this->canCreateContract()) {
            $_SESSION['error'] = "Access denied. Only Administrators and Admin Agents can create contracts.";
            header("Location: " . BASE_URL . "/contracts");
            exit;
        }

        $organizationModel = new Organization();
        $organizations = $organizationModel->getAllActive();

        $preselectedOrgId = (int)($_GET['organization_id'] ?? 0);

        $this->view('contracts/create', [
            'organizations' => $organizations,
            'preselectedOrgId' => $preselectedOrgId
        ]);
    }

    public function store()
    {
        Csrf::verify();
        $this->contractGuard();

        if (!$this->canCreateContract()) {
            $_SESSION['error'] = "Access denied. Only Administrators and Admin Agents can create contracts.";
            header("Location: " . BASE_URL . "/contracts");
            exit;
        }

        $organizationId = (int)($_POST['organization_id'] ?? 0);
        $contractType = trim($_POST['contract_type'] ?? 'annual');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        if (empty($organizationId) || empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = "Organization, Start Date, and End Date are required.";
            header("Location: " . BASE_URL . "/contracts/create" . ($organizationId ? "?organization_id=" . $organizationId : ""));
            exit;
        }

        if (strtotime($endDate) < strtotime($startDate)) {
            $_SESSION['error'] = "End Date cannot be earlier than Start Date.";
            header("Location: " . BASE_URL . "/contracts/create" . ($organizationId ? "?organization_id=" . $organizationId : ""));
            exit;
        }

        $validTypes = ['annual', 'half_yearly', 'quarterly', 'monthly', 'custom'];
        if (!in_array($contractType, $validTypes)) {
            $contractType = 'annual';
        }

        $typeLabel = ucwords(str_replace('_', ' ', $contractType));
        $contractName = $typeLabel . " Maintenance Contract (" . date('M d, Y', strtotime($startDate)) . " - " . date('M d, Y', strtotime($endDate)) . ")";

        $contractModel = new Contract();
        $createdId = $contractModel->create([
            'organization_id' => $organizationId,
            'contract_name' => $contractName,
            'contract_type' => $contractType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_by' => $_SESSION['auth_user_id'] ?? null
        ]);

        if (!$createdId) {
            $_SESSION['error'] = "Failed to create contract. Please try again.";
            header("Location: " . BASE_URL . "/contracts/create");
            exit;
        }

        $_SESSION['success'] = "Contract '" . htmlspecialchars($contractName) . "' created successfully.";
        header("Location: " . BASE_URL . "/contracts/organization/" . $organizationId . "?contract_id=" . $createdId);
        exit;
    }

    public function organization($id)
    {
        $this->contractGuard();

        $organizationId = (int)$id;
        $organizationModel = new Organization();
        $organization = $organizationModel->findById($organizationId);

        if (!$organization) {
            $_SESSION['error'] = "Organization not found.";
            header("Location: " . BASE_URL . "/contracts");
            exit;
        }

        $contractModel = new Contract();
        $contracts = $contractModel->getByOrganizationId($organizationId);

        if (empty($contracts)) {
            $_SESSION['error'] = "No contracts found for this organization. You can create one below.";
            header("Location: " . BASE_URL . "/contracts/create?organization_id=" . $organizationId);
            exit;
        }

        $selectedContractId = (int)($_GET['contract_id'] ?? 0);
        $selectedContract = null;

        if ($selectedContractId > 0) {
            foreach ($contracts as $c) {
                if ((int)$c['id'] === $selectedContractId) {
                    $selectedContract = $c;
                    break;
                }
            }
        }

        if (!$selectedContract) {
            $selectedContract = $contracts[0];
        }

        $statusInfo = $contractModel->calculateContractStatus($selectedContract);
        $tickets = $contractModel->getTicketsForContractTimeframe(
            $organizationId,
            $selectedContract['start_date'],
            $selectedContract['end_date']
        );

        $this->view('contracts/organization', [
            'organization' => $organization,
            'contracts' => $contracts,
            'selectedContract' => $selectedContract,
            'statusInfo' => $statusInfo,
            'tickets' => $tickets,
            'canCreate' => $this->canCreateContract()
        ]);
    }

    public function printReport($id)
    {
        $this->contractGuard();

        $contractId = (int)$id;
        $contractModel = new Contract();
        $contract = $contractModel->findById($contractId);

        if (!$contract) {
            $_SESSION['error'] = "Contract not found.";
            header("Location: " . BASE_URL . "/contracts");
            exit;
        }

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($contract['organization_id']);

        $statusInfo = $contractModel->calculateContractStatus($contract);

        $tickets = $contractModel->getTicketsForContractTimeframe(
            $contract['organization_id'],
            $contract['start_date'],
            $contract['end_date']
        );

        // Fetch replies for each ticket for detailed report section
        $replyModel = new TicketReply();
        foreach ($tickets as &$ticket) {
            $ticket['replies'] = $replyModel->getByTicketId($ticket['id']);
        }

        $this->view('contracts/print-report', [
            'contract' => $contract,
            'organization' => $organization,
            'statusInfo' => $statusInfo,
            'tickets' => $tickets
        ]);
    }
}
