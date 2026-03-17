<?php 
require_once 'includes/db.php';
$pageTitle = "Chính sách Bảo hành | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<main>
    <!-- HERO: Premium Dark -->
    <section class="hero-premium position-relative overflow-hidden d-flex align-items-center" style="min-height: 40vh;">
        <div class="hero-bg-gradient"></div>
        <div class="container position-relative z-2 text-center text-lg-start animate-fade-in">
            <div class="glass-badge d-inline-block px-4 py-2 mb-4 rounded-pill">
                <span class="text-primary-gradient fw-bold">Dịch vụ hậu mãi chuẩn 5 sao</span>
            </div>
            <h1 class="display-3 fw-800 mb-0 tracking-tight hero-title-main">
                Chăm sóc tận tâm.<br>
                <span class="text-gradient">Bảo hành trọn đời.</span>
            </h1>
        </div>
    </section>

    <!-- CONTENT: Light Body -->
    <section class="py-huge bg-premium-light">
        <div class="container px-xl-5">
            <div class="row g-5">
                <div class="col-lg-8 animate-reveal">
                    <div class="mb-5">
                        <h3 class="display-6 fw-bold mb-4 text-dark">1. Thời hạn bảo hành</h3>
                        <p class="text-secondary h5 fw-light leading-relaxed mb-4">
                            Tất cả sản phẩm điện thoại di động được cung cấp bởi NHK Mobile đều được bảo hành <strong>12 tháng</strong> kể từ ngày mua hàng.
                        </p>
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <div class="glass-card p-4 rounded-4 bg-white shadow-sm h-100">
                                    <i class="bi bi-arrow-repeat text-primary fs-3 mb-3 d-block"></i>
                                    <h5 class="fw-bold">Lỗi 1 đổi 1</h5>
                                    <p class="small text-secondary mb-0">Áp dụng trong 30 ngày đầu cho bất kỳ lỗi phần cứng nào từ nhà sản xuất.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="glass-card p-4 rounded-4 bg-white shadow-sm h-100">
                                    <i class="bi bi-battery-charging text-primary fs-3 mb-3 d-block"></i>
                                    <h5 class="fw-bold">Bảo hành Pin</h5>
                                    <p class="small text-secondary mb-0">Pin máy mới được bảo hành 12 tháng, máy cũ 6 tháng nếu dung lượng dưới 80%.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 mt-8">
                        <h3 class="display-6 fw-bold mb-4 text-dark">2. Điều kiện bảo hành</h3>
                        <div class="glass-card p-4 rounded-4 bg-white shadow-sm border-start border-primary border-4">
                            <p class="text-secondary leading-relaxed mb-0">
                                Sản phẩm phải còn nguyên vẹn, không có dấu hiện can thiệp phần cứng trái phép. Tem bảo hành của NHK Mobile phải còn nguyên, không bị rách hoặc tẩy xóa. Chúng tôi từ chối bảo hành các trường hợp rơi vỡ, vào nước hoặc can thiệp phần mềm ngoài hệ thống.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 animate-reveal" style="animation-delay: 0.2s">
                    <div class="glass-card p-5 rounded-5 shadow-2xl sticky-top" style="top: 120px; background: linear-gradient(145deg, #1d1d1f 0%, #3a3a40 100%); border: 1px solid rgba(255,255,255,0.1);">
                        <h4 class="fw-bold mb-4" style="color: #ffffff !important;">Tra cứu nhanh</h4>
                        <p class="small mb-4" style="color: rgba(255,255,255,0.7) !important;">Nhập mã IMEI (15 số) để kiểm tra thời hạn và lịch sử sửa chữa của máy.</p>
                        <div class="mb-4">
                             <input type="text" id="imeiInput" class="form-control form-control-lg rounded-pill px-4" style="background: rgba(255,255,255,0.95); border: none; color: #1d1d1f !important; font-size: 15px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);" placeholder="Ví dụ: 3584820912...">
                        </div>
                        <button id="btnCheckImei" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg d-flex justify-content-center align-items-center gap-2 transition-all">
                            <span id="btnText">Kiểm tra ngay</span>
                            <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <!-- Result Display Area -->
                        <div id="imeiResult" class="mt-4 animate-fade-in" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('btnCheckImei').addEventListener('click', function() {
    const input = document.getElementById('imeiInput').value.trim();
    const resultDiv = document.getElementById('imeiResult');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    
    // Reset
    resultDiv.style.display = 'none';
    resultDiv.innerHTML = '';
    
    // Validation
    if(input.length < 5) {
        resultDiv.innerHTML = `<div class="p-3 rounded-4" style="background: rgba(255,60,48,0.1); border: 1px solid rgba(255,60,48,0.3); color: #ff3b30 !important; font-size: 14px;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Vui lòng nhập số IMEI hợp lệ.</div>`;
        resultDiv.style.display = 'block';
        return;
    }

    // Loading State
    this.disabled = true;
    btnText.textContent = 'Đang tra cứu...';
    btnSpinner.classList.remove('d-none');
    
    // Fake API call (Timeout 1.5s for realistic feeling)
    setTimeout(() => {
        this.disabled = false;
        btnText.textContent = 'Kiểm tra ngay';
        btnSpinner.classList.add('d-none');
        
        // Randomly succeed or expire based on input length just to show different states
        const isExpired = Math.random() > 0.6;
        const purchaseDate = new Date(Date.now() - Math.floor(Math.random() * 10000000000) - 10000000000); // 1-2 years ago randomly
        const formattedDate = purchaseDate.toLocaleDateString('vi-VN');
        
        let htmlContent = '';
        if (isExpired) {
            htmlContent = `
                <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(255,149,0,0.15); color: #ff9500;">
                            <i class="bi bi-shield-exclamation fs-5"></i>
                        </div>
                        <h6 class="mb-0 ms-3 fw-bold" style="color: #fff !important;">Hết hạn bảo hành</h6>
                    </div>
                    <p class="small mb-1" style="color: rgba(255,255,255,0.6) !important;">IMEI: <strong style="color: #fff !important;">${input}</strong></p>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.6) !important;">Hết hạn: <strong style="color: #fff !important;">${formattedDate}</strong></p>
                </div>
            `;
        } else {
            htmlContent = `
                <div class="p-4 rounded-4" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(52,199,89,0.2); color: #32d74b;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <h6 class="mb-0 ms-3 fw-bold" style="color: #fff !important;">Đang được bảo hành</h6>
                    </div>
                    <p class="small mb-1" style="color: rgba(255,255,255,0.6) !important;">IMEI: <strong style="color: #fff !important;">${input}</strong></p>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.6) !important;">Dịch vụ: <strong style="color: #fff !important;">Bảo hành Toàn diện VIP</strong></p>
                </div>
            `;
        }
        
        resultDiv.innerHTML = htmlContent;
        resultDiv.style.display = 'block';
    }, 1200);
});
</script>

<?php include 'includes/footer.php'; ?>
