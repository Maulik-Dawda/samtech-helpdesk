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
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        body.classList.add('sidebar-open');
        body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        body.classList.remove('sidebar-open');
        body.classList.remove('overflow-hidden');
    }

    sidebarToggle?.addEventListener(
        'click',
        function (e) {
            e.stopPropagation();
            if (sidebar?.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    );

    sidebarClose?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('.app-sidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (isMobileLayout()) {
                closeSidebar();
            }
        });
    });

    window.addEventListener(
        'resize',
        function () {
            if (!isMobileLayout()) {
                closeSidebar();
            }
        }
    );

    // Live Dashboard Clock with seconds (Dubai Time Asia/Dubai)
    function startLiveDashboardClock() {
        const liveTimeElements = document.querySelectorAll('.dashboard-time[data-live-time="true"]');
        const liveDateElements = document.querySelectorAll('.dashboard-date[data-live-date="true"]');
        if (!liveTimeElements.length && !liveDateElements.length) return;

        const optionsTime = {
            timeZone: 'Asia/Dubai',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const optionsDate = {
            timeZone: 'Asia/Dubai',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };

        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', optionsTime);
            const dateString = now.toLocaleDateString('en-GB', optionsDate);

            liveTimeElements.forEach(function (el) {
                el.textContent = timeString;
            });

            liveDateElements.forEach(function (el) {
                el.textContent = dateString;
            });
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

    startLiveDashboardClock();

    // Universal Interactive Attachment Accumulator (adds files again and again on selection, without Upload button)
    function initAttachmentGroup(group) {
        const picker = group.querySelector('.attachment-picker-input');
        const hiddenInput = group.querySelector('.attachment-hidden-input');
        const listContainer = group.querySelector('.attachment-staged-list');

        if (!picker || !hiddenInput || !listContainer) return;

        let dt = new DataTransfer();

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'bi-file-image text-info';
            if (['pdf'].includes(ext)) return 'bi-file-pdf text-danger';
            if (['doc', 'docx'].includes(ext)) return 'bi-file-word text-primary';
            if (['xls', 'xlsx'].includes(ext)) return 'bi-file-excel text-success';
            if (['zip', 'rar'].includes(ext)) return 'bi-file-zip text-warning';
            return 'bi-file-earmark text-secondary';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        function renderList() {
            listContainer.innerHTML = '';

            Array.from(dt.files).forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between p-2 px-3 bg-white rounded-3 border shadow-sm';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <i class="bi ${getFileIcon(file.name)} fs-5"></i>
                        <div class="text-truncate">
                            <span class="fw-semibold text-dark small me-2">${escapeHtml(file.name)}</span>
                            <span class="text-muted small">(${formatBytes(file.size)})</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                            <i class="bi bi-check-circle-fill me-1"></i> Attached
                        </span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-staged-file" data-index="${index}" title="Remove file">
                            <i class="bi bi-x-circle-fill fs-6"></i>
                        </button>
                    </div>
                `;
                listContainer.appendChild(item);
            });

            listContainer.querySelectorAll('.remove-staged-file').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.getAttribute('data-index'));
                    const newDt = new DataTransfer();
                    Array.from(dt.files).forEach((f, i) => {
                        if (i !== idx) newDt.items.add(f);
                    });
                    dt = newDt;
                    hiddenInput.files = dt.files;
                    renderList();
                });
            });
        }

        picker.addEventListener('change', function () {
            if (picker.files.length > 0) {
                Array.from(picker.files).forEach(file => {
                    dt.items.add(file);
                });
                hiddenInput.files = dt.files;
                picker.value = '';
                renderList();
            }
        });
    }

    document.querySelectorAll('.attachment-upload-group').forEach(initAttachmentGroup);
});
</script>

</body>
</html>