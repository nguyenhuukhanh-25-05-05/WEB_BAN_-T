<?php 
require_once 'includes/db.php';
$pageTitle = "Chính sách Bảo hành | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container py-5 px-xl-5">
            <h1 class="display-4 fw-bold mb-5">Chính sách bảo hành.</h1>
            
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="mb-5">
                        <h3 class="fw-bold mb-4">1. Thời hạn bảo hành</h3>
                        <p class="text-secondary leading-relaxed mb-4">
                            Tất cả sản phẩm điện thoại iPhone, Samsung, Xiaomi được cung cấp bởi NHK Mobile đều được bảo hành <strong>12 tháng</strong> kể từ ngày kích hoạt hoặc ngày mua hàng (tùy điều kiện nào đến trước).
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check2 text-primary me-2"></i> Lỗi 1 đổi 1 trong 30 ngày đầu nêú có lỗi phần cứng từ nhà sản xuất.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-primary me-2"></i> Bảo hành pin 6 tháng cho máy cũ, 12 tháng cho máy mới.</li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h3 class="fw-bold mb-4">2. Điều kiện bảo hành</h3>
                        <p class="text-secondary leading-relaxed">
                            Sản phẩm phải còn nguyên vẹn, không có dấu hiện can thiệp phần cứng trái phép. Tem bảo hành của NHK Mobile phải còn nguyên, không bị rách hoặc tẩy xóa.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bg-light rounded-5 p-5">
                        <h5 class="fw-bold mb-4">Tra cứu nhanh</h5>
                        <p class="small text-secondary mb-4">Nhập số IMEI hoặc Số điện thoại để kiểm tra tình trạng bảo hành máy của bạn.</p>
                        <input type="text" class="form-control rounded-pill px-4 mb-3 border-0 py-3" placeholder="Số IMEI...">
                        <button class="btn btn-dark w-100 rounded-pill py-3 fw-bold">Kiểm tra ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
