<?php
// Bắt đầu phiên làm việc
session_start();

/**
 * KIỂM TRA ĐĂNG NHẬP ADMIN
 */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Nhúng file kết nối CSDL Postgres
require_once '../includes/db.php';

/**
 * 1. XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
 * Sử dụng Form với phương thức POST để thay đổi status (Completed / Cancelled)
 */
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Câu lệnh SQL cập nhật trạng thái
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    // Lưu thông báo vào URL và reload trang
    header("Location: orders.php?msg=updated");
    exit;
}

/**
 * 2. TRUY VẤN DANH SÁCH ĐƠN HÀNG
 */
// Lấy toàn bộ đơn hàng, ưu tiên những đơn mới nhất xếp trên (DESC)
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

// Cấu hình Header
$pageTitle = "Quản lý đơn hàng | Admin";
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

    <!-- THANH SIDEBAR TRÁI -->
    <aside class="sidebar text-white d-none d-lg-block">
        <div class="mb-5 px-3">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin active"><i class="bi bi-receipt"></i> Đơn hàng</a>
            
            <div class="mt-5 pt-5 border-top border-secondary mx-3">
                 <a href="../index.php" class="nav-link-admin text-info ps-0 mb-2"><i class="bi bi-box-arrow-left"></i> Xem Website</a>
                 <a href="logout.php" class="nav-link-admin text-danger ps-0 small"><i class="bi bi-power"></i> Đăng xuất</a>
            </div>
        </nav>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Duyệt Đơn Hàng</h2>
                 <p class="text-secondary small mb-0">Quản lý và cập nhật trạng thái giao dịch khách hàng.</p>
            </div>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <!-- Hiển thị thông báo khi cập nhật thành công -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-primary alert-dismissible fade show mb-4 border-0 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> Trạng thái đơn hàng đã được cập nhật!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Tiếp nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp PHP Duyệt danh sách đơn hàng -->
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td class="text-secondary fw-bold small">#ORD-<?php echo $o['id']; ?></td>
                            <td>
                                 <div class="fw-bold"><?php echo $o['customer_name']; ?></div>
                                 <div class="small text-secondary"><i class="bi bi-phone"></i> <?php echo $o['customer_phone']; ?></div>
                            </td>
                            <td class="fw-bold text-primary"><?php echo number_format($o['total_price'], 0, ',', '.'); ?>₫</td>
                            <td><span class="badge bg-light text-dark border fw-normal px-2"><?php echo $o['payment_method']; ?></span></td>
                            <td>
                                <!-- Màu sắc hiển thị badge theo trạng thái -->
                                <span class="badge bg-<?php echo $o['status'] == 'Completed' ? 'success' : ($o['status'] == 'Cancelled' ? 'danger' : 'warning'); ?>-subtle text-dark border fw-normal px-3 py-2 rounded-pill">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <!-- FORM PHP: Gửi trạng thái mới lên server -->
                                <form action="orders.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $o['id']; ?>">
                                    <!-- Nút Hoàn thành -->
                                    <button type="submit" name="update_status" value="1" class="btn btn-sm btn-light border p-2 text-success shadow-sm" title="Hoàn thành đơn">
                                        <i class="bi bi-check-circle"></i>
                                        <input type="hidden" name="status" value="Completed">
                                    </button>
                                    <!-- Nút Hủy đơn -->
                                    <button type="submit" name="update_status" value="1" class="btn btn-sm btn-light border p-2 text-danger shadow-sm ms-1" title="Hủy đơn hàng">
                                        <i class="bi bi-x-circle"></i>
                                        <input type="hidden" name="status" value="Cancelled">
                                    </button>
                                </form>
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
