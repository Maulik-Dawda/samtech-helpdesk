<?php

require_once ROOT_PATH . "/app/Core/Controller.php";
require_once ROOT_PATH . "/app/Middleware/AuthMiddleware.php";
require_once ROOT_PATH . "/app/Models/Ticket.php";
require_once ROOT_PATH . "/app/Models/User.php";
require_once ROOT_PATH . "/app/Models/Organization.php";
require_once ROOT_PATH . "/app/Models/TicketReply.php";
require_once ROOT_PATH . "/app/Models/TicketStatusHistory.php";
require_once ROOT_PATH . "/app/Models/Attachment.php";

class PdfController extends Controller
{
    private function authGuard()
    {
        AuthMiddleware::timeout();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['auth_user_role'] ?? '';

        if (!in_array($role, ['admin', 'agent'])) {
            http_response_code(403);
            exit('Unauthorized');
        }
    }

    private function getFilters()
    {
        return [
            'organization_id' => trim($_GET['organization_id'] ?? ''),
            'user_id'         => trim($_GET['user_id'] ?? ''),
            'agent_id'        => trim($_GET['agent_id'] ?? ''),
            'status'         => trim($_GET['status'] ?? ''),
            'priority'       => trim($_GET['priority'] ?? ''),
            'date_from'      => trim($_GET['date_from'] ?? ''),
            'date_to'        => trim($_GET['date_to'] ?? ''),
            'search'         => trim($_GET['search'] ?? '')
        ];
    }

    public function downloadTicketsPdf()
    {
        $this->authGuard();

        $filters = $this->getFilters();
        $ticketModel = new Ticket();
        $userModel = new User();
        $organizationModel = new Organization();

        $tickets = $ticketModel->getReportTickets($filters);
        $organizations = $organizationModel->getAllActive();
        $users = $userModel->getUsersByRole('user');
        $agents = $userModel->getUsersByRole('agent');

        $randomNum = rand(100000, 900000);
        $filename = "Samtech-Helpdesk-" . $randomNum . ".pdf";

        $html = $this->buildTicketsPdfHtml($tickets, $filters, $organizations, $users, $agents);

        $this->outputPdf($html, $filename);
    }

    public function downloadTicketDetailPdf($id)
    {
        $this->authGuard();

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findByIdForReport($id);

        if (!$ticket) {
            http_response_code(404);
            exit('Ticket not found');
        }

        $replyModel = new TicketReply();
        $historyModel = new TicketStatusHistory();
        $attachmentModel = new Attachment();

        $replies = $replyModel->getByTicketId($ticket['id']);
        $statusHistory = $historyModel->getByTicketId($ticket['id']);
        $attachments = $attachmentModel->getTicketAttachments($ticket['id']);
        $replyAttachments = $attachmentModel->getReplyAttachmentsByTicketId($ticket['id']);

        $ticketIdentifier = !empty($ticket['ticket_no']) ? $ticket['ticket_no'] : $ticket['id'];
        $filename = "Samtech-Helpdesk-" . $ticketIdentifier . ".pdf";

        $html = $this->buildTicketDetailPdfHtml($ticket, $replies, $statusHistory, $attachments, $replyAttachments);

        $this->outputPdf($html, $filename);
    }

    private function outputPdf($html, $filename)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->set('chroot', [ROOT_PATH, sys_get_temp_dir()]);
        $options->set('tempDir', sys_get_temp_dir());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfBinary = $dompdf->output();

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Length: " . strlen($pdfBinary));
        header("Cache-Control: private, max-age=0, must-revalidate");
        header("Pragma: public");

