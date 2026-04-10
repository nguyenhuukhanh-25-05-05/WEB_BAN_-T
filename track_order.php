<?php
/**
 * NHK Mobile - Order History & Tracking System
 */
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

// Initialization
$order = null;
$orders_list = [];
$error = null;
$items = [];

// 1. LOGGED IN MODE - Automatic History
$is_logged_in = isset($_SESSION['user_id']);
if ($is_logged_in) {
    $userId = $_SESSION['user_id'];
    $stmtUserOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmtUserOrders->execute([$userId]);
    $orders_list = $stmtUserOrders->fetchAll();
}

// 2. SEARCH MODE (For specific order)
if (isset($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];
    
    if ($is_logged_in) {
        // Nếu đã đăng nhập, chỉ cho phép xem đơn của chính mình
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
    } else {
        // Nếu là khách, yêu cầu kèm SĐT
        $phone = trim($_GET['phone'] ?? '');
        if (empty($phone)) {
            $error = "Vui lòng cung cấp số điện thoại để xem chi tiết đơn hàng.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_phone = ?");
            $stmt->execute([$orderId, $phone]);
        }
    }
    
    if (isset($stmt)) {
        $order = $stmt->fetch();
        if ($order) {
            $stmtItems = $pdo->prepare("SELECT order_items.*, products.image FROM order_items LEFT JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
            $stmtItems->execute([$order['id']]);
            $items = $stmtItems->fetchAll();
        } else {
            $error = "Không tìm thấy đơn hàng này.";
        }
    }
}

$pageTitle = $is_logged_in ? "Lịch sử mua hàng | NHK Mobile" : "Tra cứu đơn hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<style>
.track-card {
    border: none;
    border-radius: 1.5rem;
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.06);
    overflow: hidden;
}
.status-pill {
    padding: 0.5rem 1.25rem;
    border-radius: 50rem;
    font-weight: 600;
    font-size: 0.85rem;
}
.order-item-img {
    width: 64px; height: 64px;
    object-fit: contain;
    background: #fff;
    border-radius: 12px;
    padding: 4px;
    border: 1px solid #eee;
}

@media print {
    body { background: #fff !important; }
    nav, footer, .btn, .no-print, .alert { display: none !important; }
    .container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .track-card { 
        box-shadow: none !important; 
        border: 2px solid #000 !important; 
        width: 100% !important; 
        margin: 0 !important;
        page-break-inside: avoid;
    }
    .order-items { background: #fff !important; }
    .main-content { padding-top: 0 !important; }
    .bg-premium-light { background: #fff !important; }
    main { padding-top: 0 !important; }
}
</style>

<main class="bg-premium-light min-vh-100 pb-5" style="padding-top: 80px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-4 border-0 mb-4 no-print"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($order): ?>
                    <!-- DETAILED VIEW -->
                    <div class="track-card bg-white p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-5 no-print">
                            <a href="track_order.php" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-2"></i> Quay lại</a>
                            <button onclick="window.print()" class="btn btn-primary rounded-pill"><i class="bi bi-printer me-2"></i> In đơn hàng</button>
                        </div>

                        <div class="row mb-5 align-items-center">
                            <div class="col-6">
                                <h2 class="fw-900 mb-0">HÓA ĐƠN</h2>
                                <p class="text-muted small">Mã đơn: #<?php echo $order['id']; ?></p>
                            </div>
                            <div class="col-6 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <div class="brand-logo-box lg mb-2">NHK</div>
                                    <p class="small text-muted fw-bold mb-0">NHK Mobile Authorized</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-5 border-top pt-4">
                            <div class="col-md-6">
                                <p class="text-secondary small text-uppercase fw-bold mb-2">Thông tin người nhận</p>
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></h6>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></p>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($order['customer_address'] ?? 'Tại cửa hàng'); ?></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="text-secondary small text-uppercase fw-bold mb-2">Chi tiết giao dịch</p>
                                <h6 class="fw-bold mb-1">Ngày: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></h6>
                                <p class="text-muted small mb-0">Thanh toán: <?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></p>
                                <p class="text-muted small mb-0">Trạng thái: <strong><?php echo $order['status']; ?></strong></p>
                            </div>
                        </div>

                        <div class="order-items mb-4">
                            <table class="table table-borderless align-middle">
                                <thead class="border-bottom">
                                    <tr class="text-muted small">
                                        <th>SẢN PHẨM</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">ĐƠN GIÁ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/images/<?php echo $item['image']; ?>" class="order-item-img me-3 no-print" onerror="this.src='https://placehold.co/100'">
                                                <span class="fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end fw-bold"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="2" class="pt-4 h5 fw-bold">Tổng thanh toán:</td>
                                        <td class="pt-4 h4 fw-900 text-danger text-end"><?php echo number_format($order['total_price'], 0, ',', '.'); ?>đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-center mt-5 pt-4 border-top small text-muted">
                            <p class="mb-1">Cảm ơn bạn đã tin tưởng lựa chọn NHK Mobile!</p>
                            <p class="mb-0">Hotline: 1800 1234 | Website: nhkmobile.vn</p>
                        </div>
                    </div>

                <?php elseif ($is_logged_in): ?>
                    <!-- LIST VIEW FOR MEMBERS -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-900 mb-0">Lịch sử mua hàng</h2>
                        <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo count($orders_list); ?> Đơn hàng</span>
                    </div>

                    <?php if (empty($orders_list)): ?>
                        <div class="track-card bg-white p-5 text-center">
                            <i class="bi bi-bag-x display-1 text-light mb-4 d-block"></i>
                            <h4>Bạn chưa có đơn hàng nào</h4>
                            <p class="text-muted mb-4">Hãy bắt đầu trải nghiệm mua sắm cùng NHK Mobile ngay hôm nay.</p>
                            <a href="product.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($orders_list as $o): ?>
                                <div class="col-12">
                                    <div class="track-card bg-white p-4 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-4 p-3"><i class="bi bi-receipt text-primary fs-3"></i></div>
                                            <div>
                                                <h6 class="fw-bold mb-1">Đơn hàng #<?php echo $o['id']; ?></h6>
                                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($o['created_at'])); ?> • <?php echo number_format($o['total_price'], 0, ',', '.'); ?>đ</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 small fw-bold"><?php echo $o['status']; ?></span>
                                            <a href="track_order.php?order_id=<?php echo $o['id']; ?>" class="btn btn-outline-dark rounded-pill btn-sm px-4 fw-bold">Chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- GUEST SEARCH FORM -->
                    <div class="track-card bg-white overflow-hidden">
                        <div class="p-5 bg-dark text-white text-center">
                            <i class="bi bi-search display-3 mb-3"></i>
                            <h2 class="fw-900">Tra cứu đơn hàng</h2>
                            <p class="opacity-75">Vui lòng nhập thông tin để xem trạng thái đơn hàng</p>
                        </div>
                        <div class="p-5">
                            <form action="track_order.php" method="GET" class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">MÃ ĐƠN HÀNG</label>
                                    <input type="number" name="order_id" class="form-control bg-light border-0 py-3" placeholder="VD: 1024" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">SỐ ĐIỆN THOẠI</label>
                                    <input type="text" name="phone" class="form-control bg-light border-0 py-3" placeholder="VD: 098xxx" required>
                                </div>
                                <div class="col-12 pt-3">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">KIỂM TRA NGAY</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
