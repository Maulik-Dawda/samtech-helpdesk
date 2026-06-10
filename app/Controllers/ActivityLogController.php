<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Models/ActivityLog.php";
require_once ROOT_PATH . "/app/Models/User.php";

class ActivityLogController extends Controller
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

        $filters = [
            'user_id' => $_GET['user_id'] ?? '',
            'role' => $_GET['role'] ?? '',
            'action' => $_GET['action'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $activityModel = new ActivityLog();
        $userModel = new User();

        $logs = $activityModel->getFilteredLogs($filters);
        $users = $userModel->getAllUsersForAdmin();

        $this->view('admin/activity-logs/index', [
            'logs' => $logs,
            'users' => $users,
            'filters' => $filters
        ]);
    }
}