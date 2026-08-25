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
    <title>Samtech-Helpdesk-<?= htmlspecialchars($ticket['ticket_no'] ?? $ticket['id']); ?></title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 11px;
        }

        .print-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .print-btn {
            background: #b1e96f;
            border: none;
            padding: 9px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #b1e96f;
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-collapse: collapse;
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 6px;
            color: #111827;
        }

        .subtitle {
            color: #64748b;
            font-size: 11px;
            margin-top: 2px;
        }

        .meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: auto;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 5px solid #b1e96f;
            padding-left: 8px;
            color: #111827;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            page-break-before: avoid !important;
        }

        table.info-table th {
            width: 25%;
            background: #f8fafc;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            font-weight: bold;
        }

        table.info-table td {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
        }

        .description-box {
            border: 1px solid #d1d5db;
            background: #fafafa;
            padding: 12px;
            line-height: 1.6;
            border-radius: 6px;
        }

        .reply-card {
            border: 1px solid #dbe3ed;
            border-radius: 8px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .reply-header-table {
            width: 100%;
            background: #f8fafc;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            border-collapse: collapse;
        }

        .reply-body {
            padding: 12px;
            line-height: 1.6;
        }

        .timeline-item {
            border-left: 3px solid #b1e96f;
            padding-left: 12px;
            margin-bottom: 14px;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            color: #64748b;
            font-size: 9.5px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        @media print {
            .print-btn, .print-actions {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
                color: #111827 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.header-table, .section, table.info-table {
                page-break-before: avoid !important;
            }
        }
    </style>
</head>

<body>

    <?php if (empty($isPdfDownload)): ?>
    <div class="print-actions">
        <button onclick="window.print()" class="print-btn">
            Print / Save PDF
        </button>
    </div>
    <?php endif; ?>

    <table class="header-table">
        <tr>
            <td style="border:none; vertical-align:top; text-align:left; padding:0;">
                <img src="<?= $logoSrc; ?>" class="logo" alt="Samtech">
                <div class="title">Ticket Detail Report</div>
                <div class="subtitle">Samtech Helpdesk Management System</div>
            </td>
            <td style="border:none; vertical-align:top; text-align:right; padding:0;" class="meta">
                <strong>Generated On:</strong> <?= date('d M Y h:i A'); ?><br><br>
                <strong>Generated By:</strong> <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'System'); ?>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Ticket Information</div>
        <table class="info-table">
            <tr>
                <th>Ticket Number</th>
                <td><strong><?= htmlspecialchars($ticket['ticket_no'] ?? '-'); ?></strong></td>
            </tr>
            <tr>
                <th>Organization</th>
                <td><?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>User / Customer</th>
                <td><?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($ticket['customer_email'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Subject</th>
                <td><?= htmlspecialchars($ticket['subject'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status'] ?? ''))); ?></td>
            </tr>
            <tr>
                <th>Priority</th>
                <td><?= htmlspecialchars(ucfirst($ticket['priority'] ?? '')); ?></td>
            </tr>
            <tr>
                <th>Created Date</th>
                <td><?= htmlspecialchars($ticket['created_at'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Closed Date</th>
                <td><?= !empty($ticket['closed_at']) ? $ticket['closed_at'] : '-'; ?></td>
            </tr>
            <tr>
                <th>Closed By</th>
                <td><?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <div class="description-box">
            <?= nl2br(htmlspecialchars($ticket['description'] ?? 'No description provided.')); ?>
        </div>
    </div>

    <?php if (!empty($attachments)): ?>
        <div class="section">
            <div class="section-title">Ticket Attachments</div>
            <ul>
                <?php foreach ($attachments as $attachment): ?>
                    <li><?= htmlspecialchars($attachment['original_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">Conversation History</div>

        <?php if (empty($replies)): ?>
            <div style="color: #64748b; font-style: italic;">No Replies Available</div>
        <?php else: ?>
            <?php foreach ($replies as $reply): ?>
                <div class="reply-card">
                    <table class="reply-header-table">
                        <tr>
                            <td style="border:none; text-align:left; padding:0; font-weight:bold;">
                                <?= htmlspecialchars($reply['full_name']); ?> (<?= ucfirst($reply['role']); ?>)
                            </td>
                            <td style="border:none; text-align:right; padding:0; color:#64748b; font-size:10px;">
                                <?= htmlspecialchars($reply['created_at']); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="reply-body">
                        <?= nl2br(htmlspecialchars($reply['message'])); ?>

                        <?php if (!empty($replyAttachments[$reply['id']])): ?>
                            <hr style="border:0; border-top:1px solid #e5e7eb; margin:10px 0;">
                            <strong>Attachments:</strong>
                            <ul>
                                <?php foreach ($replyAttachments[$reply['id']] as $file): ?>
                                    <li><?= htmlspecialchars($file['original_name']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="section">
        <div class="section-title">Status Timeline</div>

        <?php if (empty($statusHistory)): ?>
            <div style="color: #64748b; font-style: italic;">No Status History</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>

    <div class="footer">
        This report was automatically generated by Samtech Helpdesk.
    </div>

    <?php if (empty($isPdfDownload)): ?>
    <script>
        function triggerSafePrint() {
            var images = Array.from(document.images);
            var promises = images.map(function(img) {
                if (img.complete) return Promise.resolve();
                return new Promise(function(resolve) {
                    img.onload = resolve;
                    img.onerror = resolve;
                });
            });

            Promise.all(promises).then(function() {
                setTimeout(function() {
                    window.print();
                }, 350);
            });
        }

        if (document.readyState === "complete") {
            triggerSafePrint();
        } else {
            window.addEventListener("load", triggerSafePrint);
        }
    </script>
    <?php endif; ?>

</body>
</html>