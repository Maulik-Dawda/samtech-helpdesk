<?php

require_once ROOT_PATH .
    "/app/Views/layouts/loader.php";

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