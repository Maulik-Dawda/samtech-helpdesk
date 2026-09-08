<?php
$logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
$logoSrc = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : BASE_URL . '/assets/images/samtech-logo-report.png';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contract Report - <?= htmlspecialchars($organization['name'] ?? 'Organization'); ?> (<?= htmlspecialchars($contract['contract_name']); ?>)</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 12mm 10mm;
        }

        @media print {
            header, footer, nav, .print-actions {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.5;
        }

        .print-actions {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
        }

        .print-btn {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .logo {
            width: 170px;
            height: auto;
        }

        .report-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .report-subtitle {
            color: #64748b;
            font-size: 11px;
            margin-top: 2px;
        }

        .company-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .contract-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
        }

        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-orange { background: #fef3c7; color: #b45309; }
        .badge-red { background: #fee2e2; color: #b91c1c; }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            text-align: left;
        }

        .table th {
            background: #f1f5f9;
            font-weight: 700;
            color: #1e293b;
        }

        .section-heading {
            font-size: 14px;
            font-weight: 800;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 4px;
            margin-top: 25px;
            margin-bottom: 12px;
            color: #0f172a;
        }

        .ticket-detail-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .ticket-detail-header {
            font-weight: 700;
            font-size: 12px;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .ticket-meta {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .reply-item {
            background: #f8fafc;
            border-left: 3px solid #0284c7;
            padding: 6px 10px;
            margin-top: 6px;
            font-size: 10.5px;
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button onclick="window.print();" class="print-btn">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <!-- Header Table with Logo & Report Details -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <img src="<?= $logoSrc; ?>" alt="Samtech Solutions" class="logo">
                <div class="report-subtitle">Official Maintenance Contract Report</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="report-title">CONTRACT REPORT</div>
                <div class="report-subtitle">Generated on: <?= date('F d, Y H:i'); ?></div>
            </td>
        </tr>
    </table>

    <!-- 1. Company Name & Contract Details -->
    <div class="company-box">
        <table style="width: 100%; border: none;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 50%; border: none; vertical-align: top;">
                    <div style="font-size: 14px; font-weight: 800; color: #0f172a;"><?= htmlspecialchars($organization['name'] ?? 'Organization'); ?></div>
                    <div style="color: #475569; margin-top: 2px;">Email: <?= htmlspecialchars($organization['email'] ?? 'N/A'); ?></div>
                    <div style="color: #475569;">Phone: <?= htmlspecialchars($organization['phone'] ?? 'N/A'); ?></div>
                </td>
                <td style="width: 50%; border: none; vertical-align: top; text-align: right;">
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($contract['contract_name']); ?></div>
                    <div style="margin-top: 2px;">
                        <strong>Type:</strong> <?= ucwords(str_replace('_', ' ', $contract['contract_type'])); ?>
                        &nbsp;|&nbsp;
                        <span class="contract-badge badge-<?= $statusInfo['color']; ?>"><?= htmlspecialchars($statusInfo['status_label']); ?></span>
                    </div>
                    <div style="margin-top: 2px; color: #0f172a; font-weight: 600;">
                        <strong>Contract Period:</strong> <?= date('M d, Y', strtotime($contract['start_date'])); ?> &mdash; <?= date('M d, Y', strtotime($contract['end_date'])); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. List of All Tickets Raised in Contract Period -->
    <div class="section-heading">1. TICKETS RAISED SUMMARY (<?= count($tickets); ?> Total)</div>

    <?php if (empty($tickets)): ?>
        <p style="font-style: italic; color: #64748b;">No support tickets were created by <?= htmlspecialchars($organization['name']); ?> during this contract period.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">Ticket No</th>
                    <th style="width: 30%;">Subject</th>
                    <th style="width: 18%;">Customer User</th>
                    <th style="width: 18%;">Assigned Agent</th>
                    <th style="width: 10%;">Priority</th>
                    <th style="width: 14%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($t['ticket_no'] ?? $t['id']); ?></strong></td>
                        <td><?= htmlspecialchars($t['subject']); ?></td>
                        <td><?= htmlspecialchars($t['customer_name'] ?? 'User'); ?></td>
                        <td><?= htmlspecialchars($t['assigned_agent_name'] ?? 'Unassigned'); ?></td>
                        <td><?= ucfirst($t['priority']); ?></td>
                        <td><strong><?= ucwords(str_replace('_', ' ', $t['status'])); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- 3. Detailed Tickets Report -->
    <?php if (!empty($tickets)): ?>
        <div class="section-heading" style="page-break-before: always;">2. DETAILED TICKET REPORTS</div>

        <?php foreach ($tickets as $index => $t): ?>
            <div class="ticket-detail-box">
                <div class="ticket-detail-header">
                    Ticket #<?= htmlspecialchars($t['ticket_no'] ?? $t['id']); ?>: <?= htmlspecialchars($t['subject']); ?>
                </div>

                <div class="ticket-meta">
                    <strong>Raised By:</strong> <?= htmlspecialchars($t['customer_name'] ?? 'User'); ?> (<?= htmlspecialchars($t['customer_email'] ?? ''); ?>) |
                    <strong>Assigned Agent:</strong> <?= htmlspecialchars($t['assigned_agent_name'] ?? 'Unassigned'); ?> |
                    <strong>Date:</strong> <?= date('M d, Y H:i', strtotime($t['created_at'])); ?> |
                    <strong>Status:</strong> <?= ucwords(str_replace('_', ' ', $t['status'])); ?> |
                    <strong>Priority:</strong> <?= ucfirst($t['priority']); ?>
                </div>

                <div style="margin-top: 6px; font-size: 11px; white-space: pre-wrap;"><strong>Description:</strong><br><?= htmlspecialchars($t['description']); ?></div>

                <?php if (!empty($t['replies'])): ?>
                    <div style="margin-top: 10px; font-weight: 700; font-size: 10.5px; color: #1e293b;">Replies &amp; Updates (<?= count($t['replies']); ?>):</div>
                    <?php foreach ($t['replies'] as $reply): ?>
                        <div class="reply-item">
                            <div style="font-weight: 600; color: #0f172a;">
                                <?= htmlspecialchars($reply['user_name'] ?? 'User'); ?> (<?= ucfirst($reply['user_role'] ?? 'user'); ?>) &bull; <span style="font-weight: normal; color: #64748b;"><?= date('M d, Y H:i', strtotime($reply['created_at'])); ?></span>
                            </div>
                            <div style="margin-top: 2px; white-space: pre-wrap;"><?= htmlspecialchars($reply['message']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
