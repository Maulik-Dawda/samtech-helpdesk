<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 30px;
            font-size: 12px;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #b1e96f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 16px;
            color: #374151;
        }

        .meta {
            text-align: right;
            color: #6b7280;
            font-size: 11px;
        }

        .summary {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 8px;
            min-width: 120px;
        }

        .summary-box strong {
            display: block;
            font-size: 18px;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-open {
            background: #dcfce7;
            color: #15803d;
        }

        .status-in-progress {
            background: #ede9fe;
            color: #6d28d9;
        }

        .status-pending {
            background: #fce7f3;
            color: #be185d;
        }

        .status-resolved {
            background: #ffedd5;
            color: #c2410c;
        }

        .status-closed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .priority-low {
            background: #e5e7eb;
            color: #374151;
        }

        .priority-medium {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .priority-high {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-urgent {
            background: #fee2e2;
            color: #b91c1c;
        }

        .print-actions {
            margin-bottom: 20px;
        }

        .print-btn {
            background: #b1e96f;
            border: none;
            padding: 9px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .footer-note {
            margin-top: 25px;
            color: #6b7280;
            font-size: 11px;
            text-align: center;
        }

        @media print {
            .print-actions {
                display: none;
            }

            body {
                margin: 15px;
            }

            @page {
                size: A4 landscape;
                margin: 12mm;
            }
        }
    </style>
</head>

<body>

    <div class="print-actions">
        <button onclick="window.print()" class="print-btn">
            Print Report
        </button>
    </div>

    <div class="report-header">

        <div>
            <div class="company-title">
                Samtech Helpdesk
            </div>

            <div class="report-title">
                Ticket Report
            </div>
        </div>

        <div class="meta">
            Generated On:<br>
            <?= date('d M Y, h:i A'); ?>
            <br><br>
            Total Records:<br>
            <?= count($tickets); ?>
        </div>

    </div>

    <div class="summary">

        <div class="summary-box">
            Total Tickets
            <strong><?= count($tickets); ?></strong>
        </div>

        <div class="summary-box">
            Open
            <strong>
                <?= count(array_filter($tickets, fn($t) => $t['status'] === 'open')); ?>
            </strong>
        </div>

        <div class="summary-box">
            Pending
            <strong>
                <?= count(array_filter($tickets, fn($t) => $t['status'] === 'pending')); ?>
            </strong>
        </div>

        <div class="summary-box">
            Resolved
            <strong>
                <?= count(array_filter($tickets, fn($t) => $t['status'] === 'resolved')); ?>
            </strong>
        </div>

        <div class="summary-box">
            Closed
            <strong>
                <?= count(array_filter($tickets, fn($t) => $t['status'] === 'closed')); ?>
            </strong>
        </div>

    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket No</th>
                <th>Organization</th>
                <th>User</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th>Closed At</th>
                <th>Closed By</th>
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

                <?php foreach($tickets as $ticket): ?>

                    <?php
                        $statusClass = match($ticket['status']) {
                            'open' => 'status-open',
                            'in_progress' => 'status-in-progress',
                            'pending' => 'status-pending',
                            'resolved' => 'status-resolved',
                            'closed' => 'status-closed',
                            default => 'status-open'
                        };

                        $priorityClass = match($ticket['priority']) {
                            'low' => 'priority-low',
                            'medium' => 'priority-medium',
                            'high' => 'priority-high',
                            'urgent' => 'priority-urgent',
                            default => 'priority-low'
                        };
                    ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($ticket['ticket_no']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['organization_name'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['customer_name'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['subject']); ?>
                        </td>

                        <td>
                            <span class="badge <?= $priorityClass; ?>">
                                <?= htmlspecialchars(ucfirst($ticket['priority'])); ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge <?= $statusClass; ?>">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status']))); ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['created_at']); ?>
                        </td>

                        <td>
                            <?= !empty($ticket['closed_at'])
                                ? htmlspecialchars($ticket['closed_at'])
                                : '-'; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($ticket['closed_by_agent_name'] ?? '-'); ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

    <div class="footer-note">
        This is a system generated report from Samtech Helpdesk.
    </div>

    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>

</body>
</html>