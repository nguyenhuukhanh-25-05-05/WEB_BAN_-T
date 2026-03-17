<?php
session_start();
require_once 'includes/db.php';

// Fetch all news articles
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$articles = $stmt->fetchAll();

$pageTitle = "Tin tức Công nghệ | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<main>
    <!-- HERO SECTION: Premium Dark -->
    <section class="hero-premium position-relative overflow-hidden d-flex align-items-center" style="min-height: 50vh;">
        <div class="hero-bg-gradient"></div>
        <div class="container position-relative z-2 text-center text-lg-start animate-fade-in">
            <div class="glass-badge d-inline-block px-4 py-2 mb-4 rounded-pill">
                <span class="text-primary-gradient fw-bold">NHK Tech News 2026</span>
            </div>
            <h1 class="display-2 fw-800 mb-4 tracking-tight hero-title-main">
                Cập nhật xu hướng.<br>
                <span class="text-gradient">Dẫn đầu công nghệ.</span>
            </h1>
            <p class="h4 text-secondary mb-0 fw-light max-w-600">
                Khám phá những bước tiến mới nhất trong thế giới di động và trí tuệ nhân tạo.
            </p>
        </div>
    </section>

    <!-- CONTENT SECTION: Light Body -->
    <section class="py-huge bg-premium-light">
        <div class="container px-xl-5">
            <div class="d-flex justify-content-between align-items-end mb-5 animate-reveal">
                <div>
                    <h2 class="display-4 fw-bold text-dark mb-2">Tin mới nhất.</h2>
                    <p class="text-secondary h5 fw-light">Những câu chuyện công nghệ đáng chú ý hôm nay.</p>
                </div>
            </div>

            <div class="row g-4 pt-4">
                <?php if (empty($articles)): ?>
                    <div class="col-12 text-center py-5">
                         <div class="glass-card p-5 rounded-5 border-dashed">
                              <i class="bi bi-newspaper display-1 mb-4 opacity-10"></i>
                              <h3>Đang cập nhật tin tức mới...</h3>
                              <p>Vui lòng quay lại sau ít phút hoặc nạp lại CSDL.</p>
                         </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $index => $a): ?>
                    <div class="col-md-6 col-lg-4 animate-reveal" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <article class="card-glass-product h-100 p-0 overflow-hidden border-0 shadow-sm transition-all hover-lift">
                            <div class="position-relative">
                                <img src="assets/images/<?php echo $a['image']; ?>" class="card-img-top object-fit-cover" alt="<?php echo $a['title']; ?>" style="height: 240px;" onerror="this.src='https://placehold.co/600x400/f5f5f7/1d1d1f?text=Tech+News'">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo $a['category']; ?></span>
                                </div>
                            </div>
                            <div class="p-4 bg-white">
                                <div class="text-secondary small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i> <?php echo date('d/m/Y', strtotime($a['created_at'])); ?>
                                </div>
                                <h4 class="fw-bold text-dark mb-3 line-clamp-2"><?php echo $a['title']; ?></h4>
                                <p class="text-secondary small mb-4 line-clamp-3"><?php echo $a['excerpt']; ?></p>
                                <a href="#" class="btn btn-link text-primary p-0 text-decoration-none fw-bold">
                                    Đọc tiếp <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER: Glass Panel on White -->
    <section class="pb-huge bg-premium-light">
        <div class="container px-xl-5">
            <div class="glass-panel p-5 p-lg-8 rounded-5 text-center bg-dark text-white shadow-2xl overflow-hidden position-relative">
                <div class="position-relative z-2">
                    <h2 class="display-5 fw-bold mb-4">Đừng bỏ lỡ bất kỳ nhịp đập nào.</h2>
                    <p class="h5 text-secondary-light mb-5 fw-light">Đăng ký để nhận tin tức công nghệ mới nhất qua Email hàng tuần.</p>
                    <form class="newsletter-form-premium d-flex flex-column flex-md-row gap-3 max-w-500 mx-auto">
                        <input type="email" class="form-control form-control-lg rounded-pill px-4 bg-white bg-opacity-10 border-light text-white" placeholder="Địa chỉ email của bạn...">
                        <button type="button" class="btn btn-premium-light btn-lg px-5 rounded-pill shadow-lg">Đăng ký</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
