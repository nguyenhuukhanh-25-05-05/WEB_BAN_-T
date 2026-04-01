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
             <img src="../assets/images/logo-k.svg" height="28" alt="Logo">
             <button class="btn btn-link text-dark d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
             </button>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin active"><i class="bi bi-grid-1x2-fill"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-phone"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-bag-check"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin"><i class="bi bi-people"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-check"></i> Bảo hành</a>
            <a href="news.php" class="nav-link-admin"><i class="bi bi-newspaper"></i> Tin tức</a>
            
            <div class="mt-auto pt-5 border-top" style="margin-top: 100px !important;">
                 <a href="../index.php" class="nav-link-admin text-primary"><i class="bi bi-arrow-left-circle"></i> Xem Website</a>
                 <a href="logout.php" class="nav-link-admin text-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        <header class="mb-5">
             <h1 class="fw-800 h2 mb-1">Bảng điều khiển</h1>
             <p class="text-secondary fw-500">Thống kê hoạt động hệ thống NHK Mobile</p>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value text-primary"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Đơn hàng mới</div>
                    <div class="stat-value"><?php echo $newOrdersCount; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Người dùng</div>
                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Sản phẩm</div>
                    <div class="stat-value"><?php echo $totalProducts; ?></div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-800 h5 mb-0">Đơn hàng gần đây</h3>
                <a href="orders.php" class="btn btn-link text-primary fw-bold text-decoration-none small">Xem tất cả</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover border-0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentOrders as $o): ?>
                        <tr>
                            <td class="fw-bold text-secondary">#<?php echo $o['id']; ?></td>
                            <td class="fw-bold"><?php echo $o['customer_name']; ?></td>
                            <td class="text-secondary"><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                            <td class="fw-bold"><?php echo number_format($o['total_price'], 0, ',', '.'); ?>₫</td>
                            <td>
                                <span class="badge-status badge-<?php echo strtolower($o['status']); ?>">
                                    <?php echo $o['status']; ?>
                                </span>
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
