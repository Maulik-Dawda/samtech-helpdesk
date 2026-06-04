<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">

    <title>Samtech Helpdesk</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">

    <style>
        .top-navbar {
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .user-card-btn {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 14px;
            padding: 7px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            min-width: 190px;
            transition: all .2s ease;
        }

        .user-card-btn:hover {
            background: #f8fafc;
            border-color: #d1d5db;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #b1e96f;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-info {
            line-height: 1.1;
            text-align: left;
            overflow: hidden;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: #6b7280;
            text-transform: capitalize;
            margin-top: 3px;
        }

        .dropdown-menu {
            border: 0;
            box-shadow: 0 12px 30px rgba(0,0,0,.12);
            border-radius: 14px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 10px;
            font-size: 14px;
            padding: 9px 12px;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
        }

        .dropdown-item.text-danger:hover {
            background: #fee2e2;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php require_once "../app/Views/layouts/sidebar.php"; ?>

<div class="main-content">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm top-navbar">

        <div class="container-fluid">

            <div class="navbar-brand fw-bold mb-0">
                Samtech Helpdesk
            </div>

            <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

            <?php if (isset($_SESSION['auth_user_id'])): ?>

                <?php
                    $fullName = $_SESSION['auth_user_name'] ?? 'User';
                    $role = $_SESSION['auth_user_role'] ?? '';

                    $nameParts = preg_split('/\s+/', trim($fullName));

                    $initials = '';

                    if (!empty($nameParts[0])) {
                        $initials .= strtoupper(substr($nameParts[0], 0, 1));
                    }

                    if (!empty($nameParts[1])) {
                        $initials .= strtoupper(substr($nameParts[1], 0, 1));
                    }

                    if ($initials === '') {
                        $initials = 'U';
                    }
                ?>

                <div class="dropdown ms-auto">

                    <button
                        class="user-card-btn dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        <div class="avatar-circle">
                            <?= htmlspecialchars($initials); ?>
                        </div>

                        <div class="user-info">
                            <div class="user-name">
                                <?= htmlspecialchars($fullName); ?>
                            </div>

                            <div class="user-role">
                                <?= htmlspecialchars($role); ?>
                            </div>
                        </div>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a
                                class="dropdown-item"
                                href="<?= BASE_URL ?>/profile"
                            >
                                View Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <button
                                class="dropdown-item text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutModal"
                            >
                                Logout
                            </button>
                        </li>

                    </ul>

                </div>

            <?php endif; ?>

        </div>

    </nav>

    <?php if (isset($_SESSION['auth_user_id'])): ?>

        <div class="modal fade" id="logoutModal" tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content border-0 rounded-4">

                    <div class="modal-header border-0">

                        <h5 class="modal-title fw-bold">
                            Confirm Logout
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body text-center">

                        <div style="font-size:46px;">
                            🚪
                        </div>

                        <h6 class="mt-3">
                            Are you sure you want to logout?
                        </h6>

                        <p class="text-muted mb-0">
                            You will be signed out from your account.
                        </p>

                    </div>

                    <div class="modal-footer border-0">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <a
                            href="<?= BASE_URL ?>/logout"
                            class="btn btn-danger">
                            Logout
                        </a>

                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>