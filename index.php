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

$pageTitle = "NHK Mobile | Apple Authorized Reseller";
$basePath = "";

include 'includes/header.php';
?>

<main>
    <!-- HERO CAROUSEL SECTION -->
    <section class="hero-carousel" id="heroCarousel">
        <!-- Slide 1: iPhone -->
        <div class="hero-slide active" data-slide="0">
            <div class="hero-slide-bg gradient-1"></div>
            <div class="container-wide">
                <div class="hero-grid">
                    <div class="hero-text">
                        <span class="hero-badge"><i class="bi bi-stars"></i>Thế hệ AI 2026</span>
                        <h1 class="display-hero">iPhone 17 Pro.<br><span class="text-gradient">Đỉnh cao trí tuệ.</span></h1>
                        <p class="hero-desc">Sức mạnh vượt bậc từ chip A19 Pro. Trải nghiệm hệ sinh thái AI toàn cầu ngay trên tay bạn với thiết kế titan siêu nhẹ và camera AI đột phá.</p>
                        <!-- Hero Search Box -->
                        <div class="hero-search-box">
                            <form action="product.php" method="GET">
                                <input type="text" name="q" class="hero-search-input" placeholder="Tìm kiếm iPhone, Samsung, Xiaomi...">
                                <button type="submit" class="hero-search-btn"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="product.php?category=Apple" class="btn-main btn-primary">Mua ngay</a>
                            <a href="product.php?category=Apple" class="btn-main btn-outline">Tìm hiểu thêm</a>
                        </div>
                    </div>
                    <div class="hero-image-container">
                        <img src="assets/images/ai_ip17_pm.png" alt="iPhone 17 Pro" class="hero-image-new"
                            onerror="this.src='https://placehold.co/800x1000/transparent/333?text=iPhone+17+Pro'">
                        <div class="hero-glow"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2: Samsung -->
        <div class="hero-slide" data-slide="1">
            <div class="hero-slide-bg gradient-2"></div>
            <div class="container-wide">
                <div class="hero-grid">
                    <div class="hero-text">
                        <span class="hero-badge" style="color: #1428a0;"><i class="bi bi-phone"></i>Galaxy AI</span>
                        <h1 class="display-hero">Galaxy S25 Ultra.<br><span style="background: linear-gradient(135deg, #1428a0, #3b5bdb); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Tương lai trong tay.</span></h1>
                        <p class="hero-desc">Trải nghiệm Galaxy AI đỉnh cao với S Pen, camera 200MP và màn hình Dynamic AMOLED 2X 120Hz.</p>
                        <div class="hero-search-box">
                            <form action="product.php" method="GET">
                                <input type="text" name="q" class="hero-search-input" placeholder="Tìm kiếm iPhone, Samsung, Xiaomi...">
                                <button type="submit" class="hero-search-btn"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="product.php?category=Samsung" class="btn-main btn-primary" style="background: linear-gradient(135deg, #1428a0, #3b5bdb);">Khám phá ngay</a>
                            <a href="product.php?category=Samsung" class="btn-main btn-outline">Xem tất cả</a>
                        </div>
                    </div>
                    <div class="hero-image-container">
                        <img src="assets/images/samsung_s25.png" alt="Galaxy S25 Ultra" class="hero-image-new"
                            onerror="this.src='https://placehold.co/800x1000/transparent/1428a0?text=Galaxy+S25+Ultra'">
                        <div class="hero-glow" style="background: #1428a0;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3: Xiaomi -->
        <div class="hero-slide" data-slide="2">
            <div class="hero-slide-bg gradient-3"></div>
            <div class="container-wide">
                <div class="hero-grid">
                    <div class="hero-text">
                        <span class="hero-badge" style="color: #ff6900;"><i class="bi bi-lightning-charge"></i>Flagship Killer</span>
                        <h1 class="display-hero">Xiaomi 15 Ultra.<br><span style="background: linear-gradient(135deg, #ff6900, #ff9f43); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sức mạnh vượt trội.</span></h1>
                        <p class="hero-desc">Camera Leica đỉnh cao, sạc nhanh 120W, chip Snapdragon 8 Elite. Hiệu năng mạnh mẽ, giá hợp lý.</p>
                        <div class="hero-search-box">
                            <form action="product.php" method="GET">
                                <input type="text" name="q" class="hero-search-input" placeholder="Tìm kiếm iPhone, Samsung, Xiaomi...">
                                <button type="submit" class="hero-search-btn"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="product.php?category=Xiaomi" class="btn-main btn-primary" style="background: linear-gradient(135deg, #ff6900, #ff9f43);">Mua ngay</a>
                            <a href="product.php?category=Xiaomi" class="btn-main btn-outline">So sánh</a>
                        </div>
                    </div>
                    <div class="hero-image-container">
                        <img src="assets/images/xiaomi_15.png" alt="Xiaomi 15 Ultra" class="hero-image-new"
                            onerror="this.src='https://placehold.co/800x1000/transparent/ff6900?text=Xiaomi+15+Ultra'">
                        <div class="hero-glow" style="background: #ff6900;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel Navigation -->
        <button class="carousel-arrow prev" onclick="changeSlide(-1)"><i class="bi bi-chevron-left"></i></button>
        <button class="carousel-arrow next" onclick="changeSlide(1)"><i class="bi bi-chevron-right"></i></button>

        <!-- Carousel Dots -->
        <div class="hero-carousel-nav">
            <button class="carousel-dot active" onclick="goToSlide(0)"></button>
            <button class="carousel-dot" onclick="goToSlide(1)"></button>
            <button class="carousel-dot" onclick="goToSlide(2)"></button>
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
                    <div class="category-icon apple"><i class="bi bi-apple"></i></div>
                    <span class="category-name">Apple</span>
                </a>
                <a href="product.php?category=Samsung" class="category-item">
                    <div class="category-icon samsung"><i class="bi bi-phone"></i></div>
                    <span class="category-name">Samsung</span>
                </a>
                <a href="product.php?category=Xiaomi" class="category-item">
                    <div class="category-icon xiaomi"><i class="bi bi-lightning-charge"></i></div>
                    <span class="category-name">Xiaomi</span>
                </a>
                <a href="product.php?category=OPPO" class="category-item">
                    <div class="category-icon oppo"><i class="bi bi-camera"></i></div>
                    <span class="category-name">OPPO</span>
                </a>
                <a href="product.php?category=Vivo" class="category-item">
                    <div class="category-icon vivo"><i class="bi bi-music-note-beamed"></i></div>
                    <span class="category-name">Vivo</span>
                </a>
                <a href="product.php?category=Realme" class="category-item">
                    <div class="category-icon realme"><i class="bi bi-bolt"></i></div>
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
                // Lấy sản phẩm giảm giá (flash sale)
                $flashSaleStmt = $pdo->query("SELECT * FROM products WHERE discount_price IS NOT NULL AND discount_price > 0 ORDER BY RANDOM() LIMIT 4");
                $flashSaleProducts = $flashSaleStmt->fetchAll();
                if (empty($flashSaleProducts)) {
                    // Nếu không có sản phẩm giảm giá, lấy sản phẩm ngẫu nhiên
                    $flashSaleStmt = $pdo->query("SELECT * FROM products ORDER BY RANDOM() LIMIT 4");
                    $flashSaleProducts = $flashSaleStmt->fetchAll();
                }
                foreach ($flashSaleProducts as $p):
                    $discountPercent = isset($p['discount_price']) && $p['discount_price'] > 0
                        ? round((1 - $p['discount_price'] / $p['price']) * 100)
                        : rand(10, 30);
                    $salePrice = isset($p['discount_price']) && $p['discount_price'] > 0
                        ? $p['discount_price']
                        : $p['price'] * (100 - $discountPercent) / 100;
                ?>
                    <div class="product-card-new" style="background: #fff; border: none;">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>">
                            <div class="product-img-box">
                                <span class="badge-hot" style="background: #ff3b30;">-<?php echo $discountPercent; ?>%</span>
                                <img src="assets/images/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>"
                                    onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                            </div>
                            <div class="product-info-new">
                                <span class="p-cat"><?php echo $p['category']; ?></span>
                                <h3 class="p-name"><?php echo $p['name']; ?></h3>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <span style="font-size: 18px; font-weight: 800; color: #ff3b30;"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                    <span style="font-size: 14px; color: #999; text-decoration: line-through;"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                </div>
                            </div>
                        </a>
                        <a href="cart.php?add=<?php echo $p['id']; ?>" class="add-to-cart-btn" style="opacity: 1; transform: none; background: #ff3b30;">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="product.php" class="btn-main btn-outline" style="background: #fff; color: #ff3b30; border-color: #fff;">Xem tất cả Flash Sale</a>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="features-new">
        <div class="container-wide">
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

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        dots[i].classList.remove('active');
    });
    slides[index].classList.add('active');
    dots[index].classList.add('active');
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
