<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Nhúng các tệp tin cốt lõi
require_once 'includes/auth_functions.php';
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
        <!-- PHẦN HERO V2.0 -->
        <section class="hero-premium position-relative overflow-hidden">
            <div class="hero-bg-gradient"></div>
            <div class="container position-relative">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7 text-center text-lg-start animate-reveal">
                        <div class="glass-badge d-inline-block px-4 py-2 mb-4 rounded-pill">
                            <span class="text-primary-gradient fw-bold">Intelligence is here. 2026</span>
                        </div>
                        <h1 class="hero-title-main fw-bold">
                            iPhone 17 Pro.<br>
                            <span class="text-primary-gradient">Siêu trí tuệ. Siêu bứt phá.</span>
                        </h1>
                        <p class="h4 text-secondary mb-5 fw-light leading-relaxed" style="max-width: 550px;">
                            Sức mạnh vô song từ chip A19 Pro. Trải nghiệm kỷ nguyên AI toàn cầu ngay trong lòng bàn tay.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                            <a href="product.php" class="btn-apple btn-lg px-5 shadow-lg">Mua ngay</a>
                            <a href="product.php?category=Apple" class="btn-outline-apple btn-lg px-5">Xem thêm</a>
                        </div>
                    </div>
                    <div class="col-lg-5 position-relative animate-float">
                        <img src="assets/images/ai_ip17_pm.png" alt="iPhone 17" class="img-fluid" onerror="this.src='https://placehold.co/800x1000/transparent/333?text=iPhone+17+Pro'">
                    </div>
                </div>
            </div>
        </section>

        <!-- PHẦN SẢN PHẨM V2.0 -->
        <section class="py-huge">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
                    <div class="animate-reveal">
                        <h2 class="display-4 fw-bold mb-2">Tuyệt phẩm công nghệ.</h2>
                        <p class="text-secondary h5 fw-light">Khám phá những thiết bị mạnh mẽ nhất tại NHK Mobile.</p>
                    </div>
                    <a href="product.php" class="text-dark fw-bold text-decoration-none">Tất cả sản phẩm <i class="bi bi-arrow-right ms-1"></i></a>
                </div>

                <div class="row g-4">
                    <?php foreach ($featuredProducts as $index => $p): ?>
                    <div class="col-6 col-md-4 col-lg-3 animate-reveal" style="animation-delay: <?php echo $index * 0.08; ?>s">
                        <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="text-decoration-none">
                            <div class="card-glass-product h-100">
                                <div class="img-wrapper-premium">
                                    <img src="assets/images/<?php echo e($p['image']); ?>" class="img-fluid" alt="<?php echo e($p['name']); ?>" onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Device'">
                                </div>
                                <div class="p-4 pt-2">
                                    <span class="category-tag mb-1 d-inline-block"><?php echo e($p['category']); ?></span>
                                    <h6 class="fw-bold text-dark mb-2 text-truncate"><?php echo e($p['name']); ?></h6>
                                    <div class="d-flex align-items-center justify-content-between mt-3">
                                        <span class="price-premium"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                        <div class="btn-buy-mini btn-add-to-cart-ajax" data-product-id="<?php echo e($p['id']); ?>">
                                            <i class="bi bi-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
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
