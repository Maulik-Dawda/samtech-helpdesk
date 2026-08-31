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

    // Live Dashboard Clock with seconds
    function startLiveDashboardClock() {
        const liveTimeElements = document.querySelectorAll('.dashboard-time[data-live-time="true"]');
        if (!liveTimeElements.length) return;

        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const formattedHours = String(hours).padStart(2, '0');
            const timeString = `${formattedHours}:${minutes}:${seconds} ${ampm}`;

            liveTimeElements.forEach(function (el) {
                const badge = el.querySelector('span');
                if (badge) {
                    const badgeClone = badge.cloneNode(true);
                    el.textContent = timeString + ' ';
                    el.appendChild(badgeClone);
                } else {
                    el.textContent = timeString;
                }
            });
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

    startLiveDashboardClock();
});
</script>

</body>
</html>