<?php
/**
 * NHK Mobile - Home Page
 * 
 * Description: The main landing page featuring the hero section, 
 * featured products grid, and core value propositions.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.4
 * Date: 2026-04-24
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
    <!-- HERO SECTION - CLEAN & PROFESSIONAL -->
    <section class="hero-new" id="heroCarousel">
        <div class="container-wide">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="hero-badge"><i class="bi bi-stars"></i>Siêu phẩm AI 2026</span>
                    <h1 class="display-hero">iPhone 17 Pro Max.<br><span class="text-gradient">Đẳng cấp vượt trội.</span></h1>
                    <p class="hero-desc">Trải nghiệm công nghệ AI đỉnh cao với chip A19 Pro, camera 48MP và thiết kế Titan sang trọng. Đặt hàng ngay để nhận ưu đãi đặc biệt!</p>
                    
                    <div class="d-flex gap-3 flex-wrap mt-4">
                        <a href="product.php" class="btn-main btn-primary"><i class="bi bi-bag"></i>Mua ngay</a>
                        <a href="product.php?category=Apple" class="btn-main btn-outline"><i class="bi bi-arrow-right"></i>Xem thêm</a>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="hero-quick-stats">
                        <div class="hero-quick-stat">
                            <div class="hero-quick-stat-value">48MP</div>
                            <div class="hero-quick-stat-label">Camera</div>
                        </div>
                        <div class="hero-quick-stat">
                            <div class="hero-quick-stat-value">A19 Pro</div>
                            <div class="hero-quick-stat-label">Chip</div>
                        </div>
                        <div class="hero-quick-stat">
                            <div class="hero-quick-stat-value">Titan</div>
                            <div class="hero-quick-stat-label">Chất liệu</div>
                        </div>
                    </div>
                </div>
                <div class="hero-image-container">
                    <img src="assets/images/apple-iphone-17-pro-max.png" alt="iPhone 17 Pro" class="hero-image-new"
                        onerror="this.src='https://placehold.co/800x1000/transparent/333?text=iPhone+17+Pro'">
                    <div class="hero-glow"></div>
                </div>
            </div>
        </div>
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
                // Lấy sản phẩm flash sale (ngẫu nhiên, giảm giá giả lập)
                $flashSaleStmt = $pdo->query("SELECT * FROM products ORDER BY RANDOM() LIMIT 4");
                $flashSaleProducts = $flashSaleStmt->fetchAll();
                foreach ($flashSaleProducts as $p):
                    $discountPercent = rand(10, 30);
                    $salePrice = $p['price'] * (100 - $discountPercent) / 100;
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
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <span style="font-size: 18px; font-weight: 800; color: #fff;"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                    <span style="font-size: 14px; color: rgba(255,255,255,0.7); text-decoration: line-through;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
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
            <div class="text-center mt-5">
                <a href="product.php" class="btn-main btn-outline" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.3);">Xem tất cả Flash Sale</a>
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
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <div class="p-price-new"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
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
// Hero Carousel
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.carousel-dot');
const totalSlides = slides.length;

// Chỉ chạy carousel nếu có slide trong DOM
if (totalSlides > 0) {
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) dots[i].classList.remove('active');
        });
        slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
    }

    function changeSlide(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    function goToSlide(index) {
        currentSlide = index;
        showSlide(currentSlide);
    }

    // Auto-play carousel
    setInterval(() => {
        changeSlide(1);
    }, 6000);
}

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
