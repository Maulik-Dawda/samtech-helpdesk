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
        border: 4px solid rgba(15, 23, 42, .1);
        border-top-color: #0f172a;
        border-right-color: #334155;
        animation: samtechSpin .8s linear infinite;
        position: relative;
    }

    .samtech-loader-circle::after {
        content: "";
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        border: 3px solid rgba(15, 23, 42, .08);
        border-bottom-color: #475569;
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

        // Auto-hide fallback after 15 seconds
        if (samtechLoaderTimer) {
            clearTimeout(samtechLoaderTimer);
        }
        samtechLoaderTimer = setTimeout(function() {
            hideSamtechLoader();
        }, 15000);
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
    window.addEventListener('pageshow', function() {
        hideSamtechLoader();
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

            form.addEventListener('submit', function(e) {

                if (e.defaultPrevented) {
                    return;
                }

                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    return;
                }

                const action = (form.getAttribute('action') || '').toLowerCase();
                const currentPath = window.location.pathname.toLowerCase();

                let message = 'Processing your request...';

                if (action.includes('admin-login')) {
                    message = 'Verifying admin access...';
                } else if (action.includes('login')) {
                    message = 'Verifying credentials...';
                } else if (action.includes('otp') || action.includes('mfa')) {
                    message = 'Verifying security code...';
                } else if (action.includes('forgot-password')) {
                    message = 'Sending verification code...';
                } else if (action.includes('reset-password')) {
                    message = 'Updating password...';
                } else if (action.includes('reply') || currentPath.includes('show')) {
                    message = 'Posting reply & sending notifications...';
                } else if (action.includes('status') || action.includes('priority') || action.includes('assign')) {
                    message = 'Updating ticket details...';
                } else if (action.includes('tickets/store') || action.includes('tickets/create')) {
                    message = 'Creating support ticket...';
                } else if (action.includes('user') && (action.includes('create') || action.includes('store'))) {
                    message = 'Creating user account...';
                } else if (action.includes('organization') && (action.includes('create') || action.includes('store'))) {
                    message = 'Creating organization...';
                } else if (action.includes('user') && action.includes('update')) {
                    message = 'Updating user details...';
                } else if (action.includes('profile')) {
                    message = 'Saving profile changes...';
                } else if (action.includes('password')) {
                    message = 'Updating password...';
                }

                showSamtechLoader(message);

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    setTimeout(function() {
                        submitBtn.disabled = true;
                        if (submitBtn.tagName === 'BUTTON') {
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                        }
                    }, 50);
                }
            });

        });

    });
</script>