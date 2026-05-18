<?php
/**
 * NHK Mobile - Home Page
 * 
 * Description: The main landing page featuring the hero section, 
 * featured products grid, and core value propositions.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.3
 * Date: 2026-04-08
 */
require_once 'includes/auth_functions.php';
require_once 'includes/db.php';

/** @var PDO $pdo */

// 1. Fetch featured products (limit 8)
$stmt = $pdo->query("SELECT * FROM products ORDER BY is_featured DESC, created_at DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll();

// 2. Fetch "Dành cho bạn" - 8 sản phẩm khác, không trùng với featured
$featuredIds = array_column($featuredProducts, 'id');
$excludeIds = implode(',', array_map('intval', $featuredIds ?: [0]));
$forYouStmt = $pdo->query("SELECT * FROM products WHERE id NOT IN ($excludeIds) ORDER BY RANDOM() LIMIT 8");
$forYouProducts = $forYouStmt->fetchAll();

$pageTitle = "NHK Mobile | Apple Authorized Reseller";
$basePath = "";

include 'includes/header.php';
?>

<main>
    <!-- HERO CAROUSEL - Multi-product advertisement slider -->
    <section class="hero-carousel-section" id="heroCarousel" aria-label="Quảng cáo sản phẩm nổi bật">

        <!-- Progress Bar -->
        <div class="hero-progress-bar"><div class="hero-progress-fill" id="heroProgress"></div></div>

        <!-- ── SLIDES ── -->
        <div class="hero-slides-wrapper" id="heroSlidesWrapper">

            <!-- Slide 1: iPhone 17 Pro Max -->
            <div class="hero-slide active" data-theme="blue">
                <div class="hero-slide-bg" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8efff 40%, #dde8ff 100%);"></div>
                <div class="container-wide hero-slide-content">
                    <div class="hero-grid">
                        <div class="hero-text">
                            <span class="hero-badge"><i class="bi bi-stars"></i>Siêu phẩm AI 2026</span>
                            <h1 class="display-hero">iPhone 17 Pro Max.<br><span class="text-gradient-blue">Đẳng cấp vượt trội.</span></h1>
                            <p class="hero-desc">Chip A19 Pro, camera 48MP Titan, màn hình ProMotion 120Hz. Trải nghiệm iPhone đỉnh cao nhất từ trước đến nay.</p>
                            <div class="d-flex gap-3 flex-wrap mt-4">
                                <a href="product.php?category=Apple" class="btn-main btn-primary"><i class="bi bi-bag"></i>Mua ngay</a>
                                <a href="product.php?category=Apple" class="btn-main btn-outline"><i class="bi bi-arrow-right"></i>Xem thêm</a>
                            </div>
                            <div class="hero-quick-stats">
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value">48MP</div><div class="hero-quick-stat-label">Camera</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value">A19 Pro</div><div class="hero-quick-stat-label">Chip</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value">Titan</div><div class="hero-quick-stat-label">Khung máy</div></div>
                            </div>
                        </div>
                        <div class="hero-image-container">
                            <img src="assets/images/apple-iphone-17-pro-max.png" alt="iPhone 17 Pro Max" class="hero-image-new"
                                onerror="this.src='https://placehold.co/500x600/dde8ff/007AFF?text=iPhone+17'">
                            <div class="hero-glow" style="background: radial-gradient(circle, rgba(0,122,255,0.35) 0%, transparent 70%);"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Samsung Galaxy S25 Ultra -->
            <div class="hero-slide" data-theme="dark">
                <div class="hero-slide-bg" style="background: linear-gradient(135deg, #0d1117 0%, #1a1428 40%, #0f1a2e 100%);"></div>
                <div class="container-wide hero-slide-content">
                    <div class="hero-grid">
                        <div class="hero-text">
                            <span class="hero-badge" style="background: rgba(100,220,255,0.15); color: #64dcff; border-color: rgba(100,220,255,0.25);"><i class="bi bi-lightning-charge-fill"></i>Galaxy AI</span>
                            <h1 class="display-hero" style="color: #fff;">Samsung Galaxy S25 Ultra.<br><span class="text-gradient-cyan">Tương lai trong tay bạn.</span></h1>
                            <p class="hero-desc" style="color: rgba(255,255,255,0.7);">Galaxy AI tích hợp trực tiếp, bút S Pen thế hệ mới, camera 200MP và pin 5000mAh siêu bền bỉ.</p>
                            <div class="d-flex gap-3 flex-wrap mt-4">
                                <a href="product.php?category=Samsung" class="btn-main" style="background:#64dcff; color:#000;"><i class="bi bi-bag"></i>Mua ngay</a>
                                <a href="product.php?category=Samsung" class="btn-main btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.3);"><i class="bi bi-arrow-right"></i>Xem thêm</a>
                            </div>
                            <div class="hero-quick-stats" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1);">
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#64dcff;">200MP</div><div class="hero-quick-stat-label" style="color:rgba(255,255,255,0.5);">Camera</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#64dcff;">Snapdragon</div><div class="hero-quick-stat-label" style="color:rgba(255,255,255,0.5);">Chip AI</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#64dcff;">5000mAh</div><div class="hero-quick-stat-label" style="color:rgba(255,255,255,0.5);">Pin</div></div>
                            </div>
                        </div>
                        <div class="hero-image-container">
                            <img src="assets/images/samsung-galaxy-s25-ultra.png" alt="Samsung Galaxy S25 Ultra" class="hero-image-new"
                                onerror="this.src='https://placehold.co/500x600/1a1428/64dcff?text=S25+Ultra'">
                            <div class="hero-glow" style="background: radial-gradient(circle, rgba(100,220,255,0.3) 0%, transparent 70%);"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Xiaomi 17 Ultra -->
            <div class="hero-slide" data-theme="orange">
                <div class="hero-slide-bg" style="background: linear-gradient(135deg, #fff8f0 0%, #fff0e0 40%, #ffe5c8 100%);"></div>
                <div class="container-wide hero-slide-content">
                    <div class="hero-grid">
                        <div class="hero-text">
                            <span class="hero-badge" style="background: rgba(255,102,0,0.1); color: #ff6600; border-color: rgba(255,102,0,0.2);"><i class="bi bi-camera-fill"></i>Leica Camera</span>
                            <h1 class="display-hero">Xiaomi 17 Ultra.<br><span class="text-gradient-orange">Nhiếp ảnh đỉnh cao.</span></h1>
                            <p class="hero-desc" style="color: #6b4e2a;">Hợp tác cùng Leica, cảm biến 1 inch khổng lồ, zoom quang học 10x, sạc không dây 90W siêu nhanh.</p>
                            <div class="d-flex gap-3 flex-wrap mt-4">
                                <a href="product.php?category=Xiaomi" class="btn-main" style="background:#ff6600; color:#fff;"><i class="bi bi-bag"></i>Mua ngay</a>
                                <a href="product.php?category=Xiaomi" class="btn-main btn-outline" style="color:#ff6600; border-color: rgba(255,102,0,0.3);"><i class="bi bi-arrow-right"></i>Xem thêm</a>
                            </div>
                            <div class="hero-quick-stats" style="background: rgba(255,255,255,0.7);">
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#ff6600;">1″</div><div class="hero-quick-stat-label">Cảm biến</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#ff6600;">10x</div><div class="hero-quick-stat-label">Zoom quang học</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#ff6600;">90W</div><div class="hero-quick-stat-label">Sạc không dây</div></div>
                            </div>
                        </div>
                        <div class="hero-image-container">
                            <img src="assets/images/xiaomi-17-ultra.png" alt="Xiaomi 17 Ultra" class="hero-image-new"
                                onerror="this.src='https://placehold.co/500x600/ffe5c8/ff6600?text=Xiaomi+17'">
                            <div class="hero-glow" style="background: radial-gradient(circle, rgba(255,102,0,0.3) 0%, transparent 70%);"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: OPPO Find X10 -->
            <div class="hero-slide" data-theme="green">
                <div class="hero-slide-bg" style="background: linear-gradient(135deg, #f0fff8 0%, #e0f8ef 40%, #c8f0e3 100%);"></div>
                <div class="container-wide hero-slide-content">
                    <div class="hero-grid">
                        <div class="hero-text">
                            <span class="hero-badge" style="background: rgba(0,180,100,0.1); color: #00b464; border-color: rgba(0,180,100,0.2);"><i class="bi bi-battery-full"></i>Pin siêu bền</span>
                            <h1 class="display-hero">OPPO Find X10.<br><span class="text-gradient-green">Mỏng nhất. Mạnh nhất.</span></h1>
                            <p class="hero-desc" style="color: #1a4a30;">Thiết kế siêu mỏng 6.7mm, màn hình LTPO AMOLED 2K, sạc nhanh 100W - đầy pin trong 25 phút.</p>
                            <div class="d-flex gap-3 flex-wrap mt-4">
                                <a href="product.php?category=Oppo" class="btn-main" style="background:#00b464; color:#fff;"><i class="bi bi-bag"></i>Mua ngay</a>
                                <a href="product.php?category=Oppo" class="btn-main btn-outline" style="color:#00b464; border-color: rgba(0,180,100,0.3);"><i class="bi bi-arrow-right"></i>Xem thêm</a>
                            </div>
                            <div class="hero-quick-stats" style="background: rgba(255,255,255,0.7);">
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#00b464;">6.7mm</div><div class="hero-quick-stat-label">Siêu mỏng</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#00b464;">2K</div><div class="hero-quick-stat-label">Màn hình</div></div>
                                <div class="hero-quick-stat"><div class="hero-quick-stat-value" style="color:#00b464;">100W</div><div class="hero-quick-stat-label">Sạc siêu nhanh</div></div>
                            </div>
                        </div>
                        <div class="hero-image-container">
                            <img src="assets/images/oppo-find-x10.png" alt="OPPO Find X10" class="hero-image-new"
                                onerror="this.src='https://placehold.co/500x600/c8f0e3/00b464?text=OPPO+Find+X10'">
                            <div class="hero-glow" style="background: radial-gradient(circle, rgba(0,180,100,0.3) 0%, transparent 70%);"></div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Navigation Arrows (inside wrapper for stable positioning) -->
        <button class="hero-arrow hero-arrow-prev" id="heroPrev" aria-label="Slide trước">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-arrow hero-arrow-next" id="heroNext" aria-label="Slide tiếp theo">
            <i class="bi bi-chevron-right"></i>
        </button>

        <!-- Dots Navigation -->
        <div class="hero-dots" id="heroDots">
            <button class="hero-dot active" data-index="0" aria-label="Slide 1"></button>
            <button class="hero-dot" data-index="1" aria-label="Slide 2"></button>
            <button class="hero-dot" data-index="2" aria-label="Slide 3"></button>
            <button class="hero-dot" data-index="3" aria-label="Slide 4"></button>
        </div>

    </div><!-- end slides-wrapper -->

    </section>

    <!-- CATEGORY SECTION -->
    <section class="category-section">
        <div class="container-wide">
            <div class="section-title-box reveal">
                <span class="section-subtitle">Danh mục</span>
                <h2 class="display-5 fw-bold">Khám phá theo thương hiệu</h2>
            </div>
            <div class="category-grid reveal-stagger">
                <a href="product.php?category=Apple" class="category-item">
                    <div class="category-icon apple"><img src="assets/images/apple-iphone-17-pro-max.png" alt="Apple" onerror="this.parentElement.innerHTML='<i class=\'bi bi-apple\'></i>'"></div>
                    <span class="category-name">Apple</span>
                </a>
                <a href="product.php?category=Samsung" class="category-item">
                    <div class="category-icon samsung"><img src="assets/images/samsung-galaxy-s25-ultra.png" alt="Samsung" onerror="this.parentElement.innerHTML='<i class=\'bi bi-phone\'></i>'"></div>
                    <span class="category-name">Samsung</span>
                </a>
                <a href="product.php?category=Xiaomi" class="category-item">
                    <div class="category-icon xiaomi"><img src="assets/images/xiaomi-17-ultra.png" alt="Xiaomi" onerror="this.parentElement.innerHTML='<i class=\'bi bi-lightning-charge\'></i>'"></div>
                    <span class="category-name">Xiaomi</span>
                </a>
                <a href="product.php?category=OPPO" class="category-item">
                    <div class="category-icon oppo"><img src="assets/images/oppo-find-x10.png" alt="OPPO" onerror="this.parentElement.innerHTML='<i class=\'bi bi-camera\'></i>'"></div>
                    <span class="category-name">OPPO</span>
                </a>
                <a href="product.php?category=Vivo" class="category-item">
                    <div class="category-icon vivo"><img src="assets/images/vivo-x300.png" alt="Vivo" onerror="this.parentElement.innerHTML='<i class=\'bi bi-music-note-beamed\'></i>'"></div>
                    <span class="category-name">Vivo</span>
                </a>
                <a href="product.php?category=Realme" class="category-item">
                    <div class="category-icon realme"><img src="assets/images/realme-gt9.png" alt="Realme" onerror="this.parentElement.innerHTML='<i class=\'bi bi-bolt\'></i>'"></div>
                    <span class="category-name">Realme</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="products-section">
        <div class="container-wide">
            <div class="section-title-box reveal">
                <span class="section-subtitle">Sản phẩm nổi bật</span>
                <h2 class="display-5 fw-bold">Đỉnh phẩm công nghệ mới.</h2>
            </div>

            <div class="product-grid-new reveal-stagger">
                <?php foreach ($featuredProducts as $p): ?>
                    <div class="product-card-new">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                            <div class="product-img-box">
                                <?php if (!empty($p['discount']) && $p['discount'] > 0): ?>
                                    <span class="badge-hot" style="background: #007AFF;">-<?php echo $p['discount']; ?>%</span>
                                <?php elseif($p['is_featured']): ?>
                                    <span class="badge-hot">Hot Deal</span>
                                <?php endif; ?>
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <?php if (!empty($p['discount']) && $p['discount'] > 0): 
                                    $salePrice = $p['price'] - ($p['price'] * $p['discount'] / 100);
                                ?>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span style="font-size: 16px; font-weight: 800; color: #000;"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                        <span style="font-size: 13px; color: #888; text-decoration: line-through;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                    </div>
                                <?php else: ?>
                                    <div class="p-price-new"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
                                <?php endif; ?>
                                <?php if(!empty($p['specs'])): ?>
                                <div class="p-specs">
                                    <?php
                                    $specsArr = array_map('trim', explode(',', $p['specs']));
                                    foreach(array_slice($specsArr, 0, 2) as $spec): ?>
                                    <span><?php echo htmlspecialchars($spec); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
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

    <!-- FLASH SALE SECTION -->
    <section class="flash-sale-section">
        <div class="container-wide">
            <div class="flash-sale-header reveal">
                <div class="flash-sale-title">
                    <i class="bi bi-lightning-charge-fill flash-icon"></i>
                    <h2>Flash Sale</h2>
                </div>
                <div class="countdown-timer">
                    <div class="countdown-item">
                        <div class="countdown-number" id="hours">02</div>
                        <div class="countdown-label">Giờ</div>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <div class="countdown-number" id="minutes">45</div>
                        <div class="countdown-label">Phút</div>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <div class="countdown-number" id="seconds">30</div>
                        <div class="countdown-label">Giây</div>
                    </div>
                </div>
            </div>
            <div class="product-grid-new">
                <?php
                // Lấy sản phẩm flash sale (Ưu tiên sản phẩm có discount thật)
                $flashSaleStmt = $pdo->query("SELECT * FROM products WHERE discount > 0 ORDER BY created_at DESC LIMIT 4");
                $flashSaleProducts = $flashSaleStmt->fetchAll();
                
                // Bổ sung nếu chưa đủ 4 máy (lấy cố định theo ID, không dùng ngẫu nhiên để tránh tải lại trang bị đổi)
                if (count($flashSaleProducts) < 4) {
                    $limit = 4 - count($flashSaleProducts);
                    $excludeIds = implode(',', array_map('intval', array_column($flashSaleProducts, 'id') ?: [0]));
                    $moreStmt = $pdo->query("SELECT * FROM products WHERE id NOT IN ($excludeIds) ORDER BY id ASC LIMIT $limit");
                    $flashSaleProducts = array_merge($flashSaleProducts, $moreStmt->fetchAll());
                }

                foreach ($flashSaleProducts as $p):
                    // Lấy % giảm thật từ admin, nếu không có thì giả lập cố định một mức (ví dụ 12%) chứ không dùng ngẫu nhiên nữa
                    $discountPercent = !empty($p['discount']) ? $p['discount'] : 12;
                    $salePrice = $p['price'] - ($p['price'] * $discountPercent / 100);
                ?>
                    <div class="product-card-new" style="background: #fff; border: none;">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                            <div class="product-img-box">
                                <span class="badge-hot" style="background: #007AFF;">-<?php echo $discountPercent; ?>%</span>
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="font-size: 16px; font-weight: 800; color: #000;"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                    <span style="font-size: 13px; color: #888; text-decoration: line-through;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                </div>
                                <?php if(!empty($p['specs'])): ?>
                                <div class="p-specs">
                                    <?php
                                    $specsArr = array_map('trim', explode(',', $p['specs']));
                                    foreach(array_slice($specsArr, 0, 2) as $spec): ?>
                                    <span><?php echo htmlspecialchars($spec); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <a href="cart.php?add=<?php echo $p['id']; ?>" class="add-to-cart-btn" style="opacity: 1; transform: none; background: #007AFF;">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- FOR YOU SECTION -->
    <section class="products-section" style="background: var(--bg-soft);">
        <div class="container-wide">
            <div class="section-title-box reveal">
                <span class="section-subtitle">Gợi ý cho bạn</span>
                <h2 class="display-5 fw-bold">Dành cho bạn.</h2>
            </div>
            <div class="product-grid-new reveal-stagger">
                <?php foreach ($forYouProducts as $p): ?>
                    <div class="product-card-new">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                            <div class="product-img-box">
                                <?php if (!empty($p['discount']) && $p['discount'] > 0): ?>
                                    <span class="badge-hot" style="background: #007AFF;">-<?php echo $p['discount']; ?>%</span>
                                <?php endif; ?>
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <?php if (!empty($p['discount']) && $p['discount'] > 0): 
                                    $salePrice = $p['price'] - ($p['price'] * $p['discount'] / 100);
                                ?>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span style="font-size: 16px; font-weight: 800; color: #000;"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                        <span style="font-size: 13px; color: #888; text-decoration: line-through;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                    </div>
                                <?php else: ?>
                                    <div class="p-price-new"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
                                <?php endif; ?>
                                <?php if(!empty($p['specs'])): ?>
                                <div class="p-specs">
                                    <?php
                                    $specsArr = array_map('trim', explode(',', $p['specs']));
                                    foreach(array_slice($specsArr, 0, 2) as $spec): ?>
                                    <span><?php echo htmlspecialchars($spec); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
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
    <section class="features-new">        <div class="container-wide">
            <div class="section-title-box reveal">
                <span class="section-subtitle">Tại sao chọn NHK Mobile?</span>
                <h2 class="display-5 fw-bold">Trải nghiệm mua sắm chuẩn 5 sao.</h2>
            </div>

            <div class="feature-grid reveal-stagger">
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

    <!-- TESTIMONIAL SECTION -->
    <section class="testimonial-section">
        <div class="container-wide">
            <div class="section-title-box reveal">
                <span class="section-subtitle">Đánh giá</span>
                <h2 class="display-5 fw-bold">Khách hàng nói gì về chúng tôi?</h2>
            </div>
            <div class="testimonial-grid reveal-stagger">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">"Mua iPhone 17 Pro tại NHK Mobile, sản phẩm chính hãng, giao hàng siêu nhanh. Nhân viên tư vấn nhiệt tình, sẽ quay lại mua tiếp!"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">NT</div>
                        <div class="testimonial-info">
                            <h4>Nguyễn Thanh Tùng</h4>
                            <p>Khách hàng tại Hà Nội</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">"Giá tốt nhất thị trường, chế độ bảo hành rõ ràng. Mình đã mua 3 chiếc điện thoại ở đây và rất hài lòng với chất lượng dịch vụ."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="background: linear-gradient(135deg, #ff6900, #ff9f43);">LH</div>
                        <div class="testimonial-info">
                            <h4>Lê Hoàng</h4>
                            <p>Khách hàng tại TP.HCM</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <p class="testimonial-text">"Website dễ sử dụng, thanh toán nhanh gọn. Flash sale giá cực hợp lý, tiết kiệm được gần 2 triệu so với mua ở chỗ khác."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="background: linear-gradient(135deg, #1428a0, #3b5bdb);">PM</div>
                        <div class="testimonial-info">
                            <h4>Phạm Minh</h4>
                            <p>Khách hàng tại Đà Nẵng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER SECTION -->
    <section class="newsletter-section">
        <div class="container-wide">
            <div class="newsletter-content reveal-scale">
                <h2>Đăng ký nhận tin</h2>
                <p>Nhận thông tin về sản phẩm mới, khuyến mãi đặc biệt và ưu đãi dành riêng cho thành viên.</p>
                <form class="newsletter-form" onsubmit="handleNewsletter(event)">
                    <input type="email" class="newsletter-input" placeholder="Nhập email của bạn" required>
                    <button type="submit" class="newsletter-btn">Đăng ký</button>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- Carousel & Countdown Scripts -->
<script>
// ── Hero Carousel Controller ────────────────────────────────────
(function () {
    const SLIDE_DURATION = 5500; // ms per slide
    const slides   = document.querySelectorAll('.hero-slide');
    const dots     = document.querySelectorAll('.hero-dot');
    const prevBtn  = document.getElementById('heroPrev');
    const nextBtn  = document.getElementById('heroNext');
    const progress = document.getElementById('heroProgress');
    const total    = slides.length;

    if (total === 0) return;

    let current   = 0;
    let autoTimer = null;
    let progTimer = null;

    /* ── Show a slide ── */
    function goTo(index, dir = 1) {
        if (index === current) return;
        const prev = current;
        current = (index + total) % total;

        // Slide out previous
        slides[prev].classList.add(dir > 0 ? 'slide-out-left' : 'slide-out-right');
        slides[prev].classList.remove('active');

        // Slide in current
        slides[current].classList.add(dir > 0 ? 'slide-in-right' : 'slide-in-left');
        slides[current].classList.add('active');

        // Clean up classes after animation
        setTimeout(() => {
            slides[prev].classList.remove('slide-out-left', 'slide-out-right');
            slides[current].classList.remove('slide-in-right', 'slide-in-left');
        }, 700);

        // Dots
        dots.forEach((d, i) => d.classList.toggle('active', i === current));

        resetProgress();
    }

    function next() { goTo(current + 1, 1); }
    function prev() { goTo(current - 1, -1); }

    /* ── Progress bar ── */
    function resetProgress() {
        clearTimeout(progTimer);
        if (progress) {
            progress.style.transition = 'none';
            progress.style.width = '0%';
            // Force reflow
            progress.offsetWidth;
            progress.style.transition = `width ${SLIDE_DURATION}ms linear`;
            progress.style.width = '100%';
        }
    }

    /* ── Auto-play ── */
    function startAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(next, SLIDE_DURATION);
        resetProgress();
    }

    function stopAuto() {
        clearInterval(autoTimer);
        if (progress) {
            const w = getComputedStyle(progress).width;
            progress.style.transition = 'none';
            progress.style.width = w;
        }
    }

    /* ── Arrow buttons ── */
    if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAuto(); });

    /* ── Dots ── */
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const idx = parseInt(dot.dataset.index, 10);
            goTo(idx, idx > current ? 1 : -1);
            startAuto();
        });
    });

    /* ── Pause on hover ── */
    const section = document.getElementById('heroCarousel');
    if (section) {
        section.addEventListener('mouseenter', stopAuto);
        section.addEventListener('mouseleave', startAuto);
    }

    /* ── Swipe / touch support ── */
    let touchStartX = 0;
    if (section) {
        section.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        section.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) { dx < 0 ? next() : prev(); startAuto(); }
        }, { passive: true });
    }

    /* ── Init ── */
    startAuto();
})();

// Countdown Timer
function updateCountdown() {
    const now = new Date();
    const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    const diff = endOfDay - now;

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
}

setInterval(updateCountdown, 1000);
updateCountdown();

// Newsletter Handler
function handleNewsletter(e) {
    e.preventDefault();
    const email = e.target.querySelector('input[type="email"]').value;
    // Gửi API đăng ký
    fetch('api/subscribe.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Cảm ơn bạn đã đăng ký! Chúng tôi sẽ gửi thông tin khuyến mãi đến email của bạn.');
            e.target.reset();
        } else {
            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    })
    .catch(() => {
        alert('Cảm ơn bạn đã đăng ký!');
        e.target.reset();
    });
}
</script>

<?php include 'includes/footer.php'; ?>
