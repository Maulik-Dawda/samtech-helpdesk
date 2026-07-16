<?php require_once ROOT_PATH . "/app/Views/layouts/loader.php"; ?>

        </main>
        <!-- /.main-content -->

    </div>
    <!-- /.app-shell -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const sidebar = document.getElementById('appSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                if (!sidebar) {
                    return;
                }

                body.classList.add('sidebar-open');

                if (sidebarToggle) {
                    sidebarToggle.setAttribute('aria-expanded', 'true');
                }

                if (sidebarOverlay) {
                    sidebarOverlay.setAttribute('aria-hidden', 'false');
                }
            }

            function closeSidebar() {
                body.classList.remove('sidebar-open');

                if (sidebarToggle) {
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }

                if (sidebarOverlay) {
                    sidebarOverlay.setAttribute('aria-hidden', 'true');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    if (body.classList.contains('sidebar-open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Escape' &&
                    body.classList.contains('sidebar-open')
                ) {
                    closeSidebar();
                }
            });

            document.querySelectorAll('#appSidebar .sidebar-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1200) {
                        closeSidebar();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1200) {
                    closeSidebar();
                }
            });
        });
    </script>

</body>
</html>