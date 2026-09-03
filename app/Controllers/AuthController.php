<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/LoginAttempt.php";
require_once ROOT_PATH . "/app/Models/AuthenticatorSecret.php";
require_once ROOT_PATH . "/app/Models/LoginOtp.php";
require_once ROOT_PATH . "/app/Services/MailService.php";
require_once ROOT_PATH . "/app/Models/PasswordResetOtp.php";
require_once ROOT_PATH . "/app/Models/ActivityLog.php";

class AuthController extends Controller
{
    public function userLogin()
    {
        AuthMiddleware::guest();
        $this->view('auth/user-login');
    }

    public function adminLogin()
    {
        AuthMiddleware::guest();
        $this->view('auth/admin-login');
    }

    public function processUserLogin()
    {
        Csrf::verify();
        $this->startSession();

        $loginType = $_POST['login_type'] ?? 'user';
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->validateCredentialsInput($email, $password)) {
            $this->redirectWithError('/user-login', 'Please enter a valid email and password.');
        }

        if ($this->isTooManyAttempts($email)) {
            $this->redirectWithError('/user-login', 'Account temporarily locked due to too many failed attempts. Please try again after 15 minutes.');
        }

        if (!in_array($loginType, ['user', 'agent'])) {
            $this->recordLoginAttempt($email, false);
            $this->redirectWithError('/user-login', 'Invalid login type selected.');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $this->recordLoginAttempt($email, false);
            $this->redirectWithError('/user-login', 'Invalid email, password, or login type.');
        }

        if ($loginType === 'user' && $user['role'] !== 'user') {
            $this->recordLoginAttempt($email, false);
            $this->redirectWithError('/user-login', 'Invalid email, password, or login type.');
        }

        if ($loginType === 'agent' && $user['role'] !== 'agent') {
            $this->recordLoginAttempt($email, false);
            $this->redirectWithError('/user-login', 'Invalid email, password, or login type.');
        }

        if (!$this->isValidUser($user, $password)) {
            $this->recordLoginAttempt($email, false);
            $this->logActivity($user['id'], 'Failed login attempt');
            $this->redirectWithError('/user-login', 'Invalid email, password, or login type.');
        }

        $this->recordLoginAttempt($email, true);
        $this->logActivity($user['id'], 'Password verified for login');

        if ($user['role'] === 'agent' || !empty($user['login_type_mfa'])) {
            $this->redirectToMfa($user);
        }

        $this->sendUserLoginOtp($user);

