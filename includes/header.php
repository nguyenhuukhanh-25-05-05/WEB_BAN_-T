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

    <!-- Global Visual Hard-Fix V2.3 -->
    <style>
        .navbar-premium {
            background: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            height: 48px !important;
        }
        .navbar-centered-wrapper {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            max-width: 1024px;
            margin: 0 auto;
            width: 100%;
        }
        .navbar-left, .navbar-right { flex: 1; display: flex; align-items: center; }
        .navbar-right { justify-content: flex-end; }
        .navbar-center { 
            flex: 2; 
            display: flex !important; 
            justify-content: center !important; 
            gap: 20px; 
        }
        .nav-link, .icon-link {
            color: #1d1d1f !important;
            opacity: 0.8 !important;
            font-size: 13px !important;
            text-decoration: none !important;
        }
        @media (max-width: 768px) {
            .navbar-center { display: none !important; }
            .navbar-left, .navbar-brand { flex: 1; display: flex; justify-content: flex-start; }
            .navbar-right { flex: 1; justify-content: flex-end; }
        }
    </style>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="<?php echo get_csrf_token(); ?>">

    <script>
        // Define global API paths for JS
        const BASE_PATH = "<?php echo $basePath; ?>";
        const SEARCH_API_URL = BASE_PATH + "api/search_suggestions.php";
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-md fixed-top navbar-premium">
        <div class="navbar-centered-wrapper">
            <div class="navbar-left">
                <!-- 1. Hamburger Button (Dành cho Mobile) -->
                <button class="navbar-toggler border-0 shadow-none d-md-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- 2. Logo -->
                <a class="navbar-brand me-0" href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" height="28" alt="Logo">
                </a>
                </a>
                
                <!-- 5. Giỏ hàng (Luôn hiện) -->
                <a href="<?php echo $basePath; ?>cart.php" class="icon-link position-relative">
                    <i class="bi bi-bag"></i>
                    <?php 
                        $cartCount = 0;
                        if(isset($_SESSION['cart'])) {
                            foreach($_SESSION['cart'] as $item) $cartCount += $item['qty'];
                        }
                    ?>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm <?php echo $cartCount > 0 ? '' : 'd-none'; ?>" style="font-size: 0.6rem;">
                        <?php echo $cartCount; ?>
                    </span>
                </a>

                <!-- 6. Tài khoản (Ẩn trên di động, dùng menu Offcanvas trên PC) -->
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                    <a href="#accountOffcanvas" role="button" class="icon-link text-dark d-none d-md-flex" data-bs-toggle="offcanvas" aria-controls="accountOffcanvas">
                        <i class="bi bi-person fs-5"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>login.php" class="icon-link text-dark d-none d-md-flex">
                        <i class="bi bi-person fs-5"></i>
                    </a>
                <?php endif; ?>
        </div>
    </nav>
        <div class="collapse navbar-collapse d-md-none" id="navbarNav">
            <div class="bg-white border-bottom w-100 shadow-sm">
                <div class="container py-3">
                    <ul class="navbar-nav gap-2">
                        <li class="nav-item">
                            <a class="nav-link py-3 fs-6 fw-bold border-bottom d-flex align-items-center justify-content-between" href="<?php echo $basePath; ?>product.php">
                                <span><i class="bi bi-phone me-2"></i> Điện thoại</span>
                                <i class="bi bi-chevron-right small opacity-50"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 fs-6 fw-bold border-bottom d-flex align-items-center justify-content-between" href="<?php echo $basePath; ?>warranty.php">
                                <span><i class="bi bi-shield-check me-2"></i> Bảo hành</span>
                                <i class="bi bi-chevron-right small opacity-50"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 fs-6 fw-bold d-flex align-items-center justify-content-between" href="<?php echo $basePath; ?>news.php">
                                <span><i class="bi bi-newspaper me-2"></i> Tin tức</span>
                                <i class="bi bi-chevron-right small opacity-50"></i>
                            </a>
                        </li>
                    </ul>
                </div>
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
