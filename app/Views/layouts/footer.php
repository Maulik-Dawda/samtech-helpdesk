<?php

require_once ROOT_PATH .
    "/app/Views/layouts/loader.php";

$footerLogoPath = ROOT_PATH . '/public/assets/images/samtech-logo-report.png';
$footerLogoBase64 = file_exists($footerLogoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerLogoPath))
    : '';

?>

    </main>
    <!-- /.main-content -->

</div>
<!-- /.app-shell -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar =
        document.getElementById('appSidebar');
    const sidebarToggle =
        document.getElementById('sidebarToggle');
    const sidebarClose =
        document.getElementById('sidebarClose');
    const overlay =
        document.getElementById('sidebarOverlay');

    function isMobileLayout() {
        return window.innerWidth < 1200;
    }

    function openSidebar() {
        if (!sidebar || !isMobileLayout()) {
            return;
        }

        body.classList.add('sidebar-open');

        sidebarToggle?.setAttribute(
            'aria-expanded',
            'true'
        );

        overlay?.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closeSidebar() {
        body.classList.remove('sidebar-open');

        sidebarToggle?.setAttribute(
            'aria-expanded',
            'false'
        );

        overlay?.setAttribute(
            'aria-hidden',
            'true'
        );
    }

    sidebarToggle?.addEventListener(
        'click',
        function () {
            if (
                body.classList.contains(
                    'sidebar-open'
                )
            ) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    );

    sidebarClose?.addEventListener(
        'click',
        closeSidebar
    );

    overlay?.addEventListener(
        'click',
        closeSidebar
    );

    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        }
    );

    document
        .querySelectorAll('#appSidebar .sidebar-link')
        .forEach(function (link) {
            link.addEventListener(
                'click',
                function () {
                    if (isMobileLayout()) {
                        closeSidebar();
                    }
                }
            );
        });

    window.addEventListener(
        'resize',
        function () {
            if (!isMobileLayout()) {
                closeSidebar();
            }
        }
    );
});

