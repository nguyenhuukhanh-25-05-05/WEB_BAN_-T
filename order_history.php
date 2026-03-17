<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

// Cần đăng nhập để xem lịch sử
require_login();

$userId = get_logged_in_user_id();

// Lấy danh sách đơn hàng của user này
$stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmtOrders->execute([$userId]);
$orders = $stmtOrders->fetchAll();

$pageTitle = "Lịch sử mua hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<style>
.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 50rem;
    font-size: 0.85rem;
    font-weight: 500;
}
.order-card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05);
    transition: transform 0.2s;
}
.order-card:hover {
    transform: translateY(-3px);
}
</style>

<main class="bg-premium-light min-vh-100 pb-5" style="padding-top: 100px;">
    <div class="container px-xl-5">
        <div class="row">
            <!-- Sidebar / Cột Account Menu -->
            <div class="col-lg-3 mb-4">
                <div class="card order-card h-100">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></h4>
                        <p class="text-secondary small mb-4">Thành viên NHK Mobile</p>
                        <div class="list-group list-group-flush text-start border-0">
                            <a href="#" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-1"><i class="bi bi-person-badge me-2"></i> Thông tin tài khoản</a>
                            <a href="order_history.php" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-1 active custom-bg-primary text-white"><i class="bi bi-clock-history me-2"></i> Lịch sử đơn hàng</a>
                            <a href="logout.php" class="list-group-item list-group-item-action border-0 py-3 rounded-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <h3 class="fw-bold mb-4">Đơn hàng của bạn</h3>
                
                <?php if (empty($orders)): ?>
                    <div class="card order-card text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-bag-x display-1 text-muted mb-3 opacity-25"></i>
                            <h5 class="fw-bold">Bạn chưa có đơn hàng nào</h5>
                            <p class="text-secondary">Hãy khám phá các sản phẩm siêu hot của chúng tôi.</p>
                            <a href="product.php" class="btn btn-dark rounded-pill px-4 py-2 mt-3">Tiếp tục mua sắm</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($orders as $order): ?>
                            <?php
                                // Chuyển đổi trạng thái sang nhãn đẹp mắt
                                $statusClass = 'bg-secondary text-white';
                                $statusValue = $order['status'];
                                $statusText = htmlspecialchars($statusValue);
                                
                                // Dùng mảng map để linh hoạt hơn (hỗ trợ cả tiếng Việt hoa/thường)
                                $statusLower = mb_strtolower($statusValue, 'UTF-8');
                                
                                switch ($statusLower) {
                                    case 'chờ duyệt':
                                    case 'pending':
                                        $statusClass = 'bg-warning text-dark';
                                        $statusText = '⏳ Chờ duyệt';
                                        break;
                                    case 'đã duyệt':
                                    case 'approved':
                                        $statusClass = 'bg-info text-white';
                                        $statusText = '📦 Đang lấy hàng';
                                        break;
                                    case 'đang giao':
                                    case 'shipping':
                                        $statusClass = 'bg-primary text-white';
                                        $statusText = '🚚 Đang giao';
                                        break;
                                    case 'hoàn thành':
                                    case 'completed':
                                        $statusClass = 'bg-success text-white';
                                        $statusText = '✅ Thành công';
                                        break;
                                    case 'hủy':
                                    case 'đã hủy':
                                    case 'cancelled':
                                        $statusClass = 'bg-danger text-white';
                                        $statusText = '❌ Đã hủy';
                                        break;
                                }

                                // Lấy chi tiết món hàng của đơn này
                                $stmtItems = $pdo->prepare("SELECT order_items.*, products.image FROM order_items LEFT JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                                $stmtItems->execute([$order['id']]);
                                $items = $stmtItems->fetchAll();
                            ?>
                            <div class="col-12">
                                <div class="card order-card overflow-hidden">
                                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-bold">Đơn hàng #<?php echo $order['id']; ?></h6>
                                            <small class="text-secondary"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                                        </div>
                                        <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </div>
                                    <div class="card-body px-4">
                                        <div class="row border-bottom pb-3 mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Người nhận:</small>
                                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong> 
                                                <span class="text-secondary">(<?php echo htmlspecialchars($order['customer_phone']); ?>)</span>
                                            </div>
                                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                                <small class="text-muted d-block">Phương thức:</small>
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($order['payment_method'] ?? 'Thanh toán trực tiếp'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Danh sách sản phẩm -->
                                        <div class="order-items-list mb-3">
                                            <?php foreach ($items as $item): ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="assets/images/<?php echo $item['image']; ?>" class="rounded-3 border me-3" style="width: 50px; height: 50px; object-fit: contain; padding: 2px;" onerror="this.src='https://placehold.co/100x100?text=SP'">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fs-6"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                    <small class="text-secondary">x<?php echo $item['quantity']; ?></small>
                                                </div>
                                                <div class="text-end fw-bold text-danger">
                                                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mt-3">
                                            <span class="text-secondary fw-medium">Tổng thanh toán:</span>
                                            <h5 class="fw-bold text-danger mb-0"><?php echo number_format($order['total_price'], 0, ',', '.'); ?>đ</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<style>
.custom-bg-primary {
    background-color: #1d1d1f !important;
}
</style>

<?php include 'includes/footer.php'; ?>
