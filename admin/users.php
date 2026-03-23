<?php
// Bắt đầu phiên làm việc
require_once 'admin_auth.php';

// Nhúng file kết nối CSDL Postgres
require_once '../includes/db.php';

/**
 * 1. XỬ LÝ CẬP NHẬT TRẠNG THÁI USER (Ban / Unban)
 */
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Câu lệnh SQL cập nhật trạng thái
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    // Lưu thông báo vào URL và reload trang
    header("Location: users.php?msg=" . urlencode("Đã cập nhật trạng thái người dùng thành công!"));
    exit;
}

/**
 * 2. TRUY VẤN DANH SÁCH USER
 */
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Cấu hình Header
$pageTitle = "Quản lý Người dùng | Admin";
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
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin active"><i class="bi bi-people"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-check"></i> Bảo hành IMEI</a>
            <a href="news.php" class="nav-link-admin"><i class="bi bi-newspaper"></i> Tin tức & Tech</a>
            
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
                 <h2 class="fw-bold mb-1">Quản lý Khách hàng</h2>
                 <p class="text-secondary small mb-0">Xem danh sách đăng ký và khóa tài khoản vi phạm.</p>
            </div>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <!-- Hiển thị thông báo khi cập nhật thành công -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-primary alert-dismissible fade show mb-4 border-0 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th>Ngày tham gia</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp PHP Duyệt danh sách User -->
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td class="text-secondary fw-bold small">#USR-<?php echo $u['id']; ?></td>
                            <td>
                                 <div class="fw-bold"><?php echo htmlspecialchars($u['fullname']); ?></div>
                                 <div class="small text-secondary" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($u['address']); ?>">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($u['address']); ?>
                                 </div>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($u['email']); ?></div>
                                <div class="small"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($u['phone']); ?></div>
                            </td>
                            <td class="small text-secondary"><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                            <td>
                                <?php if ($u['status'] === 'active' || empty($u['status'])): ?>
                                    <span class="badge bg-success-subtle text-success border fw-normal px-2 rounded-pill">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border fw-normal px-2 rounded-pill">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form action="users.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    
                                    <?php if ($u['status'] === 'active' || empty($u['status'])): ?>
                                        <button type="submit" name="update_status" value="1" class="btn btn-sm btn-outline-danger shadow-sm" title="Khóa tài khoản này">
                                            <i class="bi bi-lock"></i> Khóa
                                            <input type="hidden" name="status" value="banned">
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="update_status" value="1" class="btn btn-sm btn-outline-success shadow-sm" title="Mở khóa tài khoản">
                                            <i class="bi bi-unlock"></i> Mở khóa
                                            <input type="hidden" name="status" value="active">
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($users) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Chưa có khách hàng nào đăng ký.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
