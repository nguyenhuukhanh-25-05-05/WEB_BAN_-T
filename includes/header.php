<?php
// Đảm bảo các hàm cốt lõi luôn khả dụng trên mọi trang sử dụng Header
require_once dirname(__FILE__) . '/auth_functions.php';
require_once dirname(__FILE__) . '/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'NHK Mobile'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="<?php echo get_csrf_token(); ?>">

    <script>
        // Define global API paths for JS
        const BASE_PATH = "<?php echo $basePath; ?>";
        const SEARCH_API_URL = BASE_PATH + "api/search_suggestions.php";
    </script>
</head>
<body>
    <nav class="navbar-modern">
        <div class="nav-container">
            <!-- Mobile Menu Toggle -->
            <button class="nav-toggle d-lg-none" id="navToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
            </button>

            <!-- Logo -->
            <a class="nav-brand" href="<?php echo $basePath; ?>index.php">
                <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" alt="Logo">
            </a>

            <!-- Navigation Links (Desktop) -->
            <ul class="nav-menu d-none d-lg-flex">
                <li><a href="<?php echo $basePath; ?>product.php">Điện thoại</a></li>
                <li><a href="<?php echo $basePath; ?>product.php?category=Apple">Apple</a></li>
                <li><a href="<?php echo $basePath; ?>product.php?category=Samsung">Samsung</a></li>
                <li><a href="<?php echo $basePath; ?>warranty.php">Bảo hành</a></li>
                <li><a href="<?php echo $basePath; ?>news.php">Tin tức</a></li>
            </ul>

            <!-- Navigation Icons -->
            <div class="nav-icons">
                <a href="#" class="icon-link search-trigger" title="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </a>
                <a href="<?php echo $basePath; ?>cart.php" class="icon-link position-relative" title="Giỏ hàng">
                    <i class="bi bi-bag"></i>
                    <?php 
                        $cartCount = 0;
                        if(isset($_SESSION['cart'])) {
                            foreach($_SESSION['cart'] as $item) $cartCount += $item['qty'];
                        }
                    ?>
                    <span id="cart-count" class="cart-badge <?php echo $cartCount > 0 ? '' : 'd-none'; ?>">
                        <?php echo $cartCount; ?>
                    </span>
                </a>
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                    <a href="#accountOffcanvas" class="icon-link" data-bs-toggle="offcanvas" title="Tài khoản">
                        <i class="bi bi-person"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>login.php" class="icon-link" title="Đăng nhập">
                        <i class="bi bi-person"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Menu (Overlay) -->
        <div class="nav-mobile-overlay" id="navMobileOverlay">
            <div class="nav-mobile-menu">
                <ul class="nav-mobile-links">
                    <li><a href="<?php echo $basePath; ?>product.php">Tất cả điện thoại</a></li>
                    <li><a href="<?php echo $basePath; ?>product.php?category=Apple">iPhone / Apple</a></li>
                    <li><a href="<?php echo $basePath; ?>product.php?category=Samsung">Samsung Galaxy</a></li>
                    <li><a href="<?php echo $basePath; ?>warranty.php">Tra cứu bảo hành</a></li>
                    <li><a href="<?php echo $basePath; ?>news.php">Tin tức công nghệ</a></li>
                    <li class="divider"></li>
                    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                        <li><a href="<?php echo $basePath; ?>order_history.php">Đơn hàng của tôi</a></li>
                        <li><a href="<?php echo $basePath; ?>logout.php" class="text-danger">Đăng xuất</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $basePath; ?>login.php">Đăng nhập / Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Account Offcanvas Menu -->
    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="accountOffcanvas" aria-labelledby="accountOffcanvasLabel" style="width: 350px;">
        <div class="offcanvas-header bg-light border-bottom">
            <h5 class="offcanvas-title fw-bold" id="accountOffcanvasLabel"><i class="bi bi-layout-sidebar-reverse me-2"></i>Tài khoản</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <div class="p-4 bg-premium-light text-center border-bottom">
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-person"></i>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'Khách hàng'); ?></h5>
                    <p class="text-secondary small mb-0">Thành viên NHK Mobile</p>
                <?php else: ?>
                    <h5 class="fw-bold text-primary mb-1">Quản trị viên</h5>
                    <p class="text-secondary small mb-0">Hệ thống QTV</p>
                <?php endif; ?>
            </div>
            
            <div class="list-group list-group-flush mt-2 flex-grow-1">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3"><i class="bi bi-person-badge fs-5 text-dark"></i></div>
                        <span class="fw-medium text-dark">Thông tin cá nhân</span>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <a href="<?php echo $basePath; ?>order_history.php" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3"><i class="bi bi-clock-history fs-5 text-dark"></i></div>
                        <span class="fw-medium text-dark">Lịch sử mua hàng</span>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>admin/dashboard.php" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <div class="bg-primary-subtle rounded p-2 me-3"><i class="bi bi-speedometer2 fs-5 text-primary"></i></div>
                        <span class="fw-medium text-primary">Bảng điều khiển Admin</span>
                        <i class="bi bi-chevron-right ms-auto text-primary small"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="offcanvas-footer p-4 border-top bg-light">
            <a href="<?php echo $basePath; ?>logout.php" class="btn btn-danger w-100 rounded-pill py-3 fw-bold shadow-sm d-flex justify-content-center align-items-center">
                <i class="bi bi-box-arrow-right me-2 fs-5"></i> Đăng xuất
            </a>
        </div>
    </div>
    <?php endif; ?>