        echo $pdfBinary;
        exit;
    }

    private function buildTicketsPdfHtml($tickets, $filters, $organizations, $users, $agents)
    {
        $logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';

        $criteria = [];
        if (!empty($filters['organization_id']) && !empty($organizations)) {
            foreach ($organizations as $o) {
                if ($o['id'] == $filters['organization_id']) {
                    $criteria[] = '<strong>Organization:</strong> ' . htmlspecialchars($o['name']);
                    break;
                }
            }
        }
        if (!empty($filters['user_id']) && !empty($users)) {
            foreach ($users as $u) {
                if ($u['id'] == $filters['user_id']) {
                    $criteria[] = '<strong>User:</strong> ' . htmlspecialchars($u['full_name']);
                    break;
                }
            }
        }
        if (!empty($filters['agent_id']) && !empty($agents)) {
            foreach ($agents as $a) {
                if ($a['id'] == $filters['agent_id']) {
                    $criteria[] = '<strong>Agent:</strong> ' . htmlspecialchars($a['full_name']);
                    break;
                }
            }
        }
        if (!empty($filters['status'])) {
            $criteria[] = '<strong>Status:</strong> ' . htmlspecialchars(ucwords(str_replace('_', ' ', $filters['status'])));
        }
        if (!empty($filters['priority'])) {
            $criteria[] = '<strong>Priority:</strong> ' . htmlspecialchars(ucfirst($filters['priority']));
        }
        if (!empty($filters['date_from'])) {
            $criteria[] = '<strong>From:</strong> ' . htmlspecialchars($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $criteria[] = '<strong>To:</strong> ' . htmlspecialchars($filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $criteria[] = '<strong>Search:</strong> ' . htmlspecialchars($filters['search']);
        }

        $criteriaHtml = !empty($criteria)
            ? '<div style="margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; font-size:9pt; color:#374151;">' . implode(' &nbsp;|&nbsp; ', $criteria) . '</div>'
            : '';

        $rowsHtml = '';
        if (empty($tickets)) {
            $rowsHtml = '<tr><td colspan="9" style="text-align:center; padding:18px; color:#64748b;">No tickets found matching criteria.</td></tr>';
        } else {
            foreach ($tickets as $t) {
                $rowsHtml .= '<tr>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;"><strong>' . htmlspecialchars($t['ticket_no'] ?? '-') . '</strong></td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars($t['organization_name'] ?? '-') . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars($t['customer_name'] ?? '-') . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars($t['assigned_agent_name'] ?? '-') . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars($t['subject'] ?? '-') . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars(ucfirst($t['priority'] ?? '')) . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars(ucwords(str_replace('_', ' ', $t['status'] ?? ''))) . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . (!empty($t['created_at']) ? date('Y-m-d H:i', strtotime($t['created_at'])) : '-') . '</td>
                    <td style="padding:6px; border:1px solid #d1d5db; font-size:8.5pt;">' . htmlspecialchars($t['closed_by_agent_name'] ?? '-') . '</td>
                </tr>';
            }
        }

        $logoHtml = $logoSrc !== ''
            ? '<img src="' . $logoSrc . '" style="width:170px; height:auto;" alt="Samtech">'
            : '<div style="font-size:18pt; font-weight:bold; color:#111827;">Samtech Solutions</div>';

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9pt; color: #111827; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <table style="width:100%; border-bottom:3px solid #b1e96f; padding-bottom:10px; margin-bottom:14px;">
        <tr>
            <td style="vertical-align:top; text-align:left;">
                ' . $logoHtml . '
                <div style="font-size:18pt; font-weight:bold; color:#111827; margin-top:4px;">Ticket Report</div>
                <div style="font-size:9pt; color:#64748b;">Samtech Helpdesk Management System</div>
            </td>
            <td style="vertical-align:top; text-align:right; font-size:9pt; color:#475569; line-height:1.5;">
                <strong>Generated On:</strong> ' . date('d M Y, h:i A') . '<br>
                <strong>Generated By:</strong> ' . htmlspecialchars($_SESSION['auth_user_name'] ?? 'System') . '<br>
                <strong>Total Records:</strong> ' . count($tickets) . '
            </td>
        </tr>
    </table>

    ' . $criteriaHtml . '

    <table style="width:100%; border-collapse:collapse; margin-top:8px;">
        <thead>
            <tr style="background:#111827; color:#ffffff;">
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:11%;">Ticket No</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:12%;">Organization</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:10%;">User</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:10%;">Assigned Agent</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:22%;">Subject</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:7%;">Priority</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:8%;">Status</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:10%;">Created</th>
                <th style="padding:7px 6px; border:1px solid #111827; font-size:9pt; text-align:left; width:10%;">Closed By</th>
            </tr>
        </thead>
        <tbody>
            ' . $rowsHtml . '
        </tbody>
    </table>

    <div style="margin-top:18px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:8pt; color:#64748b; text-align:center;">
        Samtech Helpdesk • System Generated Report
    </div>
</body>
</html>';
    }

    private function buildTicketDetailPdfHtml($ticket, $replies, $statusHistory, $attachments, $replyAttachments)
    {
        $logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';

        $logoHtml = $logoSrc !== ''
            ? '<img src="' . $logoSrc . '" style="width:170px; height:auto;" alt="Samtech">'
            : '<div style="font-size:18pt; font-weight:bold; color:#111827;">Samtech Solutions</div>';

        $repliesHtml = '';
        if (empty($replies)) {
            $repliesHtml = '<div style="color:#64748b; font-style:italic;">No Replies Available</div>';
        } else {
            foreach ($replies as $r) {
                $repliesHtml .= '<div style="border:1px solid #dbe3ed; border-radius:6px; margin-bottom:12px; padding:10px;">
                    <div style="background:#f8fafc; padding:6px 10px; border-bottom:1px solid #e5e7eb; font-weight:bold; font-size:9pt;">
                        ' . htmlspecialchars($r['full_name']) . ' (' . ucfirst($r['role']) . ') - <span style="font-weight:normal; color:#64748b; font-size:8pt;">' . htmlspecialchars($r['created_at']) . '</span>
                    </div>
                    <div style="padding:8px; font-size:9pt; line-height:1.5;">
                        ' . nl2br(htmlspecialchars($r['message'])) . '
                    </div>
                </div>';
            }
        }

        $timelineHtml = '';
        if (empty($statusHistory)) {
            $timelineHtml = '<div style="color:#64748b; font-style:italic;">No Status History</div>';
        } else {
            foreach ($statusHistory as $h) {
                $timelineHtml .= '<div style="border-left:3px solid #b1e96f; padding-left:10px; margin-bottom:10px; font-size:9pt;">
                    <strong>' . ucwords(str_replace('_', ' ', $h['old_status'])) . ' &rarr; ' . ucwords(str_replace('_', ' ', $h['new_status'])) . '</strong><br>
                    <span style="color:#475569;">' . htmlspecialchars($h['full_name']) . '</span> &bull; <span style="color:#64748b; font-size:8pt;">' . htmlspecialchars($h['created_at']) . '</span>
                </div>';
            }
        }

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10pt; color: #111827; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 7px 10px; border: 1px solid #d1d5db; font-weight: bold; width: 25%; }
        td { padding: 7px 10px; border: 1px solid #d1d5db; }
    </style>
</head>
<body>
    <table style="width:100%; border-bottom:3px solid #b1e96f; padding-bottom:10px; margin-bottom:16px;">
        <tr>
            <td style="vertical-align:top; text-align:left; border:none;">
                ' . $logoHtml . '
                <div style="font-size:20pt; font-weight:bold; color:#111827; margin-top:4px;">Ticket Detail Report</div>
                <div style="font-size:9.5pt; color:#64748b;">Samtech Helpdesk Management System</div>
            </td>
            <td style="vertical-align:top; text-align:right; font-size:9pt; color:#475569; line-height:1.5; border:none;">
                <strong>Generated On:</strong> ' . date('d M Y h:i A') . '<br><br>
                <strong>Generated By:</strong> ' . htmlspecialchars($_SESSION['auth_user_name'] ?? 'System') . '
            </td>
        </tr>
    </table>

    <div style="margin-bottom:16px;">
        <div style="font-size:12pt; font-weight:bold; margin-bottom:8px; border-left:5px solid #b1e96f; padding-left:8px;">Ticket Information</div>
        <table>
            <tr><th>Ticket Number</th><td><strong>' . htmlspecialchars($ticket['ticket_no'] ?? '-') . '</strong></td></tr>
            <tr><th>Organization</th><td>' . htmlspecialchars($ticket['organization_name'] ?? '-') . '</td></tr>
            <tr><th>User / Customer</th><td>' . htmlspecialchars($ticket['customer_name'] ?? '-') . '</td></tr>
            <tr><th>Email</th><td>' . htmlspecialchars($ticket['customer_email'] ?? '-') . '</td></tr>
            <tr><th>Subject</th><td>' . htmlspecialchars($ticket['subject'] ?? '-') . '</td></tr>
            <tr><th>Status</th><td>' . htmlspecialchars(ucwords(str_replace('_', ' ', $ticket['status'] ?? ''))) . '</td></tr>
            <tr><th>Priority</th><td>' . htmlspecialchars(ucfirst($ticket['priority'] ?? '')) . '</td></tr>
            <tr><th>Created Date</th><td>' . htmlspecialchars($ticket['created_at'] ?? '-') . '</td></tr>
            <tr><th>Closed Date</th><td>' . (!empty($ticket['closed_at']) ? $ticket['closed_at'] : '-') . '</td></tr>
            <tr><th>Closed By</th><td>' . htmlspecialchars($ticket['closed_by_agent_name'] ?? '-') . '</td></tr>
        </table>
    </div>

    <div style="margin-bottom:16px;">
        <div style="font-size:12pt; font-weight:bold; margin-bottom:8px; border-left:5px solid #b1e96f; padding-left:8px;">Description</div>
        <div style="border:1px solid #d1d5db; background:#fafafa; padding:10px; line-height:1.6; border-radius:4px;">
            ' . nl2br(htmlspecialchars($ticket['description'] ?? 'No description provided.')) . '
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <div style="font-size:12pt; font-weight:bold; margin-bottom:8px; border-left:5px solid #b1e96f; padding-left:8px;">Conversation History</div>
        ' . $repliesHtml . '
    </div>

    <div style="margin-bottom:16px;">
        <div style="font-size:12pt; font-weight:bold; margin-bottom:8px; border-left:5px solid #b1e96f; padding-left:8px;">Status Timeline</div>
        ' . $timelineHtml . '
    </div>

    <div style="margin-top:20px; padding-top:10px; border-top:1px solid #e5e7eb; font-size:8.5pt; color:#64748b; text-align:center;">
        This report was automatically generated by Samtech Helpdesk.
    </div>
</body>
</html>';
    }
}
