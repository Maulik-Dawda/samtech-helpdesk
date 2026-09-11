<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 22mm 12mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            background: #ffffff;
        }

        /* Letterhead Watermark Logo */
        .watermark-logo {
            position: fixed;
            top: 32%;
            left: 15%;
            width: 420px;
            opacity: 0.10;
            z-index: -1000;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #488a25;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .logo {
            height: 55px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 8px;
            color: #111827;
        }

        .date {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            background: #111827;
            color: #ffffff;
            padding: 7px 5px;
            font-size: 9.5px;
            border: 1px solid #111827;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #d1d5db;
            font-size: 9px;
            word-wrap: break-word;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Letterhead Footer */
        .letterhead-footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            text-align: center;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #334155;
            background: #ffffff;
            padding-top: 4px;
            border-top: 1px solid #cbd5e1;
        }

        .letterhead-footer .lh-company {
            font-weight: bold;
            font-size: 9pt;
            color: #0f172a;
        }

        .letterhead-footer .lh-address {
            font-size: 7pt;
            color: #475569;
            margin-top: 2px;
        }

        .letterhead-footer .lh-contact {
            font-size: 7pt;
            color: #475569;
            margin-top: 1px;
        }
    </style>
</head>

<body>

<!-- Letterhead Background Watermark -->
<?php if (!empty($logoBase64)): ?>
    <img src="<?= $logoBase64; ?>" class="watermark-logo">
<?php endif; ?>

<div class="header">
    <?php if (!empty($logoBase64)): ?>
        <img src="<?= $logoBase64; ?>" class="logo">
    <?php endif; ?>

    <div class="title">Ticket Report</div>
    <div class="date">
        Generated on <?= date('d M Y, h:i A'); ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="5%" style="text-align:center;">Sr. No</th>
            <th width="10%">Ticket No</th>
            <th width="12%">Organization</th>
            <th width="11%">User</th>
            <th width="19%">Subject</th>
            <th width="7%">Priority</th>
            <th width="8%">Status</th>
            <th width="14%">Created</th>
            <th width="14%">Closed Date</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="9" style="text-align:center;">
                    No records found.
                </td>
            </tr>
        <?php else: ?>
            <?php $srNo = 1; ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td style="text-align:center;"><?= $srNo++; ?></td>
                    <td><?= htmlspecialchars($ticket['ticket_no']); ?></td>
                    <td><?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($ticket['subject']); ?></td>
                    <td><?= htmlspecialchars(ucfirst($ticket['priority'])); ?></td>
                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?></td>
                    <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                    <td><?= !empty($ticket['closed_at']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($ticket['closed_at']))) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Official Company Letterhead Footer -->
<div class="letterhead-footer">
    <div class="lh-company">Samvruddhi Technologies LLC</div>
    <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
    <div class="lh-contact">+971-4-3554245 | sales@samvruddhi.com | https://samtech.ae/ | TRN 100324643400003</div>
</div>

</body>
</html>