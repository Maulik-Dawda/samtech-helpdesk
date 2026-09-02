<?php require_once ROOT_PATH . "/app/Views/layouts/header.php"; ?>

<style>
    .scanner-hero-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        max-width: 580px;
        margin: 40px auto;
        padding: 40px 28px;
        text-align: center;
    }

    .scanner-badge-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin-bottom: 20px;
    }

    .btn-call-owner {
        background: #0f172a;
        color: #ffffff;
        border-radius: 50px;
        padding: 16px 36px;
        font-size: 18px;
        font-weight: 700;
        border: none;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
        transition: all 0.2s ease;
    }

    .btn-call-owner:hover {
        background: #1e293b;
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* 90% Screen Coverage Popup Overlay */
    .call-popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(8px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .call-popup-overlay.show {
        display: flex;
    }

    .call-popup-modal {
        background: #ffffff;
        width: 90vw;
        max-width: 540px;
        height: 88vh;
        max-height: 88vh;
        border-radius: 28px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: popupSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    @keyframes popupSlideUp {
        from {
            transform: translateY(40px) scale(0.96);
            opacity: 0;
        }

        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .call-popup-header {
        padding: 24px 28px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .call-popup-body {
        padding: 28px;
        overflow-y: auto;
        flex: 1;
    }

    .call-popup-footer {
        padding: 20px 28px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }

    .close-popup-btn {
        background: #f1f5f9;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        color: #475569;
        font-size: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .close-popup-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .plate-digit-input {
        letter-spacing: 12px;
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 16px !important;
    }

    .plate-digit-input:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1) !important;
    }

    .phone-number-input {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 16px !important;
    }

    .phone-number-input:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1) !important;
    }
</style>

<div class="container py-4">

    <!-- Scanner Hero Card -->
    <div class="scanner-hero-card">
        <div class="scanner-badge-icon">
            <i class="bi bi-car-front-fill"></i>
        </div>

        <h3 class="fw-bold text-dark mb-2">Vehicle Scanner Contact</h3>
        <p class="text-muted small mb-4">
            Scanner Code: <strong class="text-dark"><?= $scannerCode; ?></strong><br>
            Need to reach the vehicle owner? Tap below to connect safely via masked call.
        </p>

        <button type="button" id="btnOpenCallPopup" class="btn btn-call-owner">
            <i class="bi bi-telephone-fill me-2"></i> Call the Owner
        </button>
    </div>

</div>

<!-- 90% Screen Height/Width Modal Popup -->
<div id="callOwnerPopupOverlay" class="call-popup-overlay">
    <div class="call-popup-modal">

        <div class="call-popup-header">
            <div>
                <h5 class="fw-bold text-dark mb-0">Call Vehicle Owner</h5>
                <small class="text-muted">Secure Masked Calling System</small>
            </div>
            <button type="button" class="close-popup-btn" id="btnClosePopup">&times;</button>
        </div>

        <div class="call-popup-body">

            <div id="popupAlert" class="alert alert-danger d-none"></div>

            <form id="callMaskingForm">

                <!-- 1. Last 4 Digits of Number Plate -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold text-dark fs-6 mb-2">
                        Last 4 Digits of Number Plate <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="plate_last_4"
                        id="plateLast4Input"
                        maxlength="4"
                        pattern="\d{4}"
                        placeholder="4892"
                        class="form-control form-control-lg text-center plate-digit-input"
                        required
                        autocomplete="off">
                    <div class="form-text small mt-2 text-muted">
                        Enter the exact 4 digits from the vehicle number plate.
                    </div>
                </div>

                <!-- 2. User Phone Number for Call Masking -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold text-dark fs-6 mb-2">
                        Your Phone Number <span class="text-danger">*</span>
                    </label>
                    <input
                        type="tel"
                        name="caller_phone"
                        id="callerPhoneInput"
                        placeholder="+971 50 123 4567"
                        class="form-control form-control-lg text-center phone-number-input"
                        required>
                    <div class="form-text small mt-2 text-muted">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i>
                        Your number is stored securely and masked during the call.
                    </div>
                </div>

                <input type="hidden" name="scanner_code" value="<?= $scannerCode; ?>">

            </form>

        </div>

        <div class="call-popup-footer">
            <button type="button" id="btnContinueCall" class="btn btn-dark btn-lg w-100 py-3 rounded-pill fw-bold fs-5">
                Continue to Call Owner <i class="bi bi-arrow-right-circle-fill ms-2"></i>
            </button>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const popupOverlay = document.getElementById('callOwnerPopupOverlay');
    const btnOpenPopup = document.getElementById('btnOpenCallPopup');
    const btnClosePopup = document.getElementById('btnClosePopup');
    const btnContinueCall = document.getElementById('btnContinueCall');
    const plateInput = document.getElementById('plateLast4Input');
    const phoneInput = document.getElementById('callerPhoneInput');
    const popupAlert = document.getElementById('popupAlert');

    function openPopup() {
        if (popupOverlay) {
            popupOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            if (plateInput) plateInput.focus();
        }
    }

    function closePopup() {
        if (popupOverlay) {
            popupOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (btnOpenPopup) {
        btnOpenPopup.addEventListener('click', openPopup);
    }

    if (btnClosePopup) {
        btnClosePopup.addEventListener('click', closePopup);
    }

    if (popupOverlay) {
        popupOverlay.addEventListener('click', function (e) {
            if (e.target === popupOverlay) {
                closePopup();
            }
        });
    }

    // Plate input restrict to 4 digits
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
    }

    if (btnContinueCall) {
        btnContinueCall.addEventListener('click', function () {
            popupAlert.classList.add('d-none');
            popupAlert.textContent = '';

            const plate = plateInput ? plateInput.value.trim() : '';
            const phone = phoneInput ? phoneInput.value.trim() : '';

            if (!plate || plate.length !== 4) {
                popupAlert.textContent = 'Please enter the 4-digit number plate code.';
                popupAlert.classList.remove('d-none');
                if (plateInput) plateInput.focus();
                return;
            }

            if (!phone || phone.length < 7) {
                popupAlert.textContent = 'Please enter a valid phone number for call masking.';
                popupAlert.classList.remove('d-none');
                if (phoneInput) phoneInput.focus();
                return;
            }

            const originalBtnText = btnContinueCall.innerHTML;
            btnContinueCall.disabled = true;
            btnContinueCall.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Storing & Connecting Call...';

            const formData = new FormData();
            formData.append('plate_last_4', plate);
            formData.append('caller_phone', phone);
            formData.append('scanner_code', '<?= $scannerCode; ?>');

            fetch('<?= BASE_URL ?>/api/call-owner/submit', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.tel_url) {
                    btnContinueCall.innerHTML = '<i class="bi bi-telephone-forward-fill me-2"></i> Dialing DID Number...';

                    setTimeout(function () {
                        // Directly launch native phone dialer with DID number
                        window.location.href = data.tel_url;
                        btnContinueCall.disabled = false;
                        btnContinueCall.innerHTML = originalBtnText;
                    }, 500);
                } else {
                    popupAlert.textContent = data.message || 'Unable to connect call. Please try again.';
                    popupAlert.classList.remove('d-none');
                    btnContinueCall.disabled = false;
                    btnContinueCall.innerHTML = originalBtnText;
                }
            })
            .catch(err => {
                console.error('Call masking error:', err);
                popupAlert.textContent = 'An error occurred while connecting. Please try again.';
                popupAlert.classList.remove('d-none');
                btnContinueCall.disabled = false;
                btnContinueCall.innerHTML = originalBtnText;
            });
        });
    }
});
</script>

<?php require_once ROOT_PATH . "/app/Views/layouts/footer.php"; ?>
