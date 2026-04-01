<?php
require_once 'admin_auth.php';
require_once '../includes/db.php';

$stmtRevenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'Completed'");
$totalRevenue = $stmtRevenue->fetchColumn() ?: 0;

$stmtOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'");
$newOrdersCount = $stmtOrders->fetchColumn();

$stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmtUsers->fetchColumn();

$stmtProducts = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmtProducts->fetchColumn();

$stmtRecent = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6");
$recentOrders = $stmtRecent->fetchAll();

$pageTitle = "Dashboard | Admin NHK Mobile";
$basePath = "../";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <div class="mobile-header d-lg-none">
        <button class="btn btn-link text-dark p-0 me-3" id="sidebarToggle">
            <i class="bi bi-list fs-2"></i>
        </button>
        <img src="../assets/images/logo-k.svg" height="24" alt="Logo">
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebarMenu">
        <div class="d-flex align-items-center justify-content-between mb-5">
             <div class="d-flex align-items-center">
                <img src="../assets/images/logo-k.svg" height="28" alt="Logo" class="me-2">
                <span class="fw-800 fs-5 tracking-tight">NHK ADMIN</span>
             </div>
             <button class="btn btn-link text-dark d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
             </button>
        </div>
        <nav class="flex-grow-1">
            <a href="dashboard.php" class="nav-link-admin active"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-phone-vibrate"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-cart-check"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin"><i class="bi bi-people-fill"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-lock"></i> Bảo hành</a>
            <a href="news.php" class="nav-link-admin"><i class="bi bi-journal-text"></i> Tin tức</a>
        </nav>
        
        <div class="mt-auto pt-4 border-top">
             <a href="../index.php" class="nav-link-admin text-primary mb-2"><i class="bi bi-globe-americas"></i> Xem Website</a>
             <a href="logout.php" class="nav-link-admin text-danger"><i class="bi bi-power"></i> Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
             <div>
                <h1 class="fw-800 h2 mb-1">Tổng quan hệ thống</h1>
                <p class="text-secondary fw-500 mb-0">Chào mừng trở lại, Admin NHK Mobile</p>
             </div>
             <div class="d-flex gap-2">
                 <button class="btn btn-white border rounded-3 fw-700 px-4 py-2 small shadow-sm bg-white"><i class="bi bi-download me-2"></i>Báo cáo</button>
                 <button class="btn btn-primary rounded-3 fw-700 px-4 py-2 small shadow-sm"><i class="bi bi-plus-lg me-2"></i>Thêm mới</button>
             </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-light text-primary"><i class="bi bi-currency-dollar"></i></div>
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-label">Đơn chờ duyệt</div>
                    <div class="stat-value"><?php echo $newOrdersCount; ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-people"></i></div>
                    <div class="stat-label">Khách hàng</div>
                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-box-seam"></i></div>
                    <div class="stat-label">Sản phẩm</div>
                    <div class="stat-value"><?php echo $totalProducts; ?></div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-800 h5 mb-0">Đơn hàng gần đây</h3>
                <a href="orders.php" class="text-primary fw-800 text-decoration-none small">Xem tất cả <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentOrders as $o): ?>
                        <tr>
                            <td class="fw-bold text-muted small">#ORD-<?php echo $o['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo $o['customer_name']; ?></div>
                                <div class="text-muted small" style="font-size: 11px;">Khách hàng thân thiết</div>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                            <td class="fw-800 text-primary"><?php echo number_format($o['total_price'], 0, ',', '.'); ?>₫</td>
                            <td>
                                <span class="badge-status badge-<?php echo strtolower($o['status']); ?>">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-light btn-sm rounded-3 px-3"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function toggleSidebar() {
            sidebarMenu.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
        sidebarClose.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