async function downloadDirectServerPdf(downloadUrl, btn) {
    if (!btn) return;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Downloading PDF...';
    btn.disabled = true;

    try {
        const response = await fetch(downloadUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-cache'
        });

        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }

        const blob = await response.blob();
        if (blob.size === 0) {
            throw new Error('Received 0-byte PDF blob');
        }

        let filename = 'Samtech-Helpdesk-Report.pdf';
        const disposition = response.headers.get('Content-Disposition');
        if (disposition && disposition.includes('filename=')) {
            const match = disposition.match(/filename=["']?([^"';]+)["']?/);
            if (match && match[1]) {
                filename = match[1];
            }
        }

        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = blobUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();

        setTimeout(function() {
            window.URL.revokeObjectURL(blobUrl);
            document.body.removeChild(a);
        }, 200);

    } catch (err) {
        console.error('PDF Fetch Error:', err);
        alert('Failed to download PDF. Please try again.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

async function downloadElementAsPdf(elementId, filename, btn, headerTitle) {
    headerTitle = headerTitle || "Ticket Report";
    const element = document.getElementById(elementId);
    if (!btn || !element) {
        alert("Report content not found.");
        return;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating PDF...';
    btn.disabled = true;

    try {
        const printWrapper = document.createElement("div");
        printWrapper.id = "pdfTempPrintWrapper";
        printWrapper.style.position = "fixed";
        printWrapper.style.top = "0";
        printWrapper.style.left = "0";
        printWrapper.style.width = "790px";
        printWrapper.style.background = "#ffffff";
        printWrapper.style.color = "#111827";
        printWrapper.style.fontFamily = "Arial, Helvetica, sans-serif";
        printWrapper.style.padding = "25px";
        printWrapper.style.boxSizing = "border-box";
        printWrapper.style.zIndex = "999999";
        printWrapper.style.boxShadow = "0 0 30px rgba(0,0,0,0.15)";

        const logoBase64 = "<?= $footerLogoBase64 ?>";
        const logoHtml = logoBase64 !== ""
            ? `<img src="${logoBase64}" style="width:180px; height:auto; display:block;" alt="Samtech">`
            : `<div style="font-size:22px; font-weight:bold; color:#111827;">Samtech Solutions</div>`;

        printWrapper.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #b1e96f; padding-bottom:12px; margin-bottom:18px;">
                <div>
                    ${logoHtml}
                    <div style="font-size:22px; font-weight:bold; color:#111827; margin-top:6px;">${headerTitle}</div>
                    <div style="color:#64748b; font-size:11px; margin-top:2px;">Samtech Helpdesk Management System</div>
                </div>
                <div style="text-align:right; font-size:10px; color:#64748b; line-height:1.5;">
                    <strong>Generated On:</strong> ${new Date().toLocaleString()}<br>
                    <strong>Generated By:</strong> <?= htmlspecialchars($_SESSION['auth_user_name'] ?? 'System'); ?>
                </div>
            </div>
            <div>
                ${element.innerHTML}
            </div>
            <div style="margin-top:20px; padding-top:10px; border-top:1px solid #e5e7eb; text-align:center; color:#64748b; font-size:9.5px;">
                This report was automatically generated by Samtech Helpdesk.
            </div>
        `;

        const stripElements = printWrapper.querySelectorAll("#reportSearchInput, #ticketSearchInput, .btn, .print-btn, .search-box, script");
        stripElements.forEach(el => el.remove());

        document.body.appendChild(printWrapper);

        const images = Array.from(printWrapper.querySelectorAll('img'));
        await Promise.all(images.map(img => {
            if (img.complete && img.naturalWidth > 0) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
            });
        }));

        await new Promise(resolve => setTimeout(resolve, 350));

        const opt = {
            margin:       [8, 8, 8, 8],
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                allowTaint: true,
                logging: false, 
                scrollX: 0, 
                scrollY: 0,
                width: 790
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        await html2pdf().set(opt).from(printWrapper).save();
        document.body.removeChild(printWrapper);

    } catch (err) {
        console.error("PDF generation error:", err);
        alert("Failed to download PDF. Please try again.");
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

async function generateDirectPdf(printUrl, filename, btn) {
    if (!btn) return;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating PDF...';
    btn.disabled = true;

    try {
        const response = await fetch(printUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const htmlText = await response.text();

        const container = document.createElement('div');
        container.id = 'pdfRenderTempContainer';
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.overflowY = 'auto';
        container.style.background = '#ffffff';
        container.style.zIndex = '999999';
        container.style.padding = '20px';
        container.style.boxSizing = 'border-box';

        const pageBox = document.createElement('div');
        pageBox.style.width = '790px';
        pageBox.style.margin = '0 auto';
        pageBox.style.background = '#ffffff';
        pageBox.style.color = '#111827';
        pageBox.style.fontFamily = 'Arial, Helvetica, sans-serif';
        pageBox.innerHTML = htmlText;

        const removeTargets = pageBox.querySelectorAll('.print-btn, .print-actions, script');
        removeTargets.forEach(el => el.remove());

        container.appendChild(pageBox);
        document.body.appendChild(container);

        const images = pageBox.querySelectorAll('img');
        const imagePromises = Array.from(images).map(img => {
            if (img.complete) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
            });
        });
        await Promise.all(imagePromises);

        await new Promise(resolve => setTimeout(resolve, 250));

        const opt = {
            margin:       [8, 8, 8, 8],
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                logging: false, 
                scrollX: 0, 
                scrollY: 0 
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        await html2pdf().set(opt).from(pageBox).save();
        document.body.removeChild(container);

    } catch (err) {
        console.error('Direct PDF Generation error:', err);
        alert('Failed to generate PDF. Please try again.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function triggerBackgroundPdfDownload(url, btn) {
    if (!btn) return;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Downloading...';
    btn.disabled = true;

    let iframe = document.getElementById('hiddenDownloadIframe');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'hiddenDownloadIframe';
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }

    iframe.src = url;

    setTimeout(function() {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2500);
}
</script>

</body>
</html>