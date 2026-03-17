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
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light fixed-top navbar-premium">
        <div class="container justify-content-center">
            <div class="navbar-unified-container">
                <a class="navbar-brand py-0 m-0" href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" alt="NHK Mobile">
                </a>

                <div class="d-none d-md-flex navbar-nav-centered">
                    <a class="nav-link" href="<?php echo $basePath; ?>product.php">Điện thoại</a>
                    <a class="nav-link" href="<?php echo $basePath; ?>warranty.php">Bảo hành</a>
                    <a class="nav-link" href="<?php echo $basePath; ?>news.php">Tin tức</a>
                </div>

                <div class="navbar-icons-group gap-3">
                    <form action="<?php echo $basePath; ?>product.php" method="GET" class="d-none d-lg-flex position-relative">
                        <input type="text" name="q" placeholder="Tìm tên máy..." class="form-control form-control-sm rounded-pill border-0 bg-light ps-3 pe-5" style="width: 180px;">
                        <button type="submit" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary p-0 me-3">
                            <i class="bi bi-search py-1"></i>
                        </button>
                    </form>
                    <a href="<?php echo $basePath; ?>cart.php" class="position-relative">
                        <i class="bi bi-bag"></i>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem;">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle no-caret" data-bs-toggle="dropdown">
                            <i class="bi bi-person fs-5"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-card border-light shadow-lg rounded-4 p-2 mt-3">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li><div class="dropdown-header text-dark small fw-bold">Chào, <?php echo $_SESSION['user_fullname']; ?></div></li>
                                <li><a class="dropdown-item rounded-3 small" href="#"><i class="bi bi-person-badge me-2"></i>Tài khoản</a></li>
                                <li><a class="dropdown-item rounded-3 small" href="#"><i class="bi bi-clock-history me-2"></i>Lịch sử mua hàng</a></li>
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
                </div>
            </div>
            
            <button class="navbar-toggler border-0 ms-2 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="width: 18px;"></span>
            </button>
        </div>
    </nav>
