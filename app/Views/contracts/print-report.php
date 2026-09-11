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
    <title>Contract Report - <?= htmlspecialchars($organization['name'] ?? 'Organization'); ?> (<?= htmlspecialchars($contract['contract_name']); ?>)</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 8mm 8mm 28mm 8mm;
            box-sizing: border-box;
            width: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.5;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            .print-actions {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
            }
            footer, .footer {
                display: none !important;
            }
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 8mm 8mm 28mm 8mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .letterhead-footer {
                position: fixed;
                bottom: 6mm !important;
                left: 8mm !important;
                right: 8mm !important;
                padding: 6px 0 0 0 !important;
                background: transparent !important;
            }
        }

        /* Letterhead Watermark Logo */
        .watermark-logo {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            max-width: 80%;
            height: auto;
            opacity: 0.15;
            z-index: -1;
            pointer-events: none;
        }

        .print-actions {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
        }

        .print-btn {
            background: #488a25;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: background 0.2s ease;
        }

        .print-btn:hover {
            background: #396e1d;
        }

        /* Container table for page-repeat header */
        table.report-container {
            width: 100%;
            border-collapse: collapse;
            border: none;
            background: transparent !important;
        }

        table.report-container > thead > tr > td {
            border: none;
            padding-bottom: 12px;
            background: transparent !important;
        }

        table.report-container > tbody > tr > td {
            border: none;
            padding: 0;
            padding-bottom: 60px;
            background: transparent !important;
        }

        /* Letterhead Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #488a25;
            padding-bottom: 14px;
            margin-bottom: 16px;
            border-collapse: collapse;
            background: transparent !important;
        }

        .header-table td {
            vertical-align: top !important;
            background: transparent !important;
        }

        .logo {
            height: 62px;
            width: auto;
            max-width: 240px;
            display: block;
            margin-top: 0;
            margin-bottom: 8px;
        }

        /* Letterhead Footer */
        .letterhead-footer {
            position: fixed;
            bottom: 6mm;
            left: 8mm;
            right: 8mm;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            color: #334155;
            background: transparent !important;
            padding-top: 6px;
            padding-bottom: 0;
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

        .header-date {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-align: right;
            vertical-align: top;
        }

        .company-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
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
        .badge-gray { background: #f1f5f9; color: #475569; }

        .section-heading {
            font-size: 13px;
            font-weight: 800;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 12px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Summary Table with strict 7 columns */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-table th, .summary-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
            vertical-align: top;
            font-size: 10.5px;
        }

        .summary-table th {
            background: #f1f5f9;
            font-weight: 700;
            color: #1e293b;
        }

        /* Detailed Ticket Section Styling (matching print-ticket-detail.php) */
        .ticket-detail-block {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
            border-left: 4px solid #0f172a;
            padding-left: 8px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.info-table th {
            width: 20%;
            background: #f8fafc;
            text-align: left;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            color: #334155;
            font-size: 10px;
        }

        table.info-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            font-size: 10.5px;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: #e2e8f0;
            color: #1e293b;
        }

        .description-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 10px 12px;
            line-height: 1.5;
            border-radius: 6px;
            color: #0f172a;
            font-size: 10.5px;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 12px;
        }

        .reply-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .reply-header-table {
            width: 100%;
            background: #f1f5f9;
            padding: 6px 10px;
            border-bottom: 1px solid #cbd5e1;
            border-collapse: collapse;
        }

        .reply-header-table td {
            font-size: 10px;
        }

        .reply-body {
            padding: 10px 12px;
            line-height: 1.5;
            color: #0f172a;
            font-size: 10.5px;
            white-space: pre-line;
            word-wrap: break-word;
            text-align: left;
        }

        .timeline-item {
            border-left: 3px solid #334155;
            padding-left: 10px;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .attachment-list {
            margin: 4px 0 0 0;
            padding-left: 18px;
            color: #334155;
            font-size: 10px;
        }

        .attachment-list li {
            margin-bottom: 2px;
        }

        .ticket-divider {
            border-top: 2px dashed #cbd5e1;
            margin: 25px 0;
        }
    </style>
</head>
<body>

<!-- Letterhead Background Watermark -->
<img src="<?= $logoSrc; ?>" class="watermark-logo" alt="">

    <div class="print-actions">
        <button onclick="window.print();" class="print-btn">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <!-- Main Container Table for Repeating Letterhead Header -->
    <table class="report-container">
        <thead>
            <tr>
                <td>
                    <!-- Letterhead Header (Repeated on top of every page) -->
                    <table class="header-table">
                        <tr>
                            <td style="border: none; text-align: left; vertical-align: middle; padding: 0;">
                                <img src="<?= $logoSrc; ?>" alt="Samtech Solutions" class="logo">
                            </td>
                            <td style="border: none; text-align: right; vertical-align: middle; padding: 0;" class="header-date">
                                <strong>Print Date:</strong> <?= date('F d, Y'); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>

                    <!-- 1. Company Overview Box -->
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

                    <!-- 2. Summary Tickets Table -->
                    <div class="section-heading">1. TICKETS RAISED SUMMARY (<?= count($tickets); ?> Total)</div>

                    <?php if (empty($tickets)): ?>
                        <p style="font-style: italic; color: #64748b;">No support tickets were created by <?= htmlspecialchars($organization['name']); ?> during this contract period.</p>
                    <?php else: ?>
                        <table class="summary-table">
                            <thead>
                                <tr>
                                    <th style="width: 11%;">Ticket Number</th>
                                    <th style="width: 22%;">Subject</th>
                                    <th style="width: 14%;">Customer</th>
                                    <th style="width: 14%;">Assigned Agent</th>
                                    <th style="width: 12%;">Open Date</th>
                                    <th style="width: 12%;">Close Date</th>
                                    <th style="width: 15%;">Resolution Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $t): ?>
                                    <tr>
                                        <td><strong>#<?= htmlspecialchars($t['ticket_no'] ?? $t['id']); ?></strong></td>
                                        <td><?= htmlspecialchars($t['subject']); ?></td>
                                        <td><?= htmlspecialchars($t['customer_name'] ?? 'User'); ?></td>
                                        <td><?= htmlspecialchars($t['assigned_agent_name'] ?? 'Unassigned'); ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($t['created_at'])); ?></td>
                                        <td><?= htmlspecialchars($t['effective_closed_at'] ?? '-'); ?></td>
                                        <td style="word-break: break-word;"><?= htmlspecialchars($t['resolution_message'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- 3. Detailed Tickets Section (Matching print-ticket-detail.php structure) -->
                    <?php if (!empty($tickets)): ?>
                        <div class="section-heading" style="page-break-before: always;">2. DETAILED TICKET REPORTS</div>

                        <?php foreach ($tickets as $index => $t): ?>
                            <?php
                            $statusLabel = ucwords(str_replace('_', ' ', $t['status'] ?? 'Open'));
                            $priorityLabel = ucfirst($t['priority'] ?? 'Medium');
                            $replies = $t['replies'] ?? [];
                            $statusHistory = $t['statusHistory'] ?? [];
                            $attachments = $t['attachments'] ?? [];
                            $replyAttachments = $t['replyAttachments'] ?? [];
                            ?>

                            <div class="ticket-detail-block">
                                <div class="section-title">Ticket Information &mdash; #<?= htmlspecialchars($t['ticket_no'] ?? $t['id']); ?></div>
                                <table class="info-table">
                                    <tr>
                                        <th>Ticket Number</th>
                                        <td><strong><?= htmlspecialchars($t['ticket_no'] ?? '-'); ?></strong></td>
                                        <th>Assigned Agent</th>
                                        <td>
                                            <?php if (!empty($t['assigned_agent_name'])): ?>
                                                <strong><?= htmlspecialchars($t['assigned_agent_name']); ?></strong>
                                            <?php else: ?>
                                                <span style="color:#64748b; font-style:italic;">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Subject</th>
                                        <td colspan="3"><strong><?= htmlspecialchars($t['subject'] ?? '-'); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Organization</th>
                                        <td><?= htmlspecialchars($t['organization_name'] ?? $organization['name']); ?></td>
                                        <th>Customer / User</th>
                                        <td><?= htmlspecialchars($t['customer_name'] ?? '-'); ?> (<?= htmlspecialchars($t['customer_email'] ?? '-'); ?>)</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge-pill"><?= htmlspecialchars($statusLabel); ?></span>
                                        </td>
                                        <th>Priority</th>
                                        <td>
                                            <span class="badge-pill"><?= htmlspecialchars($priorityLabel); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created Date</th>
                                        <td><?= date('M d, Y H:i', strtotime($t['created_at'])); ?></td>
                                        <th>Closed Date</th>
                                        <td><?= htmlspecialchars($t['effective_closed_at'] ?? '-'); ?></td>
                                    </tr>
                                    <?php if (!empty($t['closed_by_agent_name'])): ?>
                                    <tr>
                                        <th>Closed By Agent</th>
                                        <td colspan="3"><?= htmlspecialchars($t['closed_by_agent_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>

                                <!-- Description -->
                                <div class="section-title">Description</div>
                                <div class="description-box"><?= nl2br(htmlspecialchars(trim($t['description'] ?? 'No description provided.'))); ?></div>

                                <!-- Ticket Attachments -->
                                <?php if (!empty($attachments)): ?>
                                    <div class="section-title">Ticket Attachments</div>
                                    <ul class="attachment-list" style="margin-bottom: 12px;">
                                        <?php foreach ($attachments as $attachment): ?>
                                            <li>📄 <?= htmlspecialchars($attachment['original_name']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <!-- Conversation History -->
                                <div class="section-title">Conversation History (<?= count($replies); ?> <?= count($replies) === 1 ? 'Reply' : 'Replies'; ?>)</div>

                                <?php if (empty($replies)): ?>
                                    <div style="color: #64748b; font-style: italic; padding: 4px 0 10px 0; font-size: 10px;">No conversation replies logged for this ticket.</div>
                                <?php else: ?>
                                    <?php foreach ($replies as $reply): ?>
                                        <div class="reply-card">
                                            <table class="reply-header-table">
                                                <tr>
                                                    <td style="border:none; text-align:left; padding:0; font-weight:bold; color:#0f172a;">
                                                        <?= htmlspecialchars($reply['full_name'] ?? $reply['user_name'] ?? 'User'); ?>
                                                        <span style="font-weight:normal; color:#475569; font-size:9.5px;">(<?= ucfirst($reply['role'] ?? $reply['user_role'] ?? 'user'); ?>)</span>
                                                    </td>
                                                    <td style="border:none; text-align:right; padding:0; color:#64748b; font-size:9.5px;">
                                                        <?php if (!empty($reply['edit_count']) && (int)$reply['edit_count'] > 0): ?>
                                                            <span style="font-weight:bold; color:#475569; font-style:italic; margin-right:4px;">(Edited)</span>
                                                        <?php endif; ?>
                                                        🕒 <?= date('M d, Y H:i', strtotime($reply['created_at'])); ?>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div class="reply-body"><?= nl2br(htmlspecialchars(trim($reply['message'] ?? ''))); ?></div>

                                            <?php if (!empty($replyAttachments[$reply['id']])): ?>
                                                <div style="border-top:1px solid #e2e8f0; margin-top:6px; padding:6px 12px;">
                                                    <strong style="font-size:9.5px; color:#475569;">Attachments:</strong>
                                                    <ul class="attachment-list">
                                                        <?php foreach ($replyAttachments[$reply['id']] as $file): ?>
                                                            <li>📎 <?= htmlspecialchars($file['original_name']); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Status Audit History -->
                                <?php if (!empty($statusHistory)): ?>
                                    <div class="section-title" style="margin-top: 10px;">Status Audit History</div>
                                    <?php foreach ($statusHistory as $history): ?>
                                        <div class="timeline-item">
                                            <strong>
                                                <?= ucwords(str_replace('_', ' ', $history['old_status'])); ?>
                                                &rarr;
                                                <?= ucwords(str_replace('_', ' ', $history['new_status'])); ?>
                                            </strong>
                                            &bull;
                                            <span style="color:#475569;"><?= htmlspecialchars($history['full_name']); ?></span>
                                            &bull;
                                            <span style="color:#64748b; font-size:9.5px;"><?= date('M d, Y H:i', strtotime($history['created_at'])); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>

                            <?php if ($index < count($tickets) - 1): ?>
                                <div class="ticket-divider"></div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>

                </td>
            </tr>
        </tbody>
    </table>

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
            }, 300);
        });
    </script>

</body>
</html>
