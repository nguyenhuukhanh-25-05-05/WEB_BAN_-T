<?php
// Bắt đầu phiên làm việc
session_start();

/**
 * KIỂM TRA QUYỀN TRUY CẬP
 * Nếu chưa đăng nhập Admin thì đẩy về trang login.php
 */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Nhúng file kết nối CSDL và xử lý các hàm chung
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

// 3. Đếm tổng số lượng máy đang có trong kho
$stmtProducts = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmtProducts->fetchColumn();

// 4. Lấy danh sách 6 đơn hàng vừa đặt mới nhất để hiện bảng
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

    <!-- SIDEBAR QUẢN TRỊ -->
    <aside class="sidebar text-white d-none d-lg-block">
        <div class="mb-5 px-3">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin active"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            
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
            <div class="col-md-4">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Tổng doanh thu</h6>
                    <h3 class="fw-bold mb-0 text-primary h2"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</h3>
                    <div class="text-success small mt-2"><i class="bi bi-graph-up"></i> Tăng 12% tháng này</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Đơn hàng mới</h6>
                    <h3 class="fw-bold mb-0 h2"><?php echo $newOrdersCount; ?></h3>
                    <div class="text-secondary small mt-2">Cần duyệt ngay</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="text-secondary small mb-2 text-uppercase fw-bold">Số lượng máy</h6>
                    <h3 class="fw-bold mb-0 h2"><?php echo $totalProducts; ?></h3>
                    <div class="text-secondary small mt-2">Sản phẩm hiện có</div>
                </div>
            </div>
        </div>

        <!-- BẢNG ĐƠN HÀNG GẦN ĐÂY -->
        <div class="stat-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Đơn đặt hàng gần đây</h5>
                <a href="orders.php" class="btn btn-sm btn-light border px-3 rounded-pill fw-bold">Xem tất cả</a>
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
</body>
</html>
