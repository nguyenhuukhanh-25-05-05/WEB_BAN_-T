<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'NHK Mobile'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">

    <script>
        // Define global API paths for JS
        const BASE_PATH = "<?php echo $basePath; ?>";
        const SEARCH_API_URL = BASE_PATH + "api/search_suggestions.php";
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light fixed-top navbar-premium">
        <div class="container-fluid">
            <div class="navbar-centered-wrapper">
                <!-- 1. Logo (Leftmost of the center block) -->
                <a class="navbar-brand" href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" height="28" alt="Logo">
                </a>

                <!-- 2. Main Menu Links (Direct children for even spacing) -->
                <a class="nav-link d-none d-md-block" href="<?php echo $basePath; ?>product.php">Điện thoại</a>
                <a class="nav-link d-none d-md-block" href="<?php echo $basePath; ?>warranty.php">Bảo hành</a>
                <a class="nav-link d-none d-md-block" href="<?php echo $basePath; ?>news.php">Tin tức</a>

                <!-- 3. Icon Actions (Direct children for even spacing) -->
                <a href="#" id="searchTrigger" class="icon-link">
                    <i class="bi bi-search"></i>
                </a>
                
                <a href="<?php echo $basePath; ?>cart.php" class="icon-link position-relative">
                    <i class="bi bi-bag"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem;">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle no-caret p-0 m-0 border-0 shadow-none outline-none" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person fs-5"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end glass-card border-light shadow-lg rounded-4 p-2 mt-3">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><div class="dropdown-header text-dark small fw-bold">Chào, <?php echo $_SESSION['user_fullname']; ?></div></li>
                            <li><a class="dropdown-item rounded-3 small" href="#"><i class="bi bi-person-badge me-2"></i>Tài khoản</a></li>
                            <li><a class="dropdown-item rounded-3 small" href="<?php echo $basePath; ?>order_history.php"><i class="bi bi-clock-history me-2"></i>Lịch sử mua hàng</a></li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li><a class="dropdown-item rounded-3 small text-danger" href="<?php echo $basePath; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        <?php elseif (isset($_SESSION['admin_id'])): ?>
                            <li><div class="dropdown-header text-primary small fw-bold">Quản trị viên</div></li>
                            <li><a class="dropdown-item rounded-3 small" href="<?php echo $basePath; ?>admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển</a></li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li><a class="dropdown-item rounded-3 small text-danger" href="<?php echo $basePath; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item rounded-3 small" href="<?php echo $basePath; ?>login.php"><i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập</a></li>
                            <li><a class="dropdown-item rounded-3 small" href="<?php echo $basePath; ?>register.php"><i class="bi bi-person-plus me-2"></i>Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Hamburger Button (Only Mobile) -->
                <button class="navbar-toggler border-0 shadow-none d-md-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu Collapse (Only visible on mobile when toggled) -->
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
