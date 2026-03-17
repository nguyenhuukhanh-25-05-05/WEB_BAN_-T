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
                    <div class="glass-card p-5 rounded-5 bg-dark text-white shadow-2xl sticky-top" style="top: 120px;">
                        <h4 class="fw-bold mb-4">Tra cứu nhanh</h4>
                        <p class="small text-secondary-light mb-4">Nhập mã IMEI để kiểm tra thời hạn và lịch sử sửa chữa của máy.</p>
                        <div class="mb-4">
                             <input type="text" class="form-control form-control-lg rounded-pill px-4 bg-white bg-opacity-10 border-light text-white" placeholder="Tìm số IMEI...">
                        </div>
                        <button class="btn btn-premium-light w-100 rounded-pill py-3 fw-bold shadow-lg">Kiểm tra ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
