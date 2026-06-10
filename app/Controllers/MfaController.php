<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/AuthenticatorSecret.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/MfaRecoveryOtp.php";
require_once ROOT_PATH . "/app/Models/ActivityLog.php";

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;

class DummyQrProvider implements IQRCodeProvider
{
    public function getQRCodeImage(string $qrText, int $size): string
    {
        return '';
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }
}

class MfaController extends Controller
{
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function tfa()
    {
        return new TwoFactorAuth(
            new DummyQrProvider(),
            'Samtech Helpdesk'
        );
    }

    public function setup()
    {

        $this->redirectIfAuthenticated();

        $this->startSession();

        if (!isset($_SESSION['mfa_setup_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $userId = $_SESSION['mfa_setup_user_id'];

        $secretModel = new AuthenticatorSecret();
        $secretRow = $secretModel->findByUserId($userId);

        $tfa = $this->tfa();

        if (!$secretRow) {
            $secret = $tfa->createSecret();
            $secretModel->createSecret($userId, $secret);
        } else {
            $secret = $secretRow['secret_key'];
        }

        $this->view('auth/mfa-setup', [
            'secret' => $secret
        ]);
    }

    public function verifySetup()
    {
        Csrf::verify();

        $this->startSession();

        if (!isset($_SESSION['mfa_setup_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $userId = $_SESSION['mfa_setup_user_id'];
        $code = trim($_POST['code'] ?? '');

        if (!preg_match('/^\d{6}$/', $code)) {
            $_SESSION['error'] = "Please enter a valid 6-digit authenticator code.";
            header("Location: " . BASE_URL . "/mfa-setup");
            exit;
        }

        $secretModel = new AuthenticatorSecret();
        $secretRow = $secretModel->findByUserId($userId);

        if (!$secretRow) {
            $_SESSION['error'] = "Authenticator setup not found.";
            header("Location: " . BASE_URL . "/mfa-setup");
            exit;
        }

        $tfa = $this->tfa();

        if (!$tfa->verifyCode($secretRow['secret_key'], $code)) {
            $_SESSION['error'] = "Invalid authenticator code.";
            header("Location: " . BASE_URL . "/mfa-setup");
            exit;
        }

        $secretModel->enableSecret($userId);
        $this->logActivity($userId, 'MFA setup completed');

        session_regenerate_id(true);

        $_SESSION['auth_user_id'] = $_SESSION['mfa_setup_user_id'];
        $_SESSION['auth_user_name'] = $_SESSION['mfa_setup_name'];
        $_SESSION['auth_user_email'] = $_SESSION['mfa_setup_email'];
        $_SESSION['auth_user_role'] = $_SESSION['mfa_setup_role'];
        $_SESSION['last_activity'] = time();

        unset($_SESSION['mfa_setup_user_id']);
        unset($_SESSION['mfa_setup_name']);
        unset($_SESSION['mfa_setup_email']);
        unset($_SESSION['mfa_setup_role']);

        $this->redirectByRole($_SESSION['auth_user_role']);
    }

    public function verifyPage()
    {
        $this->redirectIfAuthenticated();
        $this->startSession();

        if (!isset($_SESSION['mfa_pending_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $this->view('auth/mfa-verify');
    }

    public function verifyLogin()
    {
        Csrf::verify();

        $this->startSession();

        if (!isset($_SESSION['mfa_pending_user_id'])) {
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $userId = $_SESSION['mfa_pending_user_id'];
        $code = trim($_POST['code'] ?? '');

        if (!preg_match('/^\d{6}$/', $code)) {
            $_SESSION['error'] = "Please enter a valid 6-digit authenticator code.";
            header("Location: " . BASE_URL . "/mfa-verify");
            exit;
        }

        $secretModel = new AuthenticatorSecret();
        $secretRow = $secretModel->findByUserId($userId);

        if (!$secretRow || (int)$secretRow['is_enabled'] !== 1) {
            $_SESSION['error'] = "Authenticator is not enabled.";
            header("Location: " . BASE_URL . "/user-login");
            exit;
        }

        $tfa = $this->tfa();

        if (!$tfa->verifyCode($secretRow['secret_key'], $code)) {
            $_SESSION['error'] = "Invalid authenticator code.";
            header("Location: " . BASE_URL . "/mfa-verify");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['auth_user_id'] = $_SESSION['mfa_pending_user_id'];
        $_SESSION['auth_user_name'] = $_SESSION['mfa_pending_name'];
        $_SESSION['auth_user_email'] = $_SESSION['mfa_pending_email'];
        $_SESSION['auth_user_role'] = $_SESSION['mfa_pending_role'];
        $_SESSION['last_activity'] = time();

        unset($_SESSION['mfa_pending_user_id']);
        unset($_SESSION['mfa_pending_name']);
        unset($_SESSION['mfa_pending_email']);
        unset($_SESSION['mfa_pending_role']);
        
        $this->logActivity($userId, 'MFA login verified');
        $this->redirectByRole($_SESSION['auth_user_role']);
    }

    private function redirectByRole($role)
    {
        if ($role === 'admin') {
            header("Location: " . BASE_URL . "/admin-dashboard");
            exit;
        }

        if ($role === 'agent') {
            header("Location: " . BASE_URL . "/agent-dashboard");
            exit;
        }

        header("Location: " . BASE_URL . "/user-dashboard");
        exit;
    }

    private function redirectIfAuthenticated()
    {
        $this->startSession();

        if (isset($_SESSION['auth_user_id'])) {
            $this->redirectByRole($_SESSION['auth_user_role']);
        }
    }

    public function recoveryPage()
    {
        $this->redirectIfAuthenticated();

        $this->startSession();

        $this->view('auth/mfa-recovery');
    }

    public function processRecovery()
    {
        Csrf::verify();

        $this->startSession();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $_SESSION['error'] = "Please enter valid email and password.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        require_once ROOT_PATH . "/app/Models/User.php";

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !in_array($user['role'], ['agent', 'admin'])) {
            $_SESSION['error'] = "Invalid account details.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Invalid account details.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        $tfa = $this->tfa();
        $newSecret = $tfa->createSecret();

        $secretModel = new AuthenticatorSecret();
        $existingSecret = $secretModel->findByUserId($user['id']);

        if ($existingSecret) {
            $secretModel->resetMfa($user['id'], $newSecret);
        } else {
            $secretModel->createSecret($user['id'], $newSecret);
        }

        $_SESSION['mfa_setup_user_id'] = $user['id'];
        $_SESSION['mfa_setup_name'] = $user['full_name'];
        $_SESSION['mfa_setup_email'] = $user['email'];
        $_SESSION['mfa_setup_role'] = $user['role'];

        header("Location: " . BASE_URL . "/mfa-setup");
        exit;
    }
    public function sendRecoveryOtp()
    {
        Csrf::verify();

        $this->startSession();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $_SESSION['error'] = "Please enter valid email and password.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !in_array($user['role'], ['agent', 'admin'])) {
            $_SESSION['error'] = "Invalid account details.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Invalid account details.";
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        $otp = random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $otpModel = new MfaRecoveryOtp();
        $otpModel->createOtp($user['id'], $otp, $expiresAt);

        $_SESSION['mfa_recovery_user_id'] = $user['id'];
        $_SESSION['mfa_recovery_name'] = $user['full_name'];
        $_SESSION['mfa_recovery_email'] = $user['email'];
        $_SESSION['mfa_recovery_role'] = $user['role'];

        // Temporary for local testing. Later this will be sent by email.
        $_SESSION['success'] = "Recovery OTP is: " . $otp;

        $this->logActivity($user['id'], 'MFA recovery OTP requested');

        header("Location: " . BASE_URL . "/mfa-recovery-verify");
        exit;
    }

    public function recoveryVerifyPage()
{
    $this->redirectIfAuthenticated();

    $this->startSession();

    if (!isset($_SESSION['mfa_recovery_user_id'])) {
        header("Location: " . BASE_URL . "/mfa-recovery");
        exit;
    }

    $this->view('auth/mfa-recovery-verify');
}

    public function verifyRecoveryOtp()
    {
        Csrf::verify();

        $this->startSession();

        if (!isset($_SESSION['mfa_recovery_user_id'])) {
            header("Location: " . BASE_URL . "/mfa-recovery");
            exit;
        }

        $userId = $_SESSION['mfa_recovery_user_id'];
        $otp = trim($_POST['otp'] ?? '');

        if (!preg_match('/^\d{6}$/', $otp)) {
            $_SESSION['error'] = "Please enter a valid 6-digit OTP.";
            header("Location: " . BASE_URL . "/mfa-recovery-verify");
            exit;
        }

        $otpModel = new MfaRecoveryOtp();
        $otpRow = $otpModel->verifyOtp($userId, $otp);

        if (!$otpRow) {
            $_SESSION['error'] = "Invalid or expired OTP.";
            header("Location: " . BASE_URL . "/mfa-recovery-verify");
            exit;
        }

        $otpModel->markUsed($otpRow['id']);
        $this->logActivity($userId, 'MFA recovery OTP verified');

        $tfa = $this->tfa();
        $newSecret = $tfa->createSecret();

        $secretModel = new AuthenticatorSecret();
        $existingSecret = $secretModel->findByUserId($userId);

        if ($existingSecret) {
            $secretModel->resetMfa($userId, $newSecret);
        } else {
            $secretModel->createSecret($userId, $newSecret);
        }

        $_SESSION['mfa_setup_user_id'] = $_SESSION['mfa_recovery_user_id'];
        $_SESSION['mfa_setup_name'] = $_SESSION['mfa_recovery_name'];
        $_SESSION['mfa_setup_email'] = $_SESSION['mfa_recovery_email'];
        $_SESSION['mfa_setup_role'] = $_SESSION['mfa_recovery_role'];

        unset($_SESSION['mfa_recovery_user_id']);
        unset($_SESSION['mfa_recovery_name']);
        unset($_SESSION['mfa_recovery_email']);
        unset($_SESSION['mfa_recovery_role']);

        $this->logActivity($userId, 'MFA secret reset completed');

        header("Location: " . BASE_URL . "/mfa-setup");
        exit;
    }

    private function getIpAddress()
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
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
}
