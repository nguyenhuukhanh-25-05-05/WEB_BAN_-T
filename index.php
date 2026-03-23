<?php
// Bắt đầu phiên làm việc để sử dụng SESSION (Đăng nhập, Giỏ hàng)
session_start();

// Nhúng file kết nối cơ sở dữ liệu Postgres (Sử dụng PDO)
require_once 'includes/db.php';

/**
 * TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ LÊN TRANG CHỦ
 */

// 1. Lấy danh sách 8 sản phẩm tiêu biểu (Ưu tiên nổi bật trước, sau đó mới đến mới nhất)
$stmt = $pdo->query("SELECT * FROM products ORDER BY is_featured DESC, created_at DESC LIMIT 8");
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
        <!-- PHẦN HERO: Đỉnh cao công nghệ - Phong cách Glassmorphism -->
        <section class="hero-premium position-relative overflow-hidden min-vh-100 d-flex align-items-center">
            <div class="hero-bg-gradient"></div>
            <div class="container position-relative z-2">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6 text-center text-lg-start animate-fade-in">
                        <div class="glass-badge d-inline-block px-4 py-2 mb-4 rounded-pill">
                            <span class="text-primary-gradient fw-bold">Thế hệ AI mới nhất 2026</span>
                        </div>
                        <h1 class="display-1 fw-800 mb-4 tracking-tight hero-title-main">
                            iPhone 17 Pro.<br>
                            <span class="text-gradient">Được tạo bởi Trí tuệ.</span>
                        </h1>
                        <p class="h4 text-secondary mb-5 fw-light leading-relaxed max-w-500">
                            Sức mạnh vượt bậc từ chip A19 Pro. Trải nghiệm hệ sinh thái AI toàn cầu ngay trên tay bạn.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                            <a href="product.php" class="btn btn-premium-dark btn-lg px-5 py-3 shadow-lg">Sở hữu ngay</a>
                            <a href="product.php?category=Apple" class="btn btn-premium-glass btn-lg px-5 py-3">Khám phá công nghệ</a>
                        </div>
                    </div>
                    <div class="col-lg-6 position-relative animate-float">
                        <div class="hero-image-glow"></div>
                        <img src="assets/images/ai_ip17_pm.png" alt="iPhone 17" class="img-fluid hero-image-main" onerror="this.src='https://placehold.co/800x1000/transparent/333?text=iPhone+17+Pro'">
                    </div>
                </div>
            </div>
            <div class="scroll-indicator">
                <div class="mouse"></div>
            </div>
        </section>

        <!-- PHẦN SẢN PHẨM MỚI NHẤT: White Section Transition -->
        <section class="py-huge bg-premium-light">
            <div class="container px-xl-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
                    <div class="animate-reveal">
                        <h2 class="display-4 fw-bold text-dark mb-2">Đỉnh phẩm công nghệ.</h2>
                        <p class="text-secondary h5 fw-light">Những thiết bị mạnh mẽ nhất vừa cập bến NHK Mobile.</p>
                    </div>
                    <a href="product.php" class="btn-explore text-dark">Xem tất cả <i class="bi bi-chevron-right ms-2 fs-small"></i></a>
                </div>

                <div class="row g-4 pt-4">
                    <?php if (empty($featuredProducts)): ?>
                        <div class="col-12 text-center py-5 text-secondary">
                             <div class="glass-card p-5 rounded-5 border-dashed">
                                  <i class="bi bi-box-seam display-1 mb-4 opacity-25"></i>
                                  <h3>Dữ liệu đang được đồng bộ...</h3>
                                  <p>Vui lòng F5 để web tự động khởi tạo dữ liệu mẫu.</p>
                             </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($featuredProducts as $index => $p): ?>
                        <div class="col-6 col-md-4 col-lg-3 animate-reveal" style="animation-delay: <?php echo $index * 0.1; ?>s">
                            <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                                <div class="card-glass-product h-100 p-4 transition-all">
                                    <div class="img-wrapper-premium mb-4 rounded-4 overflow-hidden shadow-inner">
                                        <img src="assets/images/<?php echo $p['image']; ?>" class="img-fluid" alt="<?php echo $p['name']; ?>" onerror="this.src='https://placehold.co/300x400/111/fff?text=Phone'">
                                    </div>
                                    <div class="card-content-premium">
                                        <span class="category-tag mb-2 d-inline-block"><?php echo $p['category']; ?></span>
                                        <h5 class="fw-bold text-dark mb-2 text-truncate-2"><?php echo $p['name']; ?></h5>
                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <span class="price-premium"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                            <div class="btn-buy-mini shadow-sm"><i class="bi bi-plus-lg"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- PHẦN TRẢI NGHIỆM: Immersive Light Section -->
        <section class="py-huge position-relative bg-premium-light overflow-hidden">
             <div class="bg-blur-blob blob-1"></div>
             <div class="bg-blur-blob blob-2"></div>
             <div class="container position-relative z-1">
                  <div class="glass-panel p-5 p-lg-10 rounded-max overflow-hidden border-light">
                       <div class="row g-5 align-items-center">
                            <div class="col-lg-6">
                                 <h2 class="display-3 fw-bold mb-4 tracking-tight">NHKMOBILE.<br><span class="text-primary-gradient">Đồng hành cùng bạn.</span></h2>
                                 <p class="h4 text-secondary mb-5 fw-light">Chúng tôi không chỉ bán thiết bị. Chúng tôi mang đến trải nghiệm hậu mãi chuẩn 5 sao với trung tâm bảo hành hiện đại nhất.</p>
                                 
                                 <div class="row g-4">
                                      <div class="col-6">
                                           <div class="d-flex align-items-center gap-3">
                                                <div class="icon-circle shadow-sm"><i class="bi bi-shield-check"></i></div>
                                                <div>
                                                     <div class="h5 fw-bold mb-0">1 Đổi 1</div>
                                                     <p class="small text-secondary mb-0">Trong 30 ngày</p>
                                                </div>
                                           </div>
                                      </div>
                                      <div class="col-6">
                                           <div class="d-flex align-items-center gap-3">
                                                <div class="icon-circle shadow-sm"><i class="bi bi-truck"></i></div>
                                                <div>
                                                     <div class="h5 fw-bold mb-0">Miễn Phí</div>
                                                     <p class="small text-secondary mb-0">Giao hàng toàn quốc</p>
                                                </div>
                                           </div>
                                      </div>
                                 </div>
                            </div>
                            <div class="col-lg-6">
                                 <div class="video-preview-glass rounded-5 shadow-2xl position-relative">
                                      <img src="https://images.unsplash.com/photo-1616348436168-de43ad0db179?auto=format&fit=crop&q=80&w=1000" class="img-fluid rounded-5" alt="Store">
                                      <div class="play-button-glass"><i class="bi bi-play-fill"></i></div>
                                 </div>
                            </div>
                       </div>
                  </div>
             </div>
        </section>
    </main>

<?php 
// Nhúng phần Footer (Thông tin liên hệ, Thẻ đóng body...)
include 'includes/footer.php'; 
?>