        header("Location: " . BASE_URL . "/user-login-otp");
        exit;
    }

    public function processAdminLogin()
    {
        Csrf::verify();
        $this->startSession();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->validateCredentialsInput($email, $password)) {
            $this->redirectWithError('/admin-login', 'Please enter a valid admin email and password.');
        }

        if ($this->isTooManyAttempts($email)) {
            $this->redirectWithError('/admin-login', 'Admin login temporarily locked due to too many failed attempts. Please try again after 15 minutes.');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || $user['role'] !== 'admin') {
            $this->recordLoginAttempt($email, false);

            if ($user) {
                $this->logActivity($user['id'], 'Failed login attempt');
            }

            $this->redirectWithError('/admin-login', 'Invalid admin credentials.');
        }

        if (!$this->isValidUser($user, $password)) {
            $this->recordLoginAttempt($email, false);
            $this->logActivity($user['id'], 'Failed login attempt');
            $this->redirectWithError('/admin-login', 'Invalid admin credentials.');
        }

        $this->recordLoginAttempt($email, true);
        $this->logActivity($user['id'], 'Password verified for login');

        $this->redirectToMfa($user);
    }

    public function logout()
    {
        $this->startSession();

        $role = $_SESSION['auth_user_role'] ?? 'user';
        $userId = $_SESSION['auth_user_id'] ?? null;

        if ($userId) {
            $this->logActivity($userId, 'Logged out');
        }

        session_unset();
        session_destroy();

        if ($role === 'admin') {
            header("Location: " . BASE_URL . "/admin-login");
            exit;
        }

        header("Location: " . BASE_URL . "/user-login");
        exit;
    }

    private function redirectToMfa($user)
    {
        $this->startSession();

        $secretModel = new AuthenticatorSecret();
        $secret = $secretModel->findByUserId($user['id']);

        if ($secret && (int)$secret['is_enabled'] === 1) {
            $_SESSION['mfa_pending_user_id'] = $user['id'];
            $_SESSION['mfa_pending_name'] = $user['full_name'];
            $_SESSION['mfa_pending_email'] = $user['email'];
            $_SESSION['mfa_pending_role'] = $user['role'];
            $_SESSION['mfa_pending_is_admin_agent'] = $user['is_admin_agent'] ?? 0;

            header("Location: " . BASE_URL . "/mfa-verify");
            exit;
        }

        $_SESSION['mfa_setup_user_id'] = $user['id'];
        $_SESSION['mfa_setup_name'] = $user['full_name'];
        $_SESSION['mfa_setup_email'] = $user['email'];
        $_SESSION['mfa_setup_role'] = $user['role'];
        $_SESSION['mfa_setup_is_admin_agent'] = $user['is_admin_agent'] ?? 0;

        header("Location: " . BASE_URL . "/mfa-setup");
        exit;
    }

    private function loginUser($user)
    {
        session_regenerate_id(true);

        $_SESSION['auth_user_id'] = $user['id'];
        $_SESSION['auth_user_name'] = $user['full_name'];
        $_SESSION['auth_user_email'] = $user['email'];
        $_SESSION['auth_user_role'] = $user['role'];
        $_SESSION['is_admin_agent'] = $user['is_admin_agent'] ?? 0;
        $_SESSION['organization_id'] = $user['organization_id'] ?? null;
        $_SESSION['is_organization_admin'] = $user['is_organization_admin'] ?? 0;
        $_SESSION['last_activity'] = time();

        $userModel = new User();
        $userModel->updateLastLogin($user['id']);
    }

    private function validateCredentialsInput($email, $password)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password);
    }

    private function isValidUser($user, $password)
    {
        if ((int)$user['is_active'] !== 1) {
            return false;
        }

        if ((int)$user['is_email_verified'] !== 1) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    private function redirectWithError($path, $message)
    {
        $this->startSession();

        $_SESSION['error'] = $message;

        header("Location: " . BASE_URL . $path);
        exit;
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getIpAddress()
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private function isTooManyAttempts($email)
    {
        $attemptModel = new LoginAttempt();

        $failedAttempts = $attemptModel->countRecentFailed(
            $email,
            $this->getIpAddress()
        );

        return $failedAttempts >= 5;
    }

    private function recordLoginAttempt($email, $isSuccess)
    {
        $attemptModel = new LoginAttempt();

        $attemptModel->record(
            $email,
            $this->getIpAddress(),
            $isSuccess
        );
    }

    public function userOtpPage()
    {
        $this->startSession();

        if (!isset($_SESSION['user_login_otp_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $this->view('auth/user-login-otp');
    }

    public function verifyUserOtp()
    {
        Csrf::verify();
        $this->startSession();

        if (!isset($_SESSION['user_login_otp_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $userId = $_SESSION['user_login_otp_user_id'];
        $otp = trim($_POST['otp'] ?? '');

        if (!preg_match('/^\d{6}$/', $otp)) {
            $_SESSION['error'] = "Please enter a valid 6-digit OTP.";
            header("Location: " . BASE_URL . "/user-login-otp");
            exit;
        }

        $otpModel = new LoginOtp();
        $otpRow = $otpModel->verifyOtp($userId, $otp);

        if (!$otpRow) {
            $_SESSION['error'] = "Invalid or expired OTP.";
            header("Location: " . BASE_URL . "/user-login-otp");
            exit;
        }

        $otpModel->markUsed($otpRow['id']);

        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        unset($_SESSION['user_login_otp_user_id']);
        unset($_SESSION['user_login_otp_email']);

        $this->loginUser($user);

        header("Location: " . BASE_URL . "/user-dashboard");
        exit;
    }

    private function sendUserLoginOtp($user)
    {
        $this->startSession();

        $otpModel = new LoginOtp();

        // Expire all previous unused OTPs in database when user logs in again
        $otpModel->expireAllUnusedByUserId($user['id']);

        $_SESSION['user_login_otp_user_id'] = $user['id'];
        $_SESSION['user_login_otp_email'] = $user['email'];

        $otp = random_int(100000, 999999);

        $expiresAt = date(
            'Y-m-d H:i:s',
            strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes')
        );

        $otpModel->createOtp(
            $user['id'],
            $otp,
            $expiresAt
        );

        $mailSent = MailService::sendLoginOtp(
            $user['email'],
            $otp
        );

        if (!$mailSent) {

            $otpModel->deleteUnusedOtpByUserId($user['id']);

            $_SESSION['error'] =
                "Unable to send OTP email. Please try again later.";

            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $this->logActivity($user['id'], 'Login OTP sent via email');

        $_SESSION['success'] =
            "Verification code has been sent to your registered email address.";
    }

    public function resendUserLoginOtp()
    {
        Csrf::verify();
        $this->startSession();

        if (!isset($_SESSION['user_login_otp_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $userId = $_SESSION['user_login_otp_user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $otpModel = new LoginOtp();
        $otpModel->expireAllUnusedByUserId($user['id']);

        $otp = random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $otpModel->createOtp($user['id'], $otp, $expiresAt);

        $mailSent = MailService::sendLoginOtp($user['email'], $otp);

        if (!$mailSent) {
            $otpModel->deleteUnusedOtpByUserId($user['id']);
            $_SESSION['error'] = "Unable to send verification code email. Please try again later.";
            header("Location: " . BASE_URL . "/user-login-otp");
            exit;
        }

        $this->logActivity($user['id'], 'Login OTP resent via email');

        $_SESSION['success'] = "A new verification code has been sent to your registered email address.";
        header("Location: " . BASE_URL . "/user-login-otp");
        exit;
    }

    public function forgotPasswordPage()
    {
        AuthMiddleware::guest();

        $this->view('auth/forgot-password');
    }

    public function sendForgotPasswordOtp()
    {
        Csrf::verify();
        $this->startSession();

        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('/forgot-password', 'Please enter a valid email address.');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || (int)$user['is_active'] !== 1) {
            $this->redirectWithError('/forgot-password', 'No active account found with this email.');
        }

        $otpModel = new PasswordResetOtp();

        $_SESSION['forgot_password_user_id'] = $user['id'];
        $_SESSION['forgot_password_email'] = $user['email'];

        // Expire all previous unused OTPs in database
        $otpModel->expireAllUnusedByUserId($user['id']);

        $otp = random_int(100000, 999999);

        $expiresAt = date(
            'Y-m-d H:i:s',
            strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes')
        );

        $otpModel->createOtp(
            $user['id'],
            $otp,
            $expiresAt
        );

        $mailSent = MailService::sendForgotPasswordOtp($user['email'], $otp);

        if (!$mailSent) {
            $_SESSION['error'] = "Unable to send password reset OTP. Please try again later.";
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $this->logActivity($user['id'], 'Password reset OTP sent via email');

        $_SESSION['success'] =
            "Password reset OTP has been sent to your registered email address.";

        header("Location: " . BASE_URL . "/forgot-password-verify");
        exit;
    }

    public function resendForgotPasswordOtp()
    {
        Csrf::verify();
        $this->startSession();

        if (!isset($_SESSION['forgot_password_user_id'])) {
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $userId = $_SESSION['forgot_password_user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $otpModel = new PasswordResetOtp();
        $otpModel->expireAllUnusedByUserId($user['id']);

        $otp = random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $otpModel->createOtp($user['id'], $otp, $expiresAt);

        $mailSent = MailService::sendForgotPasswordOtp($user['email'], $otp);

        if (!$mailSent) {
            $_SESSION['error'] = "Unable to send password reset code. Please try again later.";
            header("Location: " . BASE_URL . "/forgot-password-verify");
            exit;
        }

        $this->logActivity($user['id'], 'Password reset OTP resent via email');

        $_SESSION['success'] = "A new password reset verification code has been sent to your registered email address.";
        header("Location: " . BASE_URL . "/forgot-password-verify");
        exit;
    }

    public function forgotPasswordVerifyPage()
    {
        AuthMiddleware::guest();

        $this->startSession();

        if (!isset($_SESSION['forgot_password_user_id'])) {
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $this->view('auth/forgot-password-verify');
    }

    public function verifyForgotPasswordOtp()
    {
        Csrf::verify();
        $this->startSession();

        if (!isset($_SESSION['forgot_password_user_id'])) {
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $userId = $_SESSION['forgot_password_user_id'];
        $otp = trim($_POST['otp'] ?? '');

        if (!preg_match('/^\d{6}$/', $otp)) {
            $_SESSION['error'] = "Please enter a valid 6-digit OTP.";
            header("Location: " . BASE_URL . "/forgot-password-verify");
            exit;
        }

        $otpModel = new PasswordResetOtp();
        $otpRow = $otpModel->verifyOtp($userId, $otp);

        if (!$otpRow) {
            $_SESSION['error'] = "Invalid or expired OTP.";
            header("Location: " . BASE_URL . "/forgot-password-verify");
            exit;
        }

        $otpModel->markUsed($otpRow['id']);

        $_SESSION['forgot_password_verified'] = true;

        header("Location: " . BASE_URL . "/reset-password");
        exit;
    }

    private function validateStrongPassword($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    public function resetPasswordPage()
    {
        AuthMiddleware::guest();

        $this->startSession();

        if (
            !isset($_SESSION['forgot_password_user_id']) ||
            !isset($_SESSION['forgot_password_verified'])
        ) {
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $this->view('auth/reset-password');
    }

    public function resetPassword()
    {
        Csrf::verify();
        $this->startSession();

        if (
            !isset($_SESSION['forgot_password_user_id']) ||
            !isset($_SESSION['forgot_password_verified'])
        ) {
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = "Password and confirm password do not match.";
            header("Location: " . BASE_URL . "/reset-password");
            exit;
        }

        if (!$this->validateStrongPassword($password)) {
            $_SESSION['error'] = "Password must contain minimum 8 characters, one uppercase letter, one lowercase letter, one number, and one special character.";
            header("Location: " . BASE_URL . "/reset-password");
            exit;
        }

        $userId = $_SESSION['forgot_password_user_id'];

        $userModel = new User();
        $updated = $userModel->updatePassword($userId, $password);

        if (!$updated) {
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header("Location: " . BASE_URL . "/reset-password");
            exit;
        }

        $this->logActivity($userId, 'Password reset completed');

        $user = $userModel->findById($userId);

        MailService::send(
            $user['email'],
            'Password Changed Successfully',
            '
            <h2>Samtech Helpdesk</h2>
            <p>Your account password has been changed successfully.</p>
            <p>If you did not perform this action, contact support immediately.</p>
            '
        );

        unset($_SESSION['forgot_password_user_id']);
        unset($_SESSION['forgot_password_email']);
        unset($_SESSION['forgot_password_verified']);

        $_SESSION['success'] = "Password updated successfully. Please login.";

        header("Location: " . BASE_URL . "/user-login");
        exit;
    }

    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }

    private function logActivity($userId, $action)
    {
        $logModel = new ActivityLog();

        $logModel->create(
            $userId,
            $action,
            $this->getIpAddress(),
            $this->getUserAgent()
        );
    }

    public function unsubscribePage()
    {
        $baseUrl = BASE_URL;
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Subscriptions - Samtech Helpdesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card shadow-sm border-0 p-4 p-md-5 text-center" style="max-width: 520px; border-radius: 16px;">
        <div class="mb-3">
            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                Email Notification Preferences
            </span>
        </div>
        <h3 class="fw-bold text-dark mb-3">Samtech Helpdesk Notifications</h3>
        <p class="text-secondary small mb-4">
            You are receiving transactional ticket and security notifications from Samtech Helpdesk.
            To modify your notification preferences or update your email settings, please access your profile dashboard.
        </p>
        <a href="' . $baseUrl . '/user-login" class="btn btn-primary btn-lg rounded-pill px-4">
            Log In to Helpdesk Account
        </a>
    </div>
</body>
</html>';
        exit;
    }

    public function processUnsubscribe()
    {
        http_response_code(200);
        header('Content-Type: text/plain');
        echo "Unsubscribe request received.";
        exit;
    }
}
