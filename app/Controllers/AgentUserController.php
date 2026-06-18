<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Organization.php";

class AgentUserController extends Controller
{
    private function agentGuard()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('agent');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $this->agentGuard();

        $userModel = new User();

        $users = $userModel->getAllUsersForAgent();

        $this->view('agent/users/index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $this->agentGuard();

        $organizationModel = new Organization();

        $organizations = $organizationModel->getAllActive();

        $this->view('agent/users/create', [
            'organizations' => $organizations
        ]);
    }

    public function store()
    {
        Csrf::verify();

        $this->agentGuard();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $organizationId = $_POST['organization_id'] ?? null;
        $isOrganizationAdmin = isset($_POST['is_organization_admin']) ? 1 : 0;

        if (
            empty($fullName) ||
            empty($email) ||
            empty($password) ||
            empty($organizationId)
        ) {
            $_SESSION['error'] = "Full name, email, password and organization are required.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($organizationId);

        if (!$organization || (int)$organization['is_active'] !== 1) {
            $_SESSION['error'] = "Invalid or inactive organization selected.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        $userModel = new User();

        if ($userModel->emailExists($email)) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        $created = $userModel->createByAgent([
            'organization_id' => $organizationId,
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'is_organization_admin' => $isOrganizationAdmin
        ]);

        if (!$created) {
            $_SESSION['error'] = "Unable to create user.";
            header("Location: " . BASE_URL . "/agent/users/create");
            exit;
        }

        $_SESSION['success'] = "User created successfully.";

        header("Location: " . BASE_URL . "/agent/users");
        exit;
    }

    public function edit($id)
    {
        $this->agentGuard();

        $userModel = new User();
        $organizationModel = new Organization();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] !== 'user') {
            http_response_code(403);
            echo "Only user accounts can be edited by agents.";
            exit;
        }

        $organizations = $organizationModel->getAllActive();

        $this->view('agent/users/edit', [
            'user' => $user,
            'organizations' => $organizations
        ]);
    }

    public function update($id)
    {
        Csrf::verify();

        $this->agentGuard();

        $userModel = new User();

        $existingUser = $userModel->findById($id);

        if (!$existingUser) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($existingUser['role'] !== 'user') {
            http_response_code(403);
            echo "Only user accounts can be updated by agents.";
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $organizationId = $_POST['organization_id'] ?? null;
        $isOrganizationAdmin = isset($_POST['is_organization_admin']) ? 1 : 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (
            empty($fullName) ||
            empty($email) ||
            empty($organizationId)
        ) {
            $_SESSION['error'] = "Full name, email and organization are required.";
            header("Location: " . BASE_URL . "/agent/users/edit/" . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address.";
            header("Location: " . BASE_URL . "/agent/users/edit/" . $id);
            exit;
        }

        $organizationModel = new Organization();
        $organization = $organizationModel->findById($organizationId);

        if (!$organization || (int)$organization['is_active'] !== 1) {
            $_SESSION['error'] = "Invalid or inactive organization selected.";
            header("Location: " . BASE_URL . "/agent/users/edit/" . $id);
            exit;
        }

        if (
            strtolower($email) !== strtolower($existingUser['email']) &&
            $userModel->emailExists($email)
        ) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: " . BASE_URL . "/agent/users/edit/" . $id);
            exit;
        }

        $updated = $userModel->updateUserByAgent($id, [
            'organization_id' => $organizationId,
            'full_name' => $fullName,
            'email' => $email,
            'is_organization_admin' => $isOrganizationAdmin,
            'is_active' => $isActive
        ]);

        if (!$updated) {
            $_SESSION['error'] = "Unable to update user.";
            header("Location: " . BASE_URL . "/agent/users/edit/" . $id);
            exit;
        }

        $_SESSION['success'] = "User updated successfully.";

        header("Location: " . BASE_URL . "/agent/users");
        exit;
    }

    public function disable($id)
    {
        $this->agentGuard();

        $userModel = new User();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] !== 'user') {
            http_response_code(403);
            echo "Only user accounts can be disabled by agents.";
            exit;
        }

        $disabled = $userModel->disableUserByAgent($id);

        if (!$disabled) {
            $_SESSION['error'] = "Unable to disable user.";
            header("Location: " . BASE_URL . "/agent/users");
            exit;
        }

        $_SESSION['success'] = "User disabled successfully.";

        header("Location: " . BASE_URL . "/agent/users");
        exit;
    }
}