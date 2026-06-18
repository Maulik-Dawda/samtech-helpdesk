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

    public function admin()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('admin');

        $this->startSession();

        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();

        $ticketCounts = $ticketModel->getDashboardCounts();
        $recentTickets = $ticketModel->getRecentTickets(8);
        $userCounts = $userModel->getUserCounts();
        $organizationCount = $organizationModel->countAll();

        $activityModel = new ActivityLog();

        $recentActivities = $activityModel->getRecent(8);
        $monthlyTickets = $ticketModel->getMonthlyTicketCounts();
        $organizationTickets = $ticketModel->getOrganizationTicketCounts();

        $this->view('dashboards/admin', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'userCounts' => $userCounts,
            'organizationCount' => $organizationCount,
            'recentActivities' => $recentActivities,
            'monthlyTickets' => $monthlyTickets,
            'organizationTickets' => $organizationTickets
        ]);
    }

    public function agent()
{
    AuthMiddleware::timeout();
    AuthMiddleware::check('agent');

    $this->startSession();

    $ticketModel = new Ticket();

    $ticketCounts = $ticketModel->getDashboardCounts();
    $recentTickets = $ticketModel->getRecentTickets(8);
    $monthlyTickets = $ticketModel->getMonthlyTicketCounts();
    $organizationTickets = $ticketModel->getOrganizationTicketCounts();

    $this->view('dashboards/agent', [
        'ticketCounts' => $ticketCounts,
        'recentTickets' => $recentTickets,
        'monthlyTickets' => $monthlyTickets,
        'organizationTickets' => $organizationTickets
    ]);
}

    public function user()
    {
        AuthMiddleware::timeout();
        AuthMiddleware::check('user');

        $this->startSession();

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
        }

        $this->view('dashboards/user', [
            'ticketCounts' => $ticketCounts,
            'recentTickets' => $recentTickets,
            'user' => $user,
            'monthlyTickets' => $monthlyTickets
        ]);
    }
}
