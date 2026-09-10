<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Organization.php";
require_once ROOT_PATH . "/app/Models/OrganizationBranch.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";

class AdminOrganizationController extends Controller
{
    private function staffGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['auth_user_role'] ?? '';

        if (!in_array($role, ['admin', 'agent'])) {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }
    }

    public function index()
    {
        $this->staffGuard();

        $organizationModel = new Organization();
        $organizations = $organizationModel->getAll();

        $this->view('admin/organizations/index', [
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $this->staffGuard();

        $this->view('admin/organizations/create');
    }

    public function store()
    {
        Csrf::verify();
        $this->staffGuard();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $maxUsers = (int)($_POST['max_users'] ?? 3);
        $hasBranches = isset($_POST['has_branches']) ? 1 : 0;
        $branches = is_array($_POST['branches'] ?? null) ? $_POST['branches'] : [];

        if (empty($name)) {
            $_SESSION['error'] = "Organization name is required.";
            header("Location: " . BASE_URL . "/organizations/create");
            exit;
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid organization email.";
            header("Location: " . BASE_URL . "/organizations/create");
            exit;
        }

        if ($maxUsers < 1) {
            $_SESSION['error'] = "Max users must be at least 1.";
            header("Location: " . BASE_URL . "/organizations/create");
            exit;
        }

        $organizationModel = new Organization();

        if ($organizationModel->nameExists($name)) {
            $_SESSION['error'] = "Organization name already exists.";
            header("Location: " . BASE_URL . "/organizations/create");
            exit;
        }

        $orgId = $organizationModel->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'max_users' => $maxUsers,
            'has_branches' => $hasBranches
        ]);

        if (!$orgId) {
            $_SESSION['error'] = "Unable to create organization.";
            header("Location: " . BASE_URL . "/organizations/create");
            exit;
        }

        if ($hasBranches && is_numeric($orgId) && $orgId > 0) {
            $branchModel = new OrganizationBranch();
            $branchModel->syncBranches($orgId, $branches);
        }

        $_SESSION['success'] = "Organization created successfully.";

        header("Location: " . BASE_URL . "/organizations");
        exit;
    }

    public function edit($id)
    {
        $this->staffGuard();

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($id);

        if (!$organization) {
            http_response_code(404);
            echo "Organization not found.";
            exit;
        }

        $branchModel = new OrganizationBranch();
        $branches = $branchModel->getByOrganizationId($id);

        $this->view('admin/organizations/edit', [
            'organization' => $organization,
            'branches' => $branches
        ]);
    }

    public function update($id)
    {
        Csrf::verify();
        $this->staffGuard();

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($id);

        if (!$organization) {
            http_response_code(404);
            echo "Organization not found.";
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $maxUsers = (int)($_POST['max_users'] ?? 3);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $hasBranches = isset($_POST['has_branches']) ? 1 : 0;
        $branches = is_array($_POST['branches'] ?? null) ? $_POST['branches'] : [];

        if (empty($name)) {
            $_SESSION['error'] = "Organization name is required.";
            header("Location: " . BASE_URL . "/organizations/edit/" . $id);
            exit;
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid organization email.";
            header("Location: " . BASE_URL . "/organizations/edit/" . $id);
            exit;
        }

        if ($maxUsers < 1) {
            $_SESSION['error'] = "Max users must be at least 1.";
            header("Location: " . BASE_URL . "/organizations/edit/" . $id);
            exit;
        }

        if ($organizationModel->nameExists($name, $id)) {
            $_SESSION['error'] = "Organization name already exists.";
            header("Location: " . BASE_URL . "/organizations/edit/" . $id);
            exit;
        }

        $updated = $organizationModel->update($id, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'max_users' => $maxUsers,
            'has_branches' => $hasBranches,
            'is_active' => $isActive
        ]);

        if (!$updated) {
            $_SESSION['error'] = "Unable to update organization.";
            header("Location: " . BASE_URL . "/organizations/edit/" . $id);
            exit;
        }

        $branchModel = new OrganizationBranch();
        if ($hasBranches) {
            $branchModel->syncBranches($id, $branches);
        } else {
            $branchModel->syncBranches($id, []);
        }

        $_SESSION['success'] = "Organization updated successfully.";

        header("Location: " . BASE_URL . "/organizations");
        exit;
    }

    public function show($id)
    {
        $this->staffGuard();

        $organizationModel = new Organization();
        $userModel = new User();
        $ticketModel = new Ticket();
        $branchModel = new OrganizationBranch();

        $organization = $organizationModel->findById($id);

        if (!$organization) {
            http_response_code(404);
            echo "Organization not found.";
            exit;
        }

        $users = $userModel->getOrganizationUsers($id);
        $branches = $branchModel->getByOrganizationId($id);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalTickets = $ticketModel->countOrganizationTickets($id);
        $totalPages = (int)ceil($totalTickets / $limit);
        if ($totalPages < 1) {
            $totalPages = 1;
        }

        $tickets = $ticketModel->getOrganizationTicketsPaginated($id, $limit, $offset);

        $this->view('admin/organizations/show', [
            'organization' => $organization,
            'users' => $users,
            'tickets' => $tickets,
            'branches' => $branches,
            'totalTickets' => $totalTickets,
            'page' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit
        ]);
    }

    public function getBranchesJson($id)
    {
        header('Content-Type: application/json');
        $branchModel = new OrganizationBranch();
        $branches = $branchModel->getActiveByOrganizationId($id);
        echo json_encode($branches);
        exit;
    }

    public function disable($id)
    {
        Csrf::verify();
        $this->staffGuard();

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($id);

        if (!$organization) {
            $_SESSION['error'] = "Organization not found.";
            header("Location: " . BASE_URL . "/organizations");
            exit;
        }

        $organizationModel->disableOrganizationAndUsers($id);

        $_SESSION['success'] = "Organization '" . htmlspecialchars($organization['name']) . "' and all its associated users have been disabled. No data was deleted.";

        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . "/organizations");
        header("Location: " . $redirect);
        exit;
    }

    public function enable($id)
    {
        Csrf::verify();
        $this->staffGuard();

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($id);

        if (!$organization) {
            $_SESSION['error'] = "Organization not found.";
            header("Location: " . BASE_URL . "/organizations");
            exit;
        }

        $organizationModel->enableOrganizationAndUsers($id);

        $_SESSION['success'] = "Organization '" . htmlspecialchars($organization['name']) . "' and all its associated users have been re-enabled.";

        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . "/organizations");
        header("Location: " . $redirect);
        exit;
    }
}