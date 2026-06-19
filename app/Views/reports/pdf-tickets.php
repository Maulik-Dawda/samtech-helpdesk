<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 12mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .logo {
            height: 55px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .date {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
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
            font-size: 10px;
            border: 1px solid #111827;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #d1d5db;
            font-size: 9.5px;
            word-wrap: break-word;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body>

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
            <th width="13%">Ticket No</th>
            <th width="15%">Organization</th>
            <th width="14%">User</th>
            <th width="24%">Subject</th>
            <th width="9%">Priority</th>
            <th width="10%">Status</th>
            <th width="15%">Created</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="7" style="text-align:center;">
                    No records found.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= htmlspecialchars($ticket['ticket_no']); ?></td>
                    <td><?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($ticket['subject']); ?></td>
                    <td><?= htmlspecialchars(ucfirst($ticket['priority'])); ?></td>
                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?></td>
                    <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    Samtech Solutions
</div>

</body>
</html>