<?php
/**
 * NHK Mobile - Administrative Site Header
 * 
 * Description: Orchestrates the sidebar navigation, mobile offcanvas 
 * drawer, and global admin asset loading. Ensures consistent UX 
 * across all management modules.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.1
 * Date: 2026-04-08
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/admin.css">
</head>
<body>

    <!-- MOBILE HEADER -->
    <div class="mobile-header d-lg-none">
        <button class="btn btn-link text-dark p-0 me-3" id="sidebarToggle">
            <i class="bi bi-list fs-1 shadow-sm"></i>
        </button>
        <div class="brand-logo-box sm me-2">NHK</div>
        <span class="brand-text sm">ADMIN</span>
    </div>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebarMenu">
        <div class="d-flex align-items-center justify-content-between mb-5">
             <div class="d-flex align-items-center">
                <div class="brand-logo-box sm me-2">NHK</div>
                <span class="brand-text sm">ADMIN</span>
             </div>
             <button class="btn btn-link text-dark d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
             </button>
        </div>
        <nav class="flex-grow-1">
            <a href="dashboard.php" class="nav-link-admin <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin <?php echo $current_page == 'products.php' ? 'active' : ''; ?>"><i class="bi bi-phone-vibrate"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>"><i class="bi bi-cart-check"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin <?php echo $current_page == 'users.php' ? 'active' : ''; ?>"><i class="bi bi-people-fill"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin <?php echo $current_page == 'warranties.php' ? 'active' : ''; ?>"><i class="bi bi-shield-lock"></i> Bảo hành</a>
            <a href="news.php" class="nav-link-admin <?php echo $current_page == 'news.php' ? 'active' : ''; ?>"><i class="bi bi-journal-text"></i> Tin tức</a>
        </nav>
        
        <div class="mt-4 pt-4 border-top flex-shrink-0">
             <a href="<?php echo $basePath; ?>index.php" class="nav-link-admin text-primary mb-2"><i class="bi bi-globe-americas"></i> Xem Website</a>
             <a href="logout.php" class="nav-link-admin text-danger"><i class="bi bi-power"></i> Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
