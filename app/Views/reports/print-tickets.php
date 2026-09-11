<?php
$logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
$logoSrc = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : BASE_URL . '/assets/images/samtech-logo-report.png';

$iconPath = ROOT_PATH . '/public/assets/images/samtech-icon.png';
$iconSrc = file_exists($iconPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($iconPath))
    : BASE_URL . '/assets/images/samtech-icon.png';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Samtech-Helpdesk-<?= rand(100000, 900000); ?></title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 8mm 8mm 22mm 8mm;
            box-sizing: border-box;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 10px;
        }

        .page {
            width: 100%;
            padding-bottom: 50px;
        }

        .print-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }

        .print-btn {
            background: #488a25;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 11px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .print-btn:hover {
            background: #396e1d;
        }

        /* Letterhead Watermark */
        .watermark-bg {
            position: fixed;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            z-index: -1000;
            pointer-events: none;
            user-select: none;
            text-align: center;
            width: 100%;
            opacity: 0.05;
        }

        .watermark-bg .wm-text {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 65pt;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -1px;
        }

        .watermark-bg .wm-green {
            color: #488a25;
        }

        .watermark-bg .wm-dark {
            color: #1e293b;
        }

        .watermark-icon-bg {
            position: fixed;
            bottom: -60px;
            left: -40px;
            width: 280px;
            height: auto;
            opacity: 0.04;
            z-index: -1000;
            pointer-events: none;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #488a25;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-collapse: collapse;
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
        }

        .logo {
            width: 190px;
            height: auto;
        }

        .report-title {
            font-size: 18px;
            font-weight: 800;
            margin-top: 4px;
            color: #111827;
        }

        .report-subtitle {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
        }

        .meta {
            text-align: right;
            color: #475569;
            font-size: 9.5px;
            line-height: 1.5;
        }

        .criteria {
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-size: 10px;
            line-height: 1.6;
        }

        .criteria-label {
            font-weight: 800;
            color: #111827;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-before: avoid !important;
        }

        table.data-table th {
            background: #111827;
            color: #ffffff;
            text-align: left;
            padding: 7px 6px;
            border: 1px solid #111827;
            font-size: 9.5px;
            font-weight: 800;
        }

        table.data-table td {
            padding: 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            font-size: 9px;
            word-wrap: break-word;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Letterhead Footer */
        .letterhead-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            color: #334155;
            background: #ffffff;
            padding: 6px 8mm 6px 8mm;
            border-top: 1px solid #cbd5e1;
            z-index: 1000;
        }

        .letterhead-footer .lh-company {
            font-weight: 800;
            font-size: 9.5pt;
            color: #0f172a;
        }

        .letterhead-footer .lh-address {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }

        .letterhead-footer .lh-contact {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 1px;
        }

        .sr-no { width: 4%; text-align: center; }
        .ticket-no { width: 10%; }
        .org { width: 11%; }
        .user { width: 10%; }
        .agent { width: 10%; }
        .subject { width: 16%; }
        .priority { width: 6%; }
        .status { width: 7%; }
        .created { width: 9%; }
        .closed-date { width: 9%; }
        .closed-by { width: 8%; }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            .print-actions, .print-btn {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            html, body {
                margin: 0 !important;
                padding: 8mm 8mm 22mm 8mm !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
                color: #111827 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.header-table, .criteria, table.data-table {
                page-break-before: avoid !important;
            }

            .letterhead-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 6px 8mm 6px 8mm;
            }
        }
    </style>
</head>

<body>

<!-- Letterhead Watermark -->
<div class="watermark-bg">
    <div class="wm-text">
        <span class="wm-green">Samtech</span><br>
        <span class="wm-dark">Solutions</span>
    </div>
</div>
<?php if (!empty($iconSrc)): ?>
    <img src="<?= $iconSrc; ?>" class="watermark-icon-bg" alt="">
<?php endif; ?>

