<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'NHK Mobile'; ?></title>
    
    <!-- Bootstrap 5 (Chỉ dùng Grid và một số Utility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">

    <script>
        const BASE_PATH = "<?php echo $basePath; ?>";
        const SEARCH_API_URL = BASE_PATH + "api/search_suggestions.php";
    </script>
</head>
<body>
    <nav class="navbar-minimal">
        <div class="container-wide nav-content">
            <a href="<?php echo $basePath; ?>index.php" class="nav-brand d-flex align-items-center">
                <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" height="32" alt="Logo" class="me-2">
                <span class="fw-800 fs-4 tracking-tight text-main d-none d-sm-block">NHK MOBILE</span>
            </a>

            <ul class="nav-links mb-0">
                <li><a href="<?php echo $basePath; ?>product.php" class="nav-link">Điện thoại</a></li>
                <li><a href="<?php echo $basePath; ?>warranty.php" class="nav-link">Bảo hành</a></li>
                <li><a href="<?php echo $basePath; ?>news.php" class="nav-link">Tin tức</a></li>
            </ul>

            <div class="nav-actions">
                <a href="#" id="searchTrigger" class="nav-icon"><i class="bi bi-search"></i></a>
                <a href="<?php echo $basePath; ?>cart.php" class="nav-icon position-relative">
                    <i class="bi bi-bag-heart"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.65rem; padding: 0.35em 0.5em;">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                    <a href="#accountOffcanvas" role="button" class="nav-icon" data-bs-toggle="offcanvas"><i class="bi bi-person-circle"></i></a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>login.php" class="nav-icon"><i class="bi bi-person"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Account Offcanvas (Vẫn giữ logic nhưng đổi style nhẹ) -->
    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="accountOffcanvas" style="width: 350px; border-left: 1px solid var(--border-light);">
        <div class="offcanvas-header border-bottom py-4">
            <h5 class="offcanvas-title fw-bold">Tài khoản</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="p-4 text-center border-bottom bg-light">
                <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem; border: 1px solid var(--border-light);">
                    <i class="bi bi-person text-primary"></i>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'Khách hàng'); ?></h6>
                    <p class="text-muted small mb-0">Thành viên NHK Mobile</p>
                <?php else: ?>
                    <h6 class="fw-bold text-primary mb-1">Quản trị viên</h6>
                <?php endif; ?>
            </div>
            
            <div class="list-group list-group-flush">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <i class="bi bi-person-badge me-3"></i> <span>Thông tin cá nhân</span>
                    </a>
                    <a href="<?php echo $basePath; ?>order_history.php" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <i class="bi bi-clock-history me-3"></i> <span>Lịch sử mua hàng</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>admin/dashboard.php" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center">
                        <i class="bi bi-speedometer2 me-3"></i> <span>Bảng điều khiển Admin</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="p-4 border-top">
            <a href="<?php echo $basePath; ?>logout.php" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold">Đăng xuất</a>
        </div>
    </div>
    <?php endif; ?>
