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
</script>

</body>
</html>