<div class="page">

    <?php if (empty($isPdfDownload)): ?>
    <div class="print-actions">
        <button onclick="window.print()" class="print-btn">
            🖨️ Print / Save PDF
        </button>
    </div>
    <?php endif; ?>

    <table class="header-table">
        <tr>
            <td style="border:none; vertical-align:top; text-align:left; padding:0;">
                <img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions">
                <div class="report-title">Ticket Report</div>
                <div class="report-subtitle">Samtech Helpdesk Management System</div>
            </td>
            <td style="border:none; vertical-align:top; text-align:right; padding:0;" class="meta">
                <strong>Generated On:</strong> <?= date('d M Y, h:i A'); ?><br>
                <strong>Generated By:</strong> <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'System'); ?><br>
                <strong>Total Records:</strong> <?= count($tickets); ?>
            </td>
        </tr>
    </table>

    <?php
        $criteria = [];

        if (!empty($filters['organization_id']) && !empty($organizations)) {
            foreach ($organizations as $o) {
                if ($o['id'] == $filters['organization_id']) {
                    $criteria[] = '<span class="criteria-label">Organization:</span> ' . htmlspecialchars($o['name']);
                    break;
                }
            }
        }

        if (!empty($filters['user_id']) && !empty($users)) {
            foreach ($users as $u) {
                if ($u['id'] == $filters['user_id']) {
                    $criteria[] = '<span class="criteria-label">User:</span> ' . htmlspecialchars($u['full_name']);
                    break;
                }
            }
        }

        if (!empty($filters['agent_id']) && !empty($agents)) {
            foreach ($agents as $a) {
                if ($a['id'] == $filters['agent_id']) {
                    $criteria[] = '<span class="criteria-label">Agent:</span> ' . htmlspecialchars($a['full_name']);
                    break;
                }
            }
        }

        if (!empty($filters['status'])) {
            $criteria[] = '<span class="criteria-label">Status:</span> ' . htmlspecialchars(ucwords(str_replace('_', ' ', $filters['status'])));
        }

        if (!empty($filters['priority'])) {
            $criteria[] = '<span class="criteria-label">Priority:</span> ' . htmlspecialchars(ucfirst($filters['priority']));
        }

        if (!empty($filters['date_from'])) {
            $criteria[] = '<span class="criteria-label">From:</span> ' . htmlspecialchars($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $criteria[] = '<span class="criteria-label">To:</span> ' . htmlspecialchars($filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $criteria[] = '<span class="criteria-label">Search:</span> ' . htmlspecialchars($filters['search']);
        }
    ?>

    <?php if (!empty($criteria)): ?>
        <div class="criteria">
            <?= implode(' &nbsp;|&nbsp; ', $criteria); ?>
        </div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">Sr. No</th>
                <th style="width: 10%;">Ticket No</th>
                <th style="width: 11%;">Organization</th>
                <th style="width: 10%;">User</th>
                <th style="width: 10%;">Assigned Agent</th>
                <th style="width: 16%;">Subject</th>
                <th style="width: 6%;">Priority</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 9%;">Created</th>
                <th style="width: 9%;">Closed Date</th>
                <th style="width: 8%;">Closed By</th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="11" style="text-align: center; padding: 18px; color: #64748b;">
                    No tickets found matching the selected report criteria.
                </td>
            </tr>
        <?php else: ?>

            <?php $srNo = 1; ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td class="sr-no" style="text-align: center;"><?= $srNo++; ?></td>
                    <td class="ticket-no"><strong><?= htmlspecialchars($ticket['ticket_no'] ?? '-'); ?></strong></td>
                    <td class="org"><?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?></td>
                    <td class="user"><?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?></td>
                    <td class="agent"><?= htmlspecialchars($ticket['assigned_agent_name'] ?? '-'); ?></td>
                    <td class="subject"><?= htmlspecialchars($ticket['subject'] ?? '-'); ?></td>
                    <td class="priority"><?= htmlspecialchars(ucfirst($ticket['priority'] ?? '')); ?></td>
                    <td class="status"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status'] ?? ''))); ?></td>
                    <td class="created"><?= !empty($ticket['created_at']) ? date('Y-m-d H:i', strtotime($ticket['created_at'])) : '-'; ?></td>
                    <td class="closed-date"><?= !empty($ticket['closed_at']) ? date('Y-m-d H:i', strtotime($ticket['closed_at'])) : '-'; ?></td>
                    <td class="closed-by"><?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>
    </table>

</div>

<!-- Official Company Letterhead Footer -->
<div class="letterhead-footer">
    <div class="lh-company">Samvruddhi Technologies LLC</div>
    <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
    <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
</div>

<script>
    window.addEventListener("load", function () {
        setTimeout(function() {
            window.print();
        }, 250);
    });
</script>

</body>
</html>