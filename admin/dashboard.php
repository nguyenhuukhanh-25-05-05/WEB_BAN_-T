<?php
require_once 'admin_auth.php';
require_once '../includes/db.php';

/**
 * TRUY VẤN THỐNG KÊ (Dùng cho Dashboard)
 */

// 1. Tính tổng doanh thu từ các đơn hàng đã 'Completed' (Hoàn thành)
$stmtRevenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'Completed'");
$totalRevenue = $stmtRevenue->fetchColumn() ?: 0;

// 2. Đếm số đơn hàng mới đang chờ xử lý (Pending)
$stmtOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'");
$newOrdersCount = $stmtOrders->fetchColumn();

// 3. Lấy tổng số Khách hàng (Users)
$stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmtUsers->fetchColumn();

// 4. Đếm tổng số lượng máy đang có trong kho
$stmtProducts = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmtProducts->fetchColumn();

// 5. Lấy danh sách 6 đơn hàng vừa đặt mới nhất để hiện bảng
$stmtRecent = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6");
$recentOrders = $stmtRecent->fetchAll();

// Cấu hình trang
$pageTitle = "Tổng quan Dashboard | Admin";
$basePath = "../";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <!-- MOBILE HEADER -->
    <div class="mobile-header d-lg-none">
        <button class="btn btn-light border-0 me-3" id="sidebarToggle">
            <i class="bi bi-list fs-3"></i>
        </button>
        <img src="../assets/images/logo-k.svg" height="15" alt="Logo">
    </div>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR QUẢN TRỊ -->
    <aside class="sidebar text-white" id="sidebarMenu">
        <div class="mb-5 px-3 d-flex justify-content-between align-items-center">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
             <button class="btn btn-link text-white d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
             </button>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin active"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin"><i class="bi bi-people"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-check"></i> Bảo hành IMEI</a>
            <a href="news.php" class="nav-link-admin"><i class="bi bi-newspaper"></i> Tin tức & Tech</a>
            
            <div class="mt-5 pt-5 border-top border-secondary mx-3">
                 <a href="../index.php" class="nav-link-admin text-info ps-0 mb-2"><i class="bi bi-box-arrow-left"></i> Xem Website</a>
                 <a href="logout.php" class="nav-link-admin text-danger ps-0 small"><i class="bi bi-power"></i> Đăng xuất</a>
            </div>
        </nav>
    </aside>

    <!-- TRANG CHÍNH -->
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Chào mừng quay lại!</h2>
                 <p class="text-secondary small mb-0">Hệ thống đang hoạt động ổn định.</p>
            </div>
        </header>

        <!-- KHỐI THỐNG KÊ NHANH -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white position-relative overflow-hidden">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Tổng doanh thu</h6>
                    <h3 class="fw-bold mb-0 text-primary h2"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</h3>
                    <div class="text-success small mt-2"><i class="bi bi-graph-up"></i> Tăng 12% tháng này</div>
                    <!-- Mock Sparkline -->
                    <div class="position-absolute bottom-0 start-0 end-0" style="height: 40px; opacity: 0.1;">
                        <svg viewBox="0 0 100 20" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                            <path d="M0 20 L0 15 L10 12 L20 16 L30 10 L40 14 L50 8 L60 12 L70 5 L80 10 L90 3 L100 8 L100 20 Z" fill="var(--accent-color)"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Đơn hàng mới</h6>
                    <h3 class="fw-bold mb-0 h2"><?php echo $newOrdersCount; ?></h3>
                    <div class="text-secondary small mt-2">Cần duyệt ngay</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Khách hàng</h6>
                    <h3 class="fw-bold mb-0 h2"><?php echo $totalUsers; ?></h3>
                    <div class="text-secondary small mt-2">Thành viên hiện có</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white border-start border-4 border-success">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Trạng thái hệ thống</h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                        <h3 class="fw-bold mb-0 h4 text-success">Ổn định</h3>
                    </div>
                    <div class="text-secondary small mt-2">Uptime: 99.9%</div>
                </div>
            </div>
        </div>

        <!-- BẢNG ĐƠN HÀNG GẦN ĐÂY -->
        <div class="stat-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Đơn đặt hàng gần đây</h5>
                <div>
                    <a href="export_stats.php" class="btn btn-sm btn-success border px-3 rounded-pill fw-bold shadow-sm me-2"><i class="bi bi-file-earmark-excel me-1"></i> Xuất CSV</a>
                    <a href="orders.php" class="btn btn-sm btn-light border px-3 rounded-pill fw-bold">Xem tất cả</a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Mã đơn</th>
                            <th>Tên khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng thanh toán</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp PHP Đơn hàng -->
                        <?php foreach($recentOrders as $o): ?>
                        <tr>
                            <td class="small fw-bold text-secondary">#ORD-<?php echo $o['id']; ?></td>
                            <td><div class="fw-bold"><?php echo $o['customer_name']; ?></div></td>
                            <td class="small text-secondary"><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                            <td class="fw-bold text-dark"><?php echo number_format($o['total_price'], 0, ',', '.'); ?>₫</td>
                            <td>
                                <!-- Hiển thị màu sắc tùy theo trạng thái hiệu chỉnh bằng PHP -->
                                <span class="badge bg-<?php echo $o['status'] == 'Completed' ? 'success' : ($o['status'] == 'Cancelled' ? 'danger' : 'warning'); ?>-subtle text-dark border fw-normal px-3 py-2 rounded-pill">
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
        // Sidebar Toggle for Mobile
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function toggleSidebar() {
            sidebarMenu.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.classList.toggle('overflow-hidden');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
        sidebarClose.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
