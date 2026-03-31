<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth_functions.php';
require_once 'includes/db.php';

// 1. Featured Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY is_featured DESC, created_at DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll();

$pageTitle = "NHK Mobile | Apple Authorized Reseller";
$basePath = "";

include 'includes/header.php';
?>

<main>
    <!-- Cinematic Hero Section -->
    <section class="section-padding overflow-hidden position-relative bg-premium-light">
        <div class="container py-huge">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 text-center text-lg-start animate-reveal">
                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-4 py-2 mb-4">MỚI NHẤT 2026</span>
                    <h1 class="hero-title fw-800 text-gradient mb-4">
                        iPhone 17 Pro.<br>
                        Siêu trí tuệ. Siêu bứt phá.
                    </h1>
                    <p class="hero-subtitle h4 fw-light text-secondary mb-5 leading-relaxed">
                        Sức mạnh vô song từ chip A19 Pro. Trải nghiệm kỷ nguyên AI toàn cầu ngay trong lòng bàn tay.
                    </p>
                    <div class="hero-actions gap-4 d-flex justify-content-center justify-content-lg-start">
                        <a href="product.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg">Mua ngay</a>
                        <a href="product.php?category=Apple" class="btn btn-outline-dark btn-lg rounded-pill px-5 py-3 fw-bold hover-lift">Xem thêm <i class="bi bi-chevron-right ms-2 small"></i></a>
                    </div>
                </div>
                <div class="col-lg-5 animate-fade-in" style="animation-delay: 0.3s">
                    <div class="hero-image-floating px-4">
                        <img src="assets/images/ai_ip17_pm.png" alt="iPhone 17 Pro" class="img-fluid drop-shadow-xl" onerror="this.src='https://placehold.co/1200x1200/f5f5f7/1d1d1f?text=iPhone+17+Pro'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Floating Styles for Hero -->
    <style>
        .hero-title { font-size: clamp(48px, 6vw, 76px); letter-spacing: -2px; }
        .hero-image-floating { animation: float 6s ease-in-out infinite; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1)); }
        @keyframes float { 0% { transform: translatey(0px); } 50% { transform: translatey(-20px); } 100% { transform: translatey(0px); } }
        .drop-shadow-xl { filter: drop-shadow(0 30px 45px rgba(0,0,0,0.15)); }
        .hover-lift { transition: transform 0.3s ease; }
        .hover-lift:hover { transform: translateY(-3px); }
        .leading-relaxed { line-height: 1.6; }
    </style>

    <!-- Featured Products Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title animate-reveal">Tuyệt phẩm công nghệ.</h2>
            
            <div class="product-grid">
                <?php foreach ($featuredProducts as $p): ?>
                <div class="product-card-modern animate-reveal">
                    <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="p-img-box">
                        <img src="assets/images/<?php echo e($p['image']); ?>" alt="<?php echo e($p['name']); ?>" onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Device'">
                    </a>
                    <div class="p-brand"><?php echo e($p['category']); ?></div>
                    <h3 class="p-title"><?php echo e($p['name']); ?></h3>
                    <div class="p-price"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
                    <div class="p-actions">
                        <a href="#" class="btn-p-buy btn-add-to-cart-ajax" data-product-id="<?php echo e($p['id']); ?>">Mua</a>
                        <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="btn-p-view">Chi tiết</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="product.php" class="btn-link-apple">Tất cả sản phẩm <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Support Section Modern -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 animate-reveal">
                    <h2 class="display-5 fw-bold mb-4 text-gradient">NHKMOBILE.<br>Đồng hành cùng bạn.</h2>
                    <p class="h5 text-secondary mb-5 fw-light leading-relaxed">Chúng tôi không chỉ bán thiết bị. Chúng tôi mang đến trải nghiệm hậu mãi chuẩn 5 sao với trung tâm bảo hành hiện đại nhất.</p>
                    
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-2 text-primary"><i class="bi bi-shield-check"></i></div>
                                <div>
                                    <div class="h5 fw-bold mb-0">1 Đổi 1</div>
                                    <p class="small text-secondary mb-0">Trong 30 ngày lỗi NSX</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-2 text-primary"><i class="bi bi-truck"></i></div>
                                <div>
                                    <div class="h5 fw-bold mb-0">Miễn Phí</div>
                                    <p class="small text-secondary mb-0">Giao hàng toàn quốc</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden shadow-lg">
                        <img src="https://images.unsplash.com/photo-1616348436168-de43ad0db179?auto=format&fit=crop&q=80&w=1000" class="img-fluid" alt="Store">
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
