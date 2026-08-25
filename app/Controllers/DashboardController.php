<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Organization.php";
require_once ROOT_PATH . "/app/Models/ActivityLog.php";

class DashboardController extends Controller
{
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        AuthMiddleware::timeout();
        $this->startSession();
        $role = $_SESSION['auth_user_role'] ?? '';
        if ($role !== 'admin') {
            AuthMiddleware::redirectByRole($role);
            exit;
        }

        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();
        $activityModel = new ActivityLog();

        $ticketCounts = $ticketModel->getDashboardCounts();
        $recentTickets = $ticketModel->getRecentTickets(8);
        $userCounts = $userModel->getUserCounts();
        $organizationCount = $organizationModel->countAll();
        $recentActivities = $activityModel->getRecent(8);
        $monthlyTickets = $ticketModel->getMonthlyTicketCounts();
        $organizationTickets = $ticketModel->getOrganizationTicketCounts();
        $overdueTickets = $ticketModel->getOverdueSlaTickets();

        $this->view('dashboards/admin', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'userCounts' => $userCounts,
            'organizationCount' => $organizationCount,
            'recentActivities' => $recentActivities,
            'monthlyTickets' => $monthlyTickets,
            'organizationTickets' => $organizationTickets,
            'overdueTickets' => $overdueTickets
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN AGENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function adminAgent()
    {
        AuthMiddleware::timeout();
        $this->startSession();
        $role = $_SESSION['auth_user_role'] ?? '';
        if (!in_array($role, ['admin', 'agent'])) {
            AuthMiddleware::redirectByRole($role);
            exit;
        }

        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();
        $activityModel = new ActivityLog();

        $ticketCounts = $ticketModel->getDashboardCounts();
        $recentTickets = $ticketModel->getRecentTickets(8);
        $userCounts = $userModel->getUserCounts();
        $organizationCount = $organizationModel->countAll();
        $recentActivities = $activityModel->getRecent(8);
        $monthlyTickets = $ticketModel->getMonthlyTicketCounts();
        $organizationTickets = $ticketModel->getOrganizationTicketCounts();
        $overdueTickets = $ticketModel->getOverdueSlaTickets();

        $this->view('dashboards/admin-agent', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'userCounts' => $userCounts,
            'organizationCount' => $organizationCount,
            'recentActivities' => $recentActivities,
            'monthlyTickets' => $monthlyTickets,
            'organizationTickets' => $organizationTickets,
            'overdueTickets' => $overdueTickets
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AGENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function agent()
    {
        AuthMiddleware::timeout();
        $this->startSession();
        $role = $_SESSION['auth_user_role'] ?? '';
        if (!in_array($role, ['admin', 'agent'])) {
            AuthMiddleware::redirectByRole($role);
            exit;
        }

        $ticketModel = new Ticket();

        $ticketCounts = $ticketModel->getDashboardCounts();
        $recentTickets = $ticketModel->getRecentTickets(8);
        $monthlyTickets = $ticketModel->getMonthlyTicketCounts();
        $organizationTickets = $ticketModel->getOrganizationTicketCounts();
        $overdueTickets = $ticketModel->getOverdueSlaTickets();

        $this->view('dashboards/agent', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'monthlyTickets' => $monthlyTickets,
            'organizationTickets' => $organizationTickets,
            'overdueTickets' => $overdueTickets
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | USER DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        AuthMiddleware::timeout();
        $this->startSession();
        $role = $_SESSION['auth_user_role'] ?? '';
        if (in_array($role, ['admin', 'agent'])) {
            AuthMiddleware::redirectByRole($role);
            exit;
        }

        $ticketModel = new Ticket();
        $userModel = new User();

        $user = $userModel->findWithOrganization(
            $_SESSION['auth_user_id']
        );

        if (!$user || empty($user['organization_id'])) {

            $ticketCounts = [
                'total' => 0,
                'open_count' => 0,
                'in_progress_count' => 0,
                'pending_count' => 0,
                'resolved_count' => 0,
                'closed_count' => 0
            ];

            $monthlyTickets = [];
            $recentTickets = [];
            $overdueTickets = [];

        } else {

            $ticketCounts = $ticketModel->getDashboardCounts(
                $user['organization_id']
            );

            $recentTickets = $ticketModel->getRecentTickets(
                8,
                $user['organization_id']
            );

            $monthlyTickets = $ticketModel->getMonthlyTicketCounts(
                $user['organization_id']
            );

            $overdueTickets = $ticketModel->getOverdueSlaTickets(
                $user['organization_id']
            );
        }

        $this->view('dashboards/user', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'user' => $user,
            'monthlyTickets' => $monthlyTickets,
            'overdueTickets' => $overdueTickets
        ]);
    }
}