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
});
</script>

</body>
</html>