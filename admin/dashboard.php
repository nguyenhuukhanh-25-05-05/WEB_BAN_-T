<?php
require_once 'admin_auth.php';
require_once '../includes/db.php';

$stmtRevenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'Completed' OR status = 'Hoàn thành'");
$totalRevenue = $stmtRevenue->fetchColumn() ?: 0;

$stmtOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' OR status = 'Chờ duyệt'");
$newOrdersCount = $stmtOrders->fetchColumn();

$stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmtUsers->fetchColumn();

$stmtProducts = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmtProducts->fetchColumn();

$stmtRecent = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6");
$recentOrders = $stmtRecent->fetchAll();

$pageTitle = "Dashboard | Admin NHK Mobile";
$basePath = "../";
include 'includes/admin_header.php';
?>

        <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
             <div>
                <h1 class="fw-800 h2 mb-1">Tổng quan hệ thống</h1>
                <p class="text-secondary fw-500 mb-0">Chào mừng trở lại, Admin NHK Mobile</p>
             </div>
             <div class="d-flex gap-2">
                 <a href="export_stats.php" class="btn btn-white border rounded-3 fw-700 px-4 py-2 small shadow-sm bg-white d-flex align-items-center">
                     <i class="bi bi-download me-2"></i>Báo cáo
                 </a>
                 <div class="dropdown">
                     <button class="btn btn-primary rounded-3 fw-700 px-4 py-2 small shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                         <i class="bi bi-plus-lg me-2"></i>Thêm mới
                     </button>
                     <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2">
                         <li><a class="dropdown-item py-2 px-4 fw-600" href="products.php"><i class="bi bi-phone me-2 text-primary"></i> Sản phẩm mới</a></li>
                         <li><a class="dropdown-item py-2 px-4 fw-600" href="news.php"><i class="bi bi-newspaper me-2 text-success"></i> Bài viết mới</a></li>
                         <li><hr class="dropdown-divider opacity-50"></li>
                         <li><a class="dropdown-item py-2 px-4 fw-600" href="warranties.php"><i class="bi bi-shield-check me-2 text-info"></i> Kích hoạt bảo hành</a></li>
                         <li><a class="dropdown-item py-2 px-4 fw-600" href="password_resets.php"><i class="bi bi-key me-2 text-warning"></i> Đặt lại mật khẩu</a></li>
                     </ul>
                 </div>
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
                                <?php 
                                    $badgeClass = 'bg-warning text-dark';
                                    $s = mb_strtolower($o['status'], 'UTF-8');
                                    if (str_contains($s, 'đã duyệt')) $badgeClass = 'bg-info text-white';
                                    elseif (str_contains($s, 'đang giao')) $badgeClass = 'bg-primary text-white';
                                    elseif (str_contains($s, 'hoàn thành')) $badgeClass = 'bg-success text-white';
                                    elseif (str_contains($s, 'hủy')) $badgeClass = 'bg-danger text-white';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> border-0 px-3 py-1 rounded-pill small">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="orders.php" class="btn btn-light btn-sm rounded-3 px-3"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php include 'includes/admin_footer.php'; ?>
