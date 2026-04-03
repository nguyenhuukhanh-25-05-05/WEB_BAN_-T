<?php
session_start();
require_once 'includes/db.php';

// 1. Lấy danh sách 8 sản phẩm tiêu biểu
$stmt = $pdo->query("SELECT * FROM products ORDER BY is_featured DESC, created_at DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll();

$pageTitle = "NHK Mobile | Apple Authorized Reseller";
$basePath = "";

include 'includes/header.php';
?>

<main>
    <!-- NEW HERO SECTION -->
    <section class="hero-new">
        <div class="container-wide">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="hero-badge">Thế hệ AI 2026</span>
                    <h1 class="display-large">iPhone 17 Pro.<br><span class="text-gradient">Đỉnh cao trí tuệ.</span></h1>
                    <p class="hero-desc">Sức mạnh vượt bậc từ chip A19 Pro. Trải nghiệm hệ sinh thái AI toàn cầu ngay trên tay bạn với thiết kế titan siêu nhẹ và camera AI đột phá.</p>
                    <div class="d-flex gap-3">
                        <a href="product.php" class="btn-main btn-primary">Mua ngay</a>
                        <a href="product.php?category=Apple" class="btn-main btn-outline">Tìm hiểu thêm</a>
                    </div>
                </div>
                <div class="hero-image-container">
                    <img src="assets/images/ai_ip17_pm.png" alt="iPhone 17" class="hero-image-new"
                        onerror="this.src='https://placehold.co/800x1000/transparent/333?text=iPhone+17+Pro'">
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="products-section">
        <div class="container-wide">
            <div class="section-title-box">
                <span class="section-subtitle">Sản phẩm nổi bật</span>
                <h2 class="display-5 fw-bold">Đỉnh phẩm công nghệ mới.</h2>
            </div>

            <div class="product-grid-new">
                <?php foreach ($featuredProducts as $p): ?>
                    <div class="product-card-new">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                            <div class="product-img-box">
                                <?php if($p['is_featured']): ?>
                                    <span class="badge-hot">Hot Deal</span>
                                <?php endif; ?>
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <div class="p-price-new"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
                            </div>
                        </a>
                        <a href="cart.php?add=<?php echo $p['id']; ?>" class="add-to-cart-btn">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="product.php" class="btn-main btn-outline">Xem tất cả sản phẩm</a>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="features-new">
        <div class="container-wide">
            <div class="section-title-box">
                <span class="section-subtitle">Tại sao chọn NHK Mobile?</span>
                <h2 class="display-5 fw-bold">Trải nghiệm mua sắm chuẩn 5 sao.</h2>
            </div>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h3>Bảo hành chính hãng</h3>
                    <p class="text-muted">Cam kết 100% sản phẩm chính hãng, bảo hành 1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-truck"></i></div>
                    <h3>Giao hàng siêu tốc</h3>
                    <p class="text-muted">Miễn phí giao hàng toàn quốc. Nhận hàng trong vòng 2h tại các thành phố lớn.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-headset"></i></div>
                    <h3>Hỗ trợ 24/7</h3>
                    <p class="text-muted">Đội ngũ kỹ thuật viên chuyên nghiệp luôn sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
