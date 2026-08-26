<style>
    #samtechLoaderOverlay {
        position: fixed;
        inset: 0;
        background: rgba(248, 250, 252, 0.95);
        backdrop-filter: blur(10px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    #samtechLoaderOverlay.show {
        display: flex;
    }

    .samtech-loader-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        text-align: center;
        min-width: 240px;
        box-shadow:
            0 20px 50px rgba(15, 23, 42, .10),
            0 8px 20px rgba(15, 23, 42, .06);
    }

    .samtech-loader-circle {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 50%;
        border: 4px solid rgba(177, 233, 111, .2);
        border-top-color: #b1e96f;
        border-right-color: #6cb33f;
        animation: samtechSpin .8s linear infinite;
        position: relative;
    }

    .samtech-loader-circle::after {
        content: "";
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        border: 3px solid rgba(177, 233, 111, .15);
        border-bottom-color: #6cb33f;
        animation: samtechSpinReverse 1.2s linear infinite;
    }

    .samtech-loader-title {
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 5px;
    }

    .samtech-loader-text {
        font-size: 13px;
        color: #64748b;
    }

    @keyframes samtechSpin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes samtechSpinReverse {
        from {
            transform: rotate(360deg);
        }

        to {
            transform: rotate(0deg);
        }
    }
</style>

<div id="samtechLoaderOverlay">
    <div class="samtech-loader-box">

        <div class="samtech-loader-circle"></div>

        <div class="samtech-loader-title">
            Samtech Helpdesk
        </div>

        <div class="samtech-loader-text" id="samtechLoaderText">
            Processing your request...
        </div>

    </div>
</div>

<script>
    let samtechLoaderTimer = null;

    function showSamtechLoader(message = 'Processing your request...') {
        const loader = document.getElementById('samtechLoaderOverlay');
        const text = document.getElementById('samtechLoaderText');

        if (text) {
            text.textContent = message;
        }

        if (loader) {
            loader.classList.add('show');
        }

        // Auto-hide fallback after 5 seconds
        if (samtechLoaderTimer) {
            clearTimeout(samtechLoaderTimer);
        }
        samtechLoaderTimer = setTimeout(function() {
            hideSamtechLoader();
        }, 5000);
    }

    function hideSamtechLoader() {
        const loader = document.getElementById('samtechLoaderOverlay');

        if (loader) {
            loader.classList.remove('show');
        }

        if (samtechLoaderTimer) {
            clearTimeout(samtechLoaderTimer);
            samtechLoaderTimer = null;
        }
    }

    // Always hide loader on load, DOMContentLoaded, pageshow (BFCache), and popstate
    document.addEventListener('DOMContentLoaded', hideSamtechLoader);
    window.addEventListener('load', hideSamtechLoader);
    window.addEventListener('pageshow', function(e) {
        hideSamtechLoader();
        if (e.persisted) {
            window.location.reload();
        }
    });
    window.addEventListener('popstate', function() {
        hideSamtechLoader();
    });

    // Dashboard root navigation lock (Prevents back-navigation to login pages from Dashboard)
    (function() {
        const currentPath = window.location.pathname;
        const isDashboardPage = currentPath.includes('dashboard');

        if (isDashboardPage && window.history && window.history.pushState) {
            window.history.pushState(null, "", window.location.href);

            window.addEventListener('popstate', function(e) {
                hideSamtechLoader();
                window.history.pushState(null, "", window.location.href);
            });
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('form').forEach(function(form) {

            if (form.hasAttribute('data-no-loader')) {
                return;
            }

            form.addEventListener('submit', function(e) {

                if (e.defaultPrevented) {
                    return;
                }

                let message = 'Processing your request...';

                const action = form.getAttribute('action') || '';

                if (action.includes('admin-login')) {
                    message = 'Verifying admin access...';
                } else if (action.includes('login')) {
                    message = 'Verifying credentials...';
                } else if (action.includes('otp')) {
                    message = 'Verifying OTP...';
                } else if (action.includes('forgot-password')) {
                    message = 'Sending verification code...';
                } else if (action.includes('reset-password')) {
                    message = 'Updating password...';
                } else if (action.includes('tickets')) {
                    message = 'Processing ticket...';
                } else if (action.includes('reports')) {
                    message = 'Preparing report...';
                } else if (action.includes('delete')) {
                    message = 'Deleting user...';
                }

                showSamtechLoader(message);
            });

        });

        document.querySelectorAll('a').forEach(function(link) {

            link.addEventListener('click', function(e) {

                const href = link.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    link.hasAttribute('data-bs-toggle') ||
                    link.hasAttribute('data-bs-dismiss') ||
                    link.hasAttribute('download') ||
                    link.getAttribute('target') === '_blank' ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    e.defaultPrevented
                ) {
                    return;
                }

                showSamtechLoader('Loading...');
            });

        });

    });
</script>