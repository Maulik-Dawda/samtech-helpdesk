<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Organization.php";

class AdminUserController extends Controller
{
    private function adminGuard()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('admin');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $this->adminGuard();

        $userModel = new User();

        $users = $userModel->getAllUsersForAdmin();

        $this->view('admin/users/index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $this->adminGuard();

        $organizationModel = new Organization();

        $organizations = $organizationModel->getAllActive();

        $this->view('admin/users/create', [
            'organizations' => $organizations
        ]);
    }

    public function store()
    {
        Csrf::verify();

        $this->adminGuard();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? '';
        $organizationId = $_POST['organization_id'] ?? null;
        $isOrganizationAdmin = isset($_POST['is_organization_admin']) ? 1 : 0;

        $allowedRoles = [
            'admin',
            'agent',
            'user'
        ];

        if (
            empty($fullName) ||
            empty($email) ||
            empty($password) ||
            empty($role)
        ) {
            $_SESSION['error'] = "All required fields are mandatory.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        if (!in_array($role, $allowedRoles)) {
            $_SESSION['error'] = "Invalid role selected.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        if ($role === 'admin') {
            $organizationId = null;
            $isOrganizationAdmin = 0;
        }

        if ($role === 'agent') {
            $organizationId = null;
            $isOrganizationAdmin = 0;
        }

        if ($role === 'user' && empty($organizationId)) {
            $_SESSION['error'] = "Organization is required for user accounts.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        $userModel = new User();

        if ($userModel->emailExists($email)) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        $created = $userModel->createByAdmin([
            'organization_id' => $organizationId,
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_organization_admin' => $isOrganizationAdmin
        ]);

        if (!$created) {
            $_SESSION['error'] = "Unable to create user.";
            header("Location: " . BASE_URL . "/admin/users/create");
            exit;
        }

        $_SESSION['success'] = "User created successfully.";

        header("Location: " . BASE_URL . "/admin/users");
        exit;
    }

    public function edit($id)
    {
        $this->adminGuard();

        $userModel = new User();
        $organizationModel = new Organization();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] === 'admin') {
            http_response_code(403);
            echo "Admin accounts cannot be edited.";
            exit;
        }

        $organizations = $organizationModel->getAllActive();

        $this->view('admin/users/edit', [
            'user' => $user,
            'organizations' => $organizations
        ]);
    }

    public function update($id)
    {
        Csrf::verify();

        $this->adminGuard();

        $userModel = new User();

        $existingUser = $userModel->findById($id);

        if (!$existingUser) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($existingUser['role'] === 'admin') {
            http_response_code(403);
            echo "Admin accounts cannot be updated.";
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $organizationId = $_POST['organization_id'] ?? null;
        $isOrganizationAdmin = isset($_POST['is_organization_admin']) ? 1 : 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $allowedRoles = [
            'agent',
            'user'
        ];

        if (
            empty($fullName) ||
            empty($email) ||
            empty($role)
        ) {
            $_SESSION['error'] = "All required fields are mandatory.";
            header("Location: " . BASE_URL . "/admin/users/edit/" . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address.";
            header("Location: " . BASE_URL . "/admin/users/edit/" . $id);
            exit;
        }

        if (!in_array($role, $allowedRoles)) {
            $_SESSION['error'] = "Invalid role selected.";
            header("Location: " . BASE_URL . "/admin/users/edit/" . $id);
            exit;
        }

        if ($role === 'agent') {
            $organizationId = null;
            $isOrganizationAdmin = 0;
        }

        if ($role === 'user' && empty($organizationId)) {
            $_SESSION['error'] = "Organization is required for user accounts.";
            header("Location: " . BASE_URL . "/admin/users/edit/" . $id);
            exit;
        }

        $updated = $userModel->updateUserByAdmin($id, [
            'organization_id' => $organizationId,
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role,
            'is_organization_admin' => $isOrganizationAdmin,
            'is_active' => $isActive
        ]);

        if (!$updated) {
            $_SESSION['error'] = "Unable to update user.";
            header("Location: " . BASE_URL . "/admin/users/edit/" . $id);
            exit;
        }

        $_SESSION['success'] = "User updated successfully.";

        header("Location: " . BASE_URL . "/admin/users");
        exit;
    }

    public function disable($id)
    {
        $this->adminGuard();

        $userModel = new User();

        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($user['role'] === 'admin') {
            http_response_code(403);
            echo "Admin accounts cannot be disabled.";
            exit;
        }

        $userModel->disableUserByAdmin($id);

        $_SESSION['success'] = "User disabled successfully.";

        header("Location: " . BASE_URL . "/admin/users");
        exit;
    }
}