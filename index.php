<?php
// Bắt đầu phiên làm việc để sử dụng SESSION (Đăng nhập, Giỏ hàng)
session_start();

// Nhúng file kết nối cơ sở dữ liệu Postgres (Sử dụng PDO)
require_once 'includes/db.php';

/**
 * TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ LÊN TRANG CHỦ
 */

// 1. Lấy danh sách 8 sản phẩm tiêu biểu (Sản phẩm mới nhất)
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll();

// 2. Lấy danh sách sản phẩm Apple nổi bật
$stmtApple = $pdo->query("SELECT * FROM products WHERE category = 'Apple' LIMIT 4");
$appleProducts = $stmtApple->fetchAll();

// Cấu hình các thông tin cơ bản cho trang
$pageTitle = "NHK Mobile | Apple Authorized Reseller"; // Tiêu đề thẻ <title>
$basePath = ""; // Đường dẫn gốc (dùng cho các file include)

// Nhúng phần Header (Nav, Link CSS...)
include 'includes/header.php';
?>

    <main>
        <!-- PHẦN HERO: Giới thiệu banner chính -->
        <section class="hero-section text-center d-flex align-items-center">
            <div class="container py-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-4 shadow-sm fw-bold">Thế hệ mới nhất</span>
                        <h1 class="hero-title mb-4">iPhone 16 Pro.<br><span class="text-secondary opacity-75">Bọc bởi Titanium.</span></h1>
                        <p class="hero-desc text-secondary mb-5 max-w-700 mx-auto">Sở hữu ngay siêu phẩm công nghệ đỉnh cao với hiệu năng vượt trội và hệ thống camera chuyên nghiệp.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="product.php" class="btn btn-dark btn-lg rounded-pill px-5 py-3 fw-bold shadow">Khám phá ngay</a>
                            <a href="product.php?category=Apple" class="btn btn-outline-dark btn-lg rounded-pill px-5 py-3 fw-bold">Tìm hiểu thêm</a>
                        </div>
                    </div>
                    <!-- Hình ảnh mô phỏng máy -->
                    <div class="col-12 mt-5 pt-lg-5">
                         <img src="assets/images/ai_ip17_pm.png" alt="iPhone 16" class="img-fluid floating-phone" onerror="this.src='https://placehold.co/800x400?text=Premium+Phone'">
                    </div>
                </div>
            </div>
        </section>

        <!-- PHẦN SẢN PHẨM MỚI NHẤT -->
        <section class="py-5 bg-white">
            <div class="container px-xl-5">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <div>
                        <h2 class="display-6 fw-bold mb-0">Sản phẩm mới.</h2>
                        <p class="text-secondary mt-2">Cập nhật những công nghệ di động mới nhất từ NHK Mobile.</p>
                    </div>
                    <a href="product.php" class="text-decoration-none fw-bold text-primary">Xem tất cả <i class="bi bi-arrow-right scale-btn"></i></a>
                </div>

                <div class="row g-4 justify-content-center">
                    <!-- Vòng lặp PHP Duyệt qua mảng $featuredProducts lấy từ DB -->
                    <?php if (empty($featuredProducts)): ?>
                        <div class="col-12 text-center py-4">Chưa có sản phẩm nào trong hệ thống.</div>
                    <?php else: ?>
                        <?php foreach ($featuredProducts as $p): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <!-- Link sang chi tiết sản phẩm truyền ID -->
                            <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                                <div class="card h-100 border-0 product-card-hover bg-light p-3 rounded-5 transition-all">
                                    <div class="text-center mb-3 p-3 bg-white rounded-5 shadow-sm">
                                        <img src="assets/images/<?php echo $p['image']; ?>" class="img-fluid" alt="<?php echo $p['name']; ?>" style="max-height: 200px;" onerror="this.src='https://placehold.co/200x250?text=Phone'">
                                    </div>
                                    <div class="card-body p-0 pt-3">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate"><?php echo $p['name']; ?></h6>
                                        <p class="card-text text-secondary small mb-3"><?php echo $p['category']; ?></p>
                                        <p class="text-primary fw-bold mb-0 h5"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- PHẦN GIỚI THIỆU TRẢI NGHIỆM (Branding) -->
        <section class="py-5 mb-5 bg-light rounded-5 mx-3 mx-lg-5 overflow-hidden">
             <div class="container py-lg-5">
                  <div class="row align-items-center g-5">
                       <div class="col-lg-6 ps-lg-5">
                            <h2 class="display-5 fw-bold mb-4">Trải nghiệm mua sắm đẳng cấp.</h2>
                            <p class="text-secondary leading-relaxed mb-5">Chúng tôi không chỉ bán điện thoại, chúng tôi mang tới một hệ sinh thái hỗ trợ tận tâm với chính sách bảo hành 1 đổi 1 và hỗ trợ trả góp 0% cực nhanh.</p>
                            <div class="row g-4">
                                 <div class="col-6">
                                      <div class="h4 fw-bold text-dark mb-1">100%</div>
                                      <p class="small text-secondary">Hàng chính hãng</p>
                                 </div>
                                 <div class="col-6">
                                      <div class="h4 fw-bold text-dark mb-1">24/7</div>
                                      <p class="small text-secondary">Hỗ trợ kỹ thuật</p>
                                 </div>
                            </div>
                       </div>
                       <div class="col-lg-6">
                            <img src="https://placehold.co/800x600/333/fff?text=Store+Experience" class="img-fluid rounded-5 shadow-lg">
                       </div>
                  </div>
             </div>
        </section>
    </main>

<?php 
// Nhúng phần Footer (Thông tin liên hệ, Thẻ đóng body...)
include 'includes/footer.php'; 
?>
