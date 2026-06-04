<?php

require_once "../app/Core/Controller.php";

require_once "../app/Models/User.php";
require_once "../app/Models/Organization.php";

class OrganizationUserController extends Controller
{
    private function organizationAdminGuard()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userModel = new User();

        $user = $userModel->findById(
            $_SESSION['auth_user_id']
        );

        if (!$user) {
            http_response_code(403);
            exit('Access denied.');
        }

        if ((int)$user['is_organization_admin'] !== 1) {
            http_response_code(403);
            exit('Organization admin access required.');
        }

        return $user;
    }

    public function index()
    {
        $user = $this->organizationAdminGuard();

        $userModel = new User();

        $users = $userModel->getOrganizationUsers(
            $user['organization_id']
        );

        $this->view('organization-users/index', [
            'organizationUsers' => $users
        ]);
    }

    public function create()
    {
        $user = $this->organizationAdminGuard();

        $organizationModel = new Organization();
        $userModel = new User();

        $maxUsers = $organizationModel->getMaxUsers(
            $user['organization_id']
        );

        $currentUsers = $userModel->countOrganizationUsers(
            $user['organization_id']
        );

        if ($currentUsers >= $maxUsers) {

            $_SESSION['error'] =
                "Maximum organization users limit reached.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users"
            );

            exit;
        }

        $this->view('organization-users/create');
    }

    public function store()
    {
        Csrf::verify();

        $user = $this->organizationAdminGuard();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (
            empty($fullName) ||
            empty($email) ||
            empty($password)
        ) {

            $_SESSION['error'] =
                "All fields are required.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users/create"
            );

            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $_SESSION['error'] =
                "Invalid email address.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users/create"
            );

            exit;
        }

        if (strlen($password) < 8) {

            $_SESSION['error'] =
                "Password must be at least 8 characters.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users/create"
            );

            exit;
        }

        $userModel = new User();

        if ($userModel->emailExists($email)) {

            $_SESSION['error'] =
                "Email already exists.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users/create"
            );

            exit;
        }

        $organizationModel = new Organization();

        $maxUsers = $organizationModel->getMaxUsers(
            $user['organization_id']
        );

        $currentUsers = $userModel->countOrganizationUsers(
            $user['organization_id']
        );

        if ($currentUsers >= $maxUsers) {

            $_SESSION['error'] =
                "Maximum organization users limit reached.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users"
            );

            exit;
        }

        $created = $userModel->createOrganizationUser([
            'organization_id' => $user['organization_id'],
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password
        ]);

        if (!$created) {

            $_SESSION['error'] =
                "Unable to create user.";

            header(
                "Location: " .
                BASE_URL .
                "/organization-users/create"
            );

            exit;
        }

        $_SESSION['success'] =
            "Organization user created successfully.";

        header(
            "Location: " .
            BASE_URL .
            "/organization-users"
        );

        exit;
    }
}