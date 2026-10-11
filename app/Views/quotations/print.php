<?php
$cover = $proposalData['cover'] ?? [];
$letter = $proposalData['letter'] ?? [];
$about = $proposalData['about'] ?? [];
$custom = $proposalData['custom_pages'] ?? [];
$fin = $proposalData['financials'] ?? [];
$sign = $proposalData['signoff'] ?? [];

$logoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
$logoSrc = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : BASE_URL . '/assets/images/samtech-logo-report.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation Proposal - <?= htmlspecialchars($quotation['quotation_no']); ?> - <?= htmlspecialchars($quotation['company_name']); ?></title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0mm !important;
        }

        *, *:before, *:after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #f1f5f9;
            font-size: 10pt;
            line-height: 1.5;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-actions {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
            }

            .page-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                padding: 10mm 12mm 8mm 12mm !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }

            .page-sheet:last-of-type {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .letterhead-footer {
                position: relative !important;
                flex: 0 0 auto !important;
                margin-top: auto !important;
                bottom: 0 !important;
                background: transparent !important;
            }
        }

        /* Screen Presentation Sheet */
        .page-sheet {
            background: #ffffff;
            width: 210mm;
            height: 297mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 20px auto;
            padding: 10mm 12mm 8mm 12mm;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            page-break-after: always;
            break-after: page;
        }

        .sheet-content {
            position: relative;
            z-index: 1;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .sheet-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
        }

        /* Fixed Print Actions Toolbar */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background: #488a25;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #396e1d;
        }

        .btn-back {
            background: #334155;
            color: #ffffff;
        }

        .btn-back:hover {
            background: #1e293b;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #488a25;
            padding-bottom: 8px;
            margin-bottom: 14px;
            border-collapse: collapse;
            background: transparent !important;
            flex: 0 0 auto;
        }

        .header-table td {
            vertical-align: top;
            background: transparent !important;
        }

        .logo {
            height: 48px;
            width: auto;
            max-width: 200px;
            display: block;
        }

        /* Official Letterhead Footer */
        .letterhead-footer {
            position: relative;
            z-index: 10;
            flex: 0 0 auto;
            width: 100%;
            margin-top: auto;
            padding-top: 5px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            color: #334155;
            background: transparent !important;
            line-height: 1.25;
        }

        .letterhead-footer .lh-company {
            font-weight: 800;
            font-size: 8.5pt;
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

        /* Typography & Styles */
        .cover-title {
            text-align: center;
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-top: 18px;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .cover-meta {
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            color: #1e293b;
            margin-top: 24px;
            margin-bottom: 26px;
            line-height: 1.5;
        }

        .party-block {
            text-align: center;
            margin-bottom: 26px;
            line-height: 1.6;
        }

        .party-title {
            font-size: 12pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .party-company {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .party-details {
            font-size: 10pt;
            color: #1e293b;
        }

        .party-email a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 600;
        }

        .section-heading-main {
            font-size: 14pt;
            font-weight: 800;
            color: #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            display: inline-block;
            padding-bottom: 3px;
            margin-bottom: 12px;
            margin-top: 4px;
        }

        .section-subheading {
            font-size: 11pt;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .proposal-text {
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 8px;
        }

        /* Financials Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
            font-size: 9pt;
        }

        .data-table th {
            background-color: #205493;
            color: #ffffff;
            font-weight: 700;
            padding: 7px 9px;
            border: 1px solid #1e40af;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 6px 9px;
            border: 1px solid #94a3b8;
            vertical-align: middle;
            color: #0f172a;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .data-table tfoot td {
            font-weight: 700;
            background-color: #ffffff !important;
            border: 1px solid #94a3b8;
            padding: 6px 9px;
        }

        .data-table tfoot tr.grand-total td {
            background-color: #f0fdf4 !important;
            color: #166534;
            font-size: 10pt;
            font-weight: 800;
        }

        .signatory-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            margin-bottom: 18px;
        }

        .signatory-box td {
            width: 50%;
            vertical-align: top;
            padding: 8px 12px;
            font-size: 9.5pt;
            line-height: 1.6;
        }

        .closing-banner {
            text-align: center;
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 24px;
            padding-top: 14px;
        }
    </style>
</head>
<body>

    <!-- Floating Actions Toolbar -->
    <div class="print-actions">
        <a href="<?= BASE_URL ?>/quotations/show/<?= $quotation['id']; ?>" class="btn-action btn-back">
            &larr; Back to Quotation
        </a>
        <button type="button" class="btn-action btn-print" onclick="doPrint()">
            &#128438; Print / Save as PDF
        </button>
    </div>

    <!-- ========================================== -->
    <!-- PAGE 1: COVER PAGE                         -->
    <!-- ========================================== -->
    <div class="page-sheet">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="sheet-body d-flex flex-column justify-content-around">
                <div class="cover-title">
                    QUOTATION PROPOSAL<br>
                    FOR <?= htmlspecialchars(strtoupper($quotation['product_name'])); ?><br>
                    FOR <?= htmlspecialchars(strtoupper($quotation['company_name'])); ?>
                </div>

                <div class="cover-meta">
                    <?= date('j<\s\u\p>S</\s\u\p> F Y', strtotime($quotation['quotation_date'])); ?><br>
                    <?= htmlspecialchars($quotation['quotation_no']); ?>
                </div>

                <!-- From Block -->
                <div class="party-block">
                    <div class="party-title">From</div>
                    <div class="party-company"><?= htmlspecialchars($cover['from_company'] ?? 'SAMVRUDDHI TECHNOLOGIES LLC'); ?></div>
                    <div class="party-details">Contact person: <?= htmlspecialchars($cover['from_contact_person'] ?? 'Ms. Lenie'); ?> ,</div>
                    <div class="party-details">Contact details: <?= htmlspecialchars($cover['from_contact_details'] ?? '+971 4 3554245'); ?>,</div>
                    <div class="party-details party-email">Email: <a href="mailto:<?= htmlspecialchars($cover['from_email'] ?? 'sales@samvruddhi.com'); ?>"><?= htmlspecialchars($cover['from_email'] ?? 'sales@samvruddhi.com'); ?></a></div>
                </div>

                <!-- To Block -->
                <div class="party-block">
                    <div class="party-title">To</div>
                    <div class="party-company"><?= htmlspecialchars($cover['to_company'] ?? $quotation['company_name']); ?></div>
                    <div class="party-details">Contact person: <?= htmlspecialchars($cover['to_contact_person'] ?? '-'); ?></div>
                    <div class="party-details">Contact details: <?= htmlspecialchars($cover['to_contact_details'] ?? '-'); ?></div>
                    <?php if (!empty($cover['to_email'])): ?>
                        <div class="party-details party-email">Email: <a href="mailto:<?= htmlspecialchars($cover['to_email']); ?>"><?= htmlspecialchars($cover['to_email']); ?></a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PAGE 2: COVER LETTER                       -->
    <!-- ========================================== -->
    <div class="page-sheet">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="sheet-body">
                <div style="margin-top: 25px; margin-bottom: 20px; font-weight: 700; font-size: 12pt;">
                    <?= htmlspecialchars($letter['salutation'] ?? 'Dear Client,'); ?>
                </div>

                <div class="proposal-text" style="margin-bottom: 25px;">
                    <?= nl2br(htmlspecialchars($letter['body'] ?? '')); ?>
                </div>

                <div style="margin-top: 35px; font-size: 11pt; font-weight: 700; color: #0f172a; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($letter['closing'] ?? "Best Regards,\nSamvruddhi Technologies")); ?>
                </div>
            </div>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PAGE 3: ABOUT THIS PROPOSAL                -->
    <!-- ========================================== -->
    <div class="page-sheet auto-paginate" data-page-title="About this Proposal">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="sheet-body">
                <div class="section-heading-main page-main-title">
                    <?= htmlspecialchars($about['heading'] ?? 'About this Proposal'); ?>
                </div>

                <?php if (!empty($about['intro'])): ?>
                    <div class="proposal-text page-block" data-block-type="paragraph" style="font-weight: 600; margin-bottom: 12px;">
                        <?= nl2br(htmlspecialchars($about['intro'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($about['purpose_scope'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Purpose &amp; Scope</div>
                    <?php
                    $scopeParas = preg_split('/(\r\n\s*\r\n|\n\s*\n)/', trim($about['purpose_scope']));
                    foreach ($scopeParas as $p):
                        if (trim($p) === '') continue;
                    ?>
                        <div class="proposal-text page-block" data-block-type="paragraph">
                            <?= nl2br(htmlspecialchars(trim($p))); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($about['confidentiality'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Confidentiality</div>
                    <?php
                    $confParas = preg_split('/(\r\n\s*\r\n|\n\s*\n)/', trim($about['confidentiality']));
                    foreach ($confParas as $p):
                        if (trim($p) === '') continue;
                    ?>
                        <div class="proposal-text page-block" data-block-type="paragraph">
                            <?= nl2br(htmlspecialchars(trim($p))); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($about['validity'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Validity</div>
                    <?php
                    $valParas = preg_split('/(\r\n\s*\r\n|\n\s*\n)/', trim($about['validity']));
                    foreach ($valParas as $p):
                        if (trim($p) === '') continue;
                    ?>
                        <div class="proposal-text page-block" data-block-type="paragraph">
                            <?= nl2br(htmlspecialchars(trim($p))); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MIDDLE CUSTOM PAGES (Dynamic & Auto-Splitting) -->
    <!-- ========================================== -->
    <?php if (!empty($custom)): ?>
        <?php foreach ($custom as $cPage): ?>
            <div class="page-sheet auto-paginate" data-page-title="<?= htmlspecialchars($cPage['title'] ?? 'Proposal Scope'); ?>">
                <div class="sheet-content">
                    <table class="header-table">
                        <tr>
                            <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                        </tr>
                    </table>

                    <div class="sheet-body">
                        <?php if (!empty($cPage['title'])): ?>
                            <div class="section-heading-main page-main-title">
                                <?= htmlspecialchars($cPage['title']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cPage['blocks'])): ?>
                            <?php foreach ($cPage['blocks'] as $block): ?>
                                <?php if ($block['type'] === 'heading'): ?>
                                    <div class="section-subheading page-block" data-block-type="heading">
                                        <?= htmlspecialchars($block['content']); ?>
                                    </div>
                                <?php elseif ($block['type'] === 'description'): ?>
                                    <?php
                                    $rawContent = $block['content'] ?? '';
                                    $paragraphs = preg_split('/(\r\n\s*\r\n|\n\s*\n)/', trim($rawContent));
                                    foreach ($paragraphs as $para):
                                        $para = trim($para);
                                        if ($para === '') continue;
                                        $lines = explode("\n", str_replace("\r", "", $para));
                                        $currentChunk = [];
                                        foreach ($lines as $line):
                                            $trimmedLine = trim($line);
                                            if (preg_match('/^(\•|\-|\*|\d+[\.\)])\s+/', $trimmedLine)) {
                                                if (!empty($currentChunk)) {
                                                    echo '<div class="proposal-text page-block" data-block-type="paragraph">' . nl2br(htmlspecialchars(implode("\n", $currentChunk))) . '</div>';
                                                    $currentChunk = [];
                                                }
                                                echo '<div class="proposal-text page-block" data-block-type="paragraph" style="margin-left: 8px;">' . nl2br(htmlspecialchars($line)) . '</div>';
                                            } else {
                                                $currentChunk[] = $line;
                                            }
                                        endforeach;
                                        if (!empty($currentChunk)) {
                                            echo '<div class="proposal-text page-block" data-block-type="paragraph">' . nl2br(htmlspecialchars(implode("\n", $currentChunk))) . '</div>';
                                        }
                                    endforeach;
                                    ?>
                                <?php elseif ($block['type'] === 'table' && !empty($block['columns'])): ?>
                                    <div class="table-wrapper page-block" data-block-type="table">
                                        <table class="data-table">
                                            <thead>
                                                <tr>
                                                    <?php foreach ($block['columns'] as $cIndex => $col): ?>
                                                        <th style="<?= $cIndex === 0 ? 'width:60px; text-align:center;' : ''; ?>">
                                                            <?= htmlspecialchars($col); ?>
                                                        </th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($block['rows'])): ?>
                                                    <?php foreach ($block['rows'] as $r): ?>
                                                        <tr>
                                                            <?php foreach ($r as $cIndex => $val): ?>
                                                                <td style="<?= $cIndex === 0 ? 'text-align:center;' : ''; ?>">
                                                                    <?= htmlspecialchars($val); ?>
                                                                </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="letterhead-footer">
                    <div class="lh-company">Samvruddhi Technologies LLC</div>
                    <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
                    <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ========================================== -->
    <!-- SECOND-TO-LAST PAGE: FINANCIALS            -->
    <!-- ========================================== -->
    <div class="page-sheet auto-paginate" data-page-title="Financials">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="sheet-body">
                <div class="section-heading-main page-main-title">
                    Financials
                </div>

                <?php if (!empty($fin['scope_subtitle'])): ?>
                    <div class="page-block" style="font-size: 10.5pt; font-weight: 600; color: #334155; margin-bottom: 10px;">
                        <?= htmlspecialchars($fin['scope_subtitle']); ?>
                    </div>
                <?php endif; ?>

                <!-- Financials Table -->
                <div class="table-wrapper page-block" data-block-type="table">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">SN</th>
                                <th>ITEMS</th>
                                <th style="width: 120px; text-align: center;">TOTAL NOS</th>
                                <th style="width: 170px; text-align: right;">TOTAL AMOUNT (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($fin['items'])): ?>
                                <?php foreach ($fin['items'] as $item): ?>
                                    <tr>
                                        <td style="text-align: center;"><?= htmlspecialchars($item['sn'] ?? '1'); ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($item['item'] ?? ''); ?></td>
                                        <td style="text-align: center;"><?= htmlspecialchars($item['qty'] ?? '1'); ?></td>
                                        <td style="text-align: right; font-weight: 600;"><?= number_format((float)($item['amount'] ?? 0), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">TOTAL AMOUNT</td>
                                <td style="text-align: right;"><?= number_format((float)($fin['total_amount'] ?? 0), 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right;">VAT <?= htmlspecialchars($fin['vat_percent'] ?? 5); ?>%</td>
                                <td style="text-align: right;"><?= number_format((float)($fin['vat_amount'] ?? 0), 2); ?></td>
                            </tr>
                            <tr class="grand-total">
                                <td colspan="3" style="text-align: right;">GRAND TOTAL AMOUNT (AED)</td>
                                <td style="text-align: right;"><?= number_format((float)($fin['grand_total'] ?? 0), 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($fin['notes'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Note:</div>
                    <div class="proposal-text page-block" data-block-type="paragraph">
                        <?= nl2br(htmlspecialchars($fin['notes'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($fin['terms'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Terms</div>
                    <div class="proposal-text page-block" data-block-type="paragraph" style="font-weight: 600;">
                        <?= nl2br(htmlspecialchars($fin['terms'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($fin['deliverables'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Deliverables</div>
                    <div class="proposal-text page-block" data-block-type="paragraph">
                        <?= nl2br(htmlspecialchars($fin['deliverables'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LAST PAGE: REPORT HIGHLIGHTS & AGREEMENT   -->
    <!-- ========================================== -->
    <div class="page-sheet auto-paginate" data-page-title="Agreement & Sign-off">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="sheet-body">
                <?php if (!empty($sign['report_highlights'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading" style="margin-top: 0;">Report highlights:</div>
                    <div class="proposal-text page-block" data-block-type="paragraph">
                        <?= nl2br(htmlspecialchars($sign['report_highlights'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sign['report_delivery'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Report delivery:</div>
                    <div class="proposal-text page-block" data-block-type="paragraph">
                        <?= nl2br(htmlspecialchars($sign['report_delivery'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sign['non_disclosure'])): ?>
                    <div class="section-subheading page-block" data-block-type="heading">Non-Disclosure</div>
                    <div class="proposal-text page-block" data-block-type="paragraph">
                        <?= nl2br(htmlspecialchars($sign['non_disclosure'])); ?>
                    </div>
                <?php endif; ?>

                <!-- Dual Signatory Blocks -->
                <div class="page-block" data-block-type="signatories">
                    <table class="signatory-box">
                        <tr>
                            <td>
                                <div style="font-weight: 800; font-size: 10.5pt; color: #0f172a; margin-bottom: 6px;">Samvruddhi Technologies LLC</div>
                                <div>Name: <?= htmlspecialchars($sign['provider_name'] ?? 'Mr. Vipul Davda'); ?></div>
                                <div>Title: <?= htmlspecialchars($sign['provider_title'] ?? 'Managing Director'); ?></div>
                                <div>Tel Nos: <?= htmlspecialchars($sign['provider_tel'] ?? '04 435 54245'); ?></div>
                                <div>Email: <?= htmlspecialchars($sign['provider_email'] ?? 'vipul@samvruddhi.com'); ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 800; font-size: 10.5pt; color: #0f172a; margin-bottom: 6px;"><?= htmlspecialchars($quotation['company_name']); ?></div>
                                <div>Name: <?= htmlspecialchars($sign['client_name'] ?? '-'); ?></div>
                                <div>Title: <?= htmlspecialchars($sign['client_title'] ?? '-'); ?></div>
                                <div>Tel Nos: <?= htmlspecialchars($sign['client_tel'] ?? '-'); ?></div>
                                <div>Email: <?= htmlspecialchars($sign['client_email'] ?? '-'); ?></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php if (!empty($sign['closing_message'])): ?>
                    <div class="closing-banner page-block" data-block-type="closing">
                        <?= htmlspecialchars($sign['closing_message']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- DYNAMIC AUTO-PAGINATION ENGINE             -->
    <!-- ========================================== -->
    <script>
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function createNextPageSheet(previousSheet, pageTitle) {
        const newSheet = document.createElement('div');
        newSheet.className = 'page-sheet auto-paginate is-split-child';
        if (pageTitle) {
            newSheet.setAttribute('data-page-title', pageTitle);
        }

        const logoImg = previousSheet.querySelector('.logo');
        const logoSrc = logoImg ? logoImg.src : '';

        newSheet.innerHTML = `
            <div class="sheet-content">
                <table class="header-table">
                    <tr>
                        <td><img src="${logoSrc}" class="logo" alt="Samtech Solutions"></td>
                    </tr>
                </table>
                <div class="sheet-body">
                    ${pageTitle ? `<div class="section-heading-main page-main-title">${escapeHtml(pageTitle)} <span style="font-size: 10pt; font-weight: normal; color: #64748b; margin-left: 6px;">(Contd.)</span></div>` : ''}
                </div>
            </div>
            <div class="letterhead-footer">
                <div class="lh-company">Samvruddhi Technologies LLC</div>
                <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
                <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
            </div>
        `;

        previousSheet.parentNode.insertBefore(newSheet, previousSheet.nextSibling);
        return newSheet;
    }

    function paginateSheet(sheet) {
        const sheetBody = sheet.querySelector('.sheet-body');
        const footer = sheet.querySelector('.letterhead-footer');
        if (!sheetBody || !footer) return;

        // Force a layout calculation
        const sheetRect = sheet.getBoundingClientRect();
        const footerRect = footer.getBoundingClientRect();

        // Calculate maximum allowed bottom boundary in pixels
        // The content must stay at least 14px above the footer
        const maxBottomByFooter = footerRect.top - 14;
        const maxBottomBySheet = (sheetRect.top + sheet.clientHeight) - footer.offsetHeight - 16;
        const maxBottom = Math.min(maxBottomByFooter, maxBottomBySheet);

        const children = Array.from(sheetBody.children);
        if (children.length <= 1) return; // Only title or empty

        let splitIndex = -1;
        let splitTable = null;

        for (let i = 0; i < children.length; i++) {
            const child = children[i];
            if (child.classList.contains('page-main-title')) continue;

            const childRect = child.getBoundingClientRect();

            if (childRect.bottom > maxBottom) {
                // Check if this child is a table wrapper with rows
                if (child.classList.contains('table-wrapper')) {
                    const table = child.querySelector('table');
                    const rows = table ? Array.from(table.querySelectorAll('tbody tr')) : [];
                    if (rows.length > 1) {
                        let splitRowIdx = -1;
                        for (let r = 0; r < rows.length; r++) {
                            const rowRect = rows[r].getBoundingClientRect();
                            if (rowRect.bottom > maxBottom) {
                                splitRowIdx = r;
                                break;
                            }
                        }
                        if (splitRowIdx > 0) {
                            splitIndex = i;
                            splitTable = { splitRowIndex: splitRowIdx };
                            break;
                        }
                    }
                }

                splitIndex = i;
                break;
            }
        }

        if (splitIndex === -1) {
            // Everything fits on this page
            return;
        }

        // Avoid orphan heading: if element immediately before splitIndex is a heading, move it too
        if (splitIndex > 0 && !splitTable) {
            const prev = children[splitIndex - 1];
            if (prev && !prev.classList.contains('page-main-title') &&
                (prev.classList.contains('section-subheading') || prev.getAttribute('data-block-type') === 'heading')) {
                splitIndex--;
            }
        }

        // Avoid pushing all content off if splitIndex lands on index 0
        if (splitIndex === 0 && children[0].classList.contains('page-main-title')) {
            splitIndex = 1;
        }

        if (splitIndex >= children.length) return;

        // Create the continuation sheet
        const pageTitle = sheet.getAttribute('data-page-title') || '';
        const newSheet = createNextPageSheet(sheet, pageTitle);
        const newSheetBody = newSheet.querySelector('.sheet-body');

        if (splitTable) {
            const tableBlock = children[splitIndex];
            const originalTbody = tableBlock.querySelector('tbody');
            const rows = Array.from(originalTbody.querySelectorAll('tr'));

            const clonedTableBlock = tableBlock.cloneNode(true);
            const clonedTbody = clonedTableBlock.querySelector('tbody');
            clonedTbody.innerHTML = '';

            for (let r = splitTable.splitRowIndex; r < rows.length; r++) {
                clonedTbody.appendChild(rows[r]);
            }

            newSheetBody.appendChild(clonedTableBlock);

            for (let i = splitIndex + 1; i < children.length; i++) {
                newSheetBody.appendChild(children[i]);
            }
        } else {
            for (let i = splitIndex; i < children.length; i++) {
                newSheetBody.appendChild(children[i]);
            }
        }

        // Recursively paginate newSheet if it also has more content than fits
        paginateSheet(newSheet);
    }

    function paginateAllSheets() {
        const paginatedSheets = Array.from(document.querySelectorAll('.page-sheet.auto-paginate:not(.is-split-child)'));
        paginatedSheets.forEach(sheet => {
            paginateSheet(sheet);
        });
    }

    function doPrint() {
        paginateAllSheets();
        setTimeout(() => {
            window.print();
        }, 150);
    }

    window.addEventListener('DOMContentLoaded', () => {
        paginateAllSheets();
    });

    window.addEventListener('beforeprint', () => {
        paginateAllSheets();
    });
    </script>
</body>
</html>
