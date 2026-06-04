<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Access Denied</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body{
            background:#f8fafc;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            font-family:Inter,sans-serif;
        }

        .access-card{
            max-width:650px;
            width:100%;
            background:#fff;
            border-radius:24px;
            padding:50px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
            text-align:center;
        }

        .error-code{
            font-size:90px;
            font-weight:800;
            color:#ef4444;
            line-height:1;
        }

        .error-title{
            font-size:28px;
            font-weight:700;
            color:#111827;
            margin-top:15px;
        }

        .error-text{
            color:#6b7280;
            margin-top:15px;
            margin-bottom:30px;
        }

        .btn-home{
            background:#b1e96f;
            border:none;
            color:#111827;
            font-weight:600;
            padding:10px 24px;
            border-radius:10px;
        }

        .btn-home:hover{
            background:#9fd95f;
        }

        .icon{
            font-size:70px;
            margin-bottom:10px;
        }

    </style>

</head>

<body>

    <div class="access-card">

        <div class="icon">
            🔒
        </div>

        <div class="error-code">
            403
        </div>

        <div class="error-title">
            Access Denied
        </div>

        <div class="error-text">

            You do not have permission to access this module.

            <br><br>

            If you believe this is incorrect,
            please contact your system administrator.

        </div>

        <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

        <?php
            $role = $_SESSION['auth_user_role'] ?? '';
        ?>

        <?php if($role === 'admin'): ?>

            <a
                href="<?= BASE_URL ?>/admin/dashboard"
                class="btn btn-home"
            >
                Go To Dashboard
            </a>

        <?php elseif($role === 'agent'): ?>

            <a
                href="<?= BASE_URL ?>/agent/dashboard"
                class="btn btn-home"
            >
                Go To Dashboard
            </a>

        <?php else: ?>

            <a
                href="<?= BASE_URL ?>/dashboard"
                class="btn btn-home"
            >
                Go To Dashboard
            </a>

        <?php endif; ?>

    </div>

</body>

</html>