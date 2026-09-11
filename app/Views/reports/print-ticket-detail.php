<?php
$logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
$logoSrc = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : BASE_URL . '/assets/images/samtech-logo-report.png';

$iconPath = ROOT_PATH . '/public/assets/images/samtech-icon.png';
$iconSrc = file_exists($iconPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($iconPath))
    : BASE_URL . '/assets/images/samtech-icon.png';

$assignedAgent = !empty($ticket['assigned_agent_name']) 
    ? trim($ticket['assigned_agent_name']) 
    : 'Unassigned';

$statusLabel = ucwords(str_replace('_', ' ', $ticket['status'] ?? 'Open'));
$priorityLabel = ucfirst($ticket['priority'] ?? 'Medium');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Samtech-Helpdesk-<?= htmlspecialchars($ticket['ticket_no'] ?? $ticket['id']); ?></title>

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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.5;
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
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: background 0.2s ease;
        }

        .print-btn:hover {
            background: #396e1d;
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
            opacity: 0.12;
            z-index: -1;
            pointer-events: none;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #488a25;
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-collapse: collapse;
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
        }

        .logo {
            width: 170px;
            height: auto;
        }

        .title {
            font-size: 22px;
            font-weight: 800;
            margin-top: 6px;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #64748b;
            font-size: 11px;
            margin-top: 2px;
            font-weight: 500;
        }

        .meta {
            text-align: right;
            font-size: 10.5px;
            color: #475569;
            line-height: 1.6;
        }

        .section {
            margin-bottom: 22px;
            page-break-inside: auto;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            border-left: 4px solid #0f172a;
            padding-left: 10px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            page-break-before: avoid !important;
        }

        table.info-table th {
            width: 22%;
            background: #f8fafc;
            text-align: left;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            color: #334155;
            font-size: 10.5px;
        }

        table.info-table td {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            font-size: 11px;
        }

        .badge-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: #e2e8f0;
            color: #1e293b;
        }

        .description-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 14px 16px;
            line-height: 1.6;
            border-radius: 6px;
            color: #0f172a;
            font-size: 11.5px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .reply-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 14px;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .reply-header-table {
            width: 100%;
            background: #f1f5f9;
            padding: 8px 14px;
            border-bottom: 1px solid #cbd5e1;
            border-collapse: collapse;
        }

        .reply-header-table td {
            font-size: 11px;
        }

        .reply-body {
            padding: 12px 14px;
            line-height: 1.6;
            color: #0f172a;
            font-size: 11px;
            white-space: pre-line;
            word-wrap: break-word;
            text-align: left;
        }

        .timeline-item {
            border-left: 3px solid #334155;
            padding-left: 12px;
            margin-bottom: 12px;
        }

        .attachment-list {
            margin: 6px 0 0 0;
            padding-left: 20px;
            color: #334155;
        }

        .attachment-list li {
            margin-bottom: 3px;
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

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            .print-btn, .print-actions {
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
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.header-table, .section, table.info-table {
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

<!-- Letterhead Background Watermark -->
<img src="<?= $logoSrc; ?>" class="watermark-logo" alt="">

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
                <div class="title">Ticket Detail Report</div>
                <div class="subtitle">Samtech Helpdesk Management System</div>
            </td>
            <td style="border:none; vertical-align:top; text-align:right; padding:0;" class="meta">
                <strong>Generated On:</strong> <?= date('d M Y, h:i A'); ?><br>
                <strong>Generated By:</strong> <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'System Administrator'); ?>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Ticket Information</div>
        <table class="info-table">
            <tr>
                <th>Ticket Number</th>
                <td><strong><?= htmlspecialchars($ticket['ticket_no'] ?? '-'); ?></strong></td>
                <th>Assigned Agent</th>
                <td>
                    <?php if (!empty($ticket['assigned_agent_name'])): ?>
                        <strong><?= htmlspecialchars($ticket['assigned_agent_name']); ?></strong>
                    <?php else: ?>
                        <span style="color:#64748b; font-style:italic;">Unassigned</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Subject</th>
                <td colspan="3"><strong><?= htmlspecialchars($ticket['subject'] ?? '-'); ?></strong></td>
            </tr>
            <tr>
                <th>Organization</th>
                <td><?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?></td>
                <th>Customer / User</th>
                <td><?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?> (<?= htmlspecialchars($ticket['customer_email'] ?? '-'); ?>)</td>
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
                <td><?= !empty($ticket['created_at']) ? htmlspecialchars(date('d M Y, h:i A', strtotime($ticket['created_at']))) : '-'; ?></td>
                <th>Closed Date</th>
                <td><?= !empty($ticket['closed_at']) ? htmlspecialchars(date('d M Y, h:i A', strtotime($ticket['closed_at']))) : '-'; ?></td>
            </tr>
            <?php if (!empty($ticket['closed_by_agent_name'])): ?>
            <tr>
                <th>Closed By Agent</th>
                <td colspan="3"><?= htmlspecialchars($ticket['closed_by_agent_name']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <div class="description-box"><?= nl2br(htmlspecialchars(trim($ticket['description'] ?? 'No description provided.'))); ?></div>
    </div>

    <?php if (!empty($attachments)): ?>
        <div class="section">
            <div class="section-title">Ticket Attachments</div>
            <ul class="attachment-list">
                <?php foreach ($attachments as $attachment): ?>
                    <li>📄 <?= htmlspecialchars($attachment['original_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">Conversation History (<?= count($replies); ?> <?= count($replies) === 1 ? 'Reply' : 'Replies'; ?>)</div>

        <?php if (empty($replies)): ?>
            <div style="color: #64748b; font-style: italic; padding: 6px 0;">No conversation replies logged for this ticket.</div>
        <?php else: ?>
            <?php foreach ($replies as $reply): ?>
                <div class="reply-card">
                    <table class="reply-header-table">
                        <tr>
                            <td style="border:none; text-align:left; padding:0; font-weight:bold; color:#0f172a;">
                                <?= htmlspecialchars($reply['full_name']); ?>
                                <span style="font-weight:normal; color:#475569; font-size:10px;">(<?= ucfirst($reply['role']); ?>)</span>
                            </td>
                            <td style="border:none; text-align:right; padding:0; color:#64748b; font-size:10px;">
                                <?php if (!empty($reply['edit_count']) && (int)$reply['edit_count'] > 0): ?>
                                    <span style="font-weight:bold; color:#475569; font-style:italic; margin-right:4px;">(Edited)</span>
                                <?php endif; ?>
                                🕒 <?= htmlspecialchars($reply['created_at']); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="reply-body"><?= nl2br(htmlspecialchars(trim($reply['message'] ?? ''))); ?></div>

                        <?php if (!empty($replyAttachments[$reply['id']])): ?>
                            <div style="border-top:1px solid #e2e8f0; margin-top:10px; padding-top:8px;">
                                <strong style="font-size:10px; color:#475569;">Attachments:</strong>
                                <ul class="attachment-list">
                                    <?php foreach ($replyAttachments[$reply['id']] as $file): ?>
                                        <li>📎 <?= htmlspecialchars($file['original_name']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($statusHistory)): ?>
    <div class="section">
        <div class="section-title">Status Audit History</div>
        <?php foreach ($statusHistory as $history): ?>
            <div class="timeline-item">
                <strong>
                    <?= ucwords(str_replace('_', ' ', $history['old_status'])); ?>
                    &rarr;
                    <?= ucwords(str_replace('_', ' ', $history['new_status'])); ?>
                </strong>
                <br>
                <span style="color:#475569;"><?= htmlspecialchars($history['full_name']); ?></span>
                &bull;
                <span style="color:#64748b; font-size:10px;"><?= htmlspecialchars($history['created_at']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

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