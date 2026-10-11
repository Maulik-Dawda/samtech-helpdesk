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

$iconPath = ROOT_PATH . '/public/assets/images/samtech-icon.png';
$iconSrc = file_exists($iconPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($iconPath))
    : BASE_URL . '/assets/images/samtech-icon.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation Proposal - <?= htmlspecialchars($quotation['quotation_no']); ?> - <?= htmlspecialchars($quotation['company_name']); ?></title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 20mm 10mm;
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
            font-size: 11.5pt;
            line-height: 1.6;
        }

        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
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
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .page-sheet:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .letterhead-footer {
                position: fixed;
                bottom: 4mm !important;
                left: 10mm !important;
                right: 10mm !important;
                background: transparent !important;
            }
        }

        /* Screen Presentation Sheet */
        .page-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 10mm 12mm 24mm 12mm;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
        }

        /* Letterhead Watermark Logo */
        .watermark-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            max-width: 80%;
            height: auto;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
        }

        .sheet-content {
            position: relative;
            z-index: 1;
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
            padding-bottom: 12px;
            margin-bottom: 22px;
            border-collapse: collapse;
            background: transparent !important;
        }

        .header-table td {
            vertical-align: top;
            background: transparent !important;
        }

        .logo {
            height: 56px;
            width: auto;
            max-width: 220px;
            display: block;
        }

        /* Official Letterhead Footer */
        .letterhead-footer {
            position: absolute;
            bottom: 4mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            color: #334155;
            background: transparent !important;
            padding-top: 4px;
            border-top: 1px solid #cbd5e1;
            z-index: 10;
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

        /* Typography & Styles */
        .cover-title {
            text-align: center;
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-top: 25px;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .cover-meta {
            text-align: center;
            font-size: 13pt;
            font-weight: 700;
            color: #1e293b;
            margin-top: 30px;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .party-block {
            text-align: center;
            margin-bottom: 35px;
            line-height: 1.7;
        }

        .party-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .party-company {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .party-details {
            font-size: 11pt;
            color: #1e293b;
        }

        .party-email a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 600;
        }

        .section-heading-main {
            font-size: 16pt;
            font-weight: 800;
            color: #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 18px;
            margin-top: 10px;
        }

        .section-subheading {
            font-size: 12pt;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 16px;
            margin-bottom: 6px;
        }

        .proposal-text {
            font-size: 10.5pt;
            color: #1e293b;
            line-height: 1.65;
            text-align: justify;
            margin-bottom: 14px;
        }

        /* Financials Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 18px;
            font-size: 9.5pt;
        }

        .data-table th {
            background-color: #205493;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 10px;
            border: 1px solid #1e40af;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 7px 10px;
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
        }

        .data-table tfoot tr.grand-total td {
            background-color: #f0fdf4 !important;
            color: #166534;
            font-size: 10.5pt;
            font-weight: 800;
        }

        .signatory-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .signatory-box td {
            width: 50%;
            vertical-align: top;
            padding: 10px 15px;
            font-size: 10pt;
            line-height: 1.7;
        }

        .closing-banner {
            text-align: center;
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 40px;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Floating Actions Toolbar -->
    <div class="print-actions">
        <a href="<?= BASE_URL ?>/quotations/show/<?= $quotation['id']; ?>" class="btn-action btn-back">
            &larr; Back to Quotation
        </a>
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            &#128438; Print / Save as PDF
        </button>
    </div>

    <!-- ========================================== -->
    <!-- PAGE 1: COVER PAGE                         -->
    <!-- ========================================== -->
    <div class="page-sheet">
        <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

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
        <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div style="margin-top: 35px; margin-bottom: 25px; font-weight: 700; font-size: 13pt;">
                <?= htmlspecialchars($letter['salutation'] ?? 'Dear Client,'); ?>
            </div>

            <div class="proposal-text" style="margin-bottom: 25px;">
                <?= nl2br(htmlspecialchars($letter['body'] ?? '')); ?>
            </div>

            <div style="margin-top: 40px; font-size: 11.5pt; font-weight: 700; color: #0f172a; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($letter['closing'] ?? "Best Regards,\nSamvruddhi Technologies")); ?>
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
    <div class="page-sheet">
        <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="section-heading-main">
                <?= htmlspecialchars($about['heading'] ?? 'About this Proposal'); ?>
            </div>

            <?php if (!empty($about['intro'])): ?>
                <div class="proposal-text" style="font-weight: 600; margin-bottom: 20px;">
                    <?= nl2br(htmlspecialchars($about['intro'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($about['purpose_scope'])): ?>
                <div class="section-subheading">Purpose &amp; Scope</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($about['purpose_scope'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($about['confidentiality'])): ?>
                <div class="section-subheading">Confidentiality</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($about['confidentiality'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($about['validity'])): ?>
                <div class="section-subheading">Validity</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($about['validity'])); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MIDDLE CUSTOM PAGES (Dynamic)              -->
    <!-- ========================================== -->
    <?php if (!empty($custom)): ?>
        <?php foreach ($custom as $cPage): ?>
            <div class="page-sheet">
                <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
                <div class="sheet-content">
                    <table class="header-table">
                        <tr>
                            <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                        </tr>
                    </table>

                    <?php if (!empty($cPage['title'])): ?>
                        <div class="section-heading-main">
                            <?= htmlspecialchars($cPage['title']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($cPage['blocks'])): ?>
                        <?php foreach ($cPage['blocks'] as $block): ?>
                            <?php if ($block['type'] === 'heading'): ?>
                                <div class="section-subheading">
                                    <?= htmlspecialchars($block['content']); ?>
                                </div>
                            <?php elseif ($block['type'] === 'description'): ?>
                                <div class="proposal-text">
                                    <?= nl2br(htmlspecialchars($block['content'])); ?>
                                </div>
                            <?php elseif ($block['type'] === 'table' && !empty($block['columns'])): ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <?php foreach ($block['columns'] as $cIndex => $col): ?>
                                                <th style="<?= $cIndex === 0 ? 'width:70px; text-align:center;' : ''; ?>">
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
    <div class="page-sheet">
        <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <div class="section-heading-main">
                Financials
            </div>

            <?php if (!empty($fin['scope_subtitle'])): ?>
                <div style="font-size: 11pt; font-weight: 600; color: #334155; margin-bottom: 12px;">
                    <?= htmlspecialchars($fin['scope_subtitle']); ?>
                </div>
            <?php endif; ?>

            <!-- Financials Table -->
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

            <?php if (!empty($fin['notes'])): ?>
                <div class="section-subheading">Note:</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($fin['notes'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($fin['terms'])): ?>
                <div class="section-subheading">Terms</div>
                <div class="proposal-text" style="font-weight: 600;">
                    <?= nl2br(htmlspecialchars($fin['terms'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($fin['deliverables'])): ?>
                <div class="section-subheading">Deliverables</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($fin['deliverables'])); ?>
                </div>
            <?php endif; ?>
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
    <div class="page-sheet">
        <img class="watermark-logo" src="<?= $iconSrc; ?>" alt="Watermark">
        <div class="sheet-content">
            <table class="header-table">
                <tr>
                    <td><img src="<?= $logoSrc; ?>" class="logo" alt="Samtech Solutions"></td>
                </tr>
            </table>

            <?php if (!empty($sign['report_highlights'])): ?>
                <div class="section-subheading" style="margin-top: 0;">Report highlights:</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($sign['report_highlights'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($sign['report_delivery'])): ?>
                <div class="section-subheading">Report delivery:</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($sign['report_delivery'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($sign['non_disclosure'])): ?>
                <div class="section-subheading">Non-Disclosure</div>
                <div class="proposal-text">
                    <?= nl2br(htmlspecialchars($sign['non_disclosure'])); ?>
                </div>
            <?php endif; ?>

            <!-- Dual Signatory Blocks -->
            <table class="signatory-box">
                <tr>
                    <td>
                        <div style="font-weight: 800; font-size: 11pt; color: #0f172a; margin-bottom: 6px;">Samvruddhi Technologies LLC</div>
                        <div>Name: <?= htmlspecialchars($sign['provider_name'] ?? 'Mr. Vipul Davda'); ?></div>
                        <div>Title: <?= htmlspecialchars($sign['provider_title'] ?? 'Managing Director'); ?></div>
                        <div>Tel Nos: <?= htmlspecialchars($sign['provider_tel'] ?? '04 435 54245'); ?></div>
                        <div>Email: <?= htmlspecialchars($sign['provider_email'] ?? 'vipul@samvruddhi.com'); ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 800; font-size: 11pt; color: #0f172a; margin-bottom: 6px;"><?= htmlspecialchars($quotation['company_name']); ?></div>
                        <div>Name: <?= htmlspecialchars($sign['client_name'] ?? '-'); ?></div>
                        <div>Title: <?= htmlspecialchars($sign['client_title'] ?? '-'); ?></div>
                        <div>Tel Nos: <?= htmlspecialchars($sign['client_tel'] ?? '-'); ?></div>
                        <div>Email: <?= htmlspecialchars($sign['client_email'] ?? '-'); ?></div>
                    </td>
                </tr>
            </table>

            <?php if (!empty($sign['closing_message'])): ?>
                <div class="closing-banner">
                    <?= htmlspecialchars($sign['closing_message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="letterhead-footer">
            <div class="lh-company">Samvruddhi Technologies LLC</div>
            <div class="lh-address">1st Floor, Shindagha City Centre (Carrefour) , A001A Blue Titan Office B-14, Dubai United Arab Emirates P.O. Box 377567</div>
            <div class="lh-contact">+971-4-3554245 &nbsp;|&nbsp; sales@samvruddhi.com &nbsp;|&nbsp; https://samtech.ae/ &nbsp;|&nbsp; TRN 100324643400003</div>
        </div>
    </div>

</body>
</html>
