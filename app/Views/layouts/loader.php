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
        transition: all .3s ease;
    }

    #samtechLoaderOverlay.show {
        display: flex;
    }

    .samtech-loader-box {
        background: #ffffff;
        border-radius: 24px;
        padding: 35px;
        text-align: center;
        min-width: 280px;
        box-shadow:
            0 25px 60px rgba(15, 23, 42, .12),
            0 10px 25px rgba(15, 23, 42, .08);
    }

    .samtech-loader-circle {
        width: 100px;
        height: 100px;
        margin: 0 auto 18px;
        position: relative;
        animation: samtechPulse 1.6s ease-in-out infinite;
    }

    .samtech-loader-circle::before {
        content: "";
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 4px solid rgba(177, 233, 111, 0.25);
        border-top: 4px solid #b1e96f;
        border-right: 4px solid #6cb33f;
        animation: samtechSpin 1s linear infinite;
    }

    .samtech-loader-circle img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
        background: #ffffff;
        padding: 12px;
    }

    .samtech-loader-title {
        font-size: 17px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
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

    @keyframes samtechPulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }
</style>

<div id="samtechLoaderOverlay">
    <div class="samtech-loader-box">

        <div class="samtech-loader-circle">
            <img
                src="<?= BASE_URL ?>/assets/images/samtech-icon.png"
                alt="Samtech">
        </div>

        <div class="samtech-loader-title">
            Samtech Helpdesk
        </div>

        <div class="samtech-loader-text" id="samtechLoaderText">
            Processing your request...
        </div>

    </div>
</div>

<script>
function showSamtechLoader(message = 'Processing your request...')
{
    const loader = document.getElementById('samtechLoaderOverlay');
    const text = document.getElementById('samtechLoaderText');

    if (text) {
        text.textContent = message;
    }

    if (loader) {
        loader.classList.add('show');
    }
}

function hideSamtechLoader()
{
    const loader = document.getElementById('samtechLoaderOverlay');

    if (loader) {
        loader.classList.remove('show');
    }
}

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('form').forEach(function(form) {

        form.addEventListener('submit', function () {

            let message = 'Processing your request...';

            const action = form.getAttribute('action') || '';

            if (action.includes('login')) {
                message = 'Verifying credentials...';
            }

            if (action.includes('otp')) {
                message = 'Verifying OTP...';
            }

            if (action.includes('forgot-password')) {
                message = 'Sending verification code...';
            }

            if (action.includes('reset-password')) {
                message = 'Updating password...';
            }

            showSamtechLoader(message);
        });

    });

    document.querySelectorAll('a').forEach(function(link) {

        link.addEventListener('click', function () {

            const href = link.getAttribute('href');

            if (
                !href ||
                href === '#' ||
                href.startsWith('javascript:') ||
                link.hasAttribute('data-bs-toggle') ||
                link.hasAttribute('target')
            ) {
                return;
            }

            showSamtechLoader('Loading...');
        });

    });

});
</script>