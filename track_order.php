<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

$order = null;
$error = null;
$items = [];

// Xử lý khi nhấn nút Tra cứu
if (isset($_GET['order_id']) && isset($_GET['phone'])) {
    $orderId = (int)$_GET['order_id'];
    $phone = trim($_GET['phone']);

    if (empty($orderId) || empty($phone)) {
        $error = "Vui lòng nhập đầy đủ Mã đơn hàng và Số điện thoại.";
    } else {
        // Truy vấn đơn hàng khớp ID và Số điện thoại
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_phone = ?");
        $stmt->execute([$orderId, $phone]);
        $order = $stmt->fetch();

        if (!$order) {
            $error = "Không tìm thấy đơn hàng nào khớp với thông tin đã cung cấp.";
        } else {
            // Lấy chi tiết sản phẩm trong đơn
            $stmtItems = $pdo->prepare("
                SELECT order_items.*, products.image 
                FROM order_items 
                LEFT JOIN products ON order_items.product_id = products.id 
                WHERE order_id = ?
            ");
            $stmtItems->execute([$order['id']]);
            $items = $stmtItems->fetchAll();
        }
    }
}

$pageTitle = "Tra cứu đơn hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<style>
.track-card {
    border: none;
    border-radius: 1.5rem;
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.08);
    overflow: hidden;
}
.track-header {
    background: linear-gradient(135deg, #1d1d1f 0%, #434343 100%);
    color: #fff;
    padding: 3rem 2rem;
    text-align: center;
}
.status-pill {
    padding: 0.5rem 1.25rem;
    border-radius: 50rem;
    font-weight: 600;
    font-size: 0.9rem;
}
.order-item-img {
    width: 64px;
    height: 64px;
    object-fit: contain;
    background: #fff;
    border-radius: 12px;
    padding: 4px;
    border: 1px solid #eee;
}
</style>

<main class="bg-premium-light min-vh-100 pb-5" style="padding-top: 80px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                
                <!-- Search Form -->
                <div class="track-card bg-white mb-5 animate-reveal">
                    <div class="track-header">
                        <i class="bi bi-box-seam display-4 mb-3 d-block opacity-75"></i>
                        <h2 class="fw-800 mb-2">Theo dõi đơn hàng</h2>
                        <p class="mb-0 opacity-75">Nhập thông tin để cập nhật trạng thái đơn hàng của bạn.</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="track_order.php" method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase tracking-wider text-secondary">Mã đơn hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-hash"></i></span>
                                    <input type="number" name="order_id" class="form-control bg-light border-0 py-2" placeholder="VD: 1024" value="<?php echo htmlspecialchars($_GET['order_id'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase tracking-wider text-secondary">Số điện thoại đặt hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-phone"></i></span>
                                    <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="VD: 0987xxx" value="<?php echo htmlspecialchars($_GET['phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-search me-2"></i> TRA CỨU NGAY
                                </button>
                            </div>
                        </form>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 rounded-4 mt-4 mb-0 d-flex align-items-center">
                                <i class="bi bi-exclamation-circle-fill me-3 fs-4"></i>
                                <div><?php echo $error; ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Results -->
                <?php if ($order): ?>
                    <div class="track-card bg-white animate-reveal" style="animation-delay: 0.1s;">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary small text-uppercase fw-bold d-block mb-1">Kết quả cho</span>
                                <h4 class="fw-bold mb-0">Đơn hàng #<?php echo $order['id']; ?></h4>
                            </div>
                            <?php
                                $statusClass = 'bg-secondary';
                                $statusText = $order['status'];
                                $s = mb_strtolower($order['status'], 'UTF-8');
                                if (str_contains($s, 'chờ') || str_contains($s, 'pending')) { $statusClass = 'bg-warning text-dark'; $statusText = '⏳ Chờ duyệt'; }
                                elseif (str_contains($s, 'duyệt') || str_contains($s, 'approved')) { $statusClass = 'bg-info text-white'; $statusText = '📦 Đang lấy hàng'; }
                                elseif (str_contains($s, 'đang giao') || str_contains($s, 'shipping')) { $statusClass = 'bg-primary text-white'; $statusText = '🚚 Đang giao'; }
                                elseif (str_contains($s, 'hoàn thành') || str_contains($s, 'completed')) { $statusClass = 'bg-success text-white'; $statusText = '✅ Thành công'; }
                                elseif (str_contains($s, 'hủy') || str_contains($s, 'cancelled')) { $statusClass = 'bg-danger text-white'; $statusText = '❌ Đã hủy'; }
                            ?>
                            <span class="status-pill <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <hr class="opacity-10 mb-4">
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <p class="text-secondary small text-uppercase fw-bold mb-2">Thông tin người nhận</p>
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name']); ?></h6>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($order['customer_address']); ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="text-secondary small text-uppercase fw-bold mb-2">Thời gian đặt</p>
                                    <h6 class="fw-bold mb-1"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></h6>
                                    <p class="text-muted small mb-0">Thanh toán: <?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></p>
                                </div>
                            </div>

                            <div class="order-items bg-light rounded-4 p-3 mb-4">
                                <?php foreach ($items as $item): ?>
                                    <div class="d-flex align-items-center mb-3 last-child-mb-0">
                                        <img src="assets/images/<?php echo $item['image']; ?>" class="order-item-img me-3" onerror="this.src='https://placehold.co/100x100?text=SP'">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <span class="text-secondary small">Số lượng: <?php echo $item['quantity']; ?></span>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-dark small"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-4">
                                <span class="fw-bold text-secondary">Tổng cộng:</span>
                                <h4 class="fw-900 text-danger mb-0"><?php echo number_format($order['total_price'], 0, ',', '.'); ?>đ</h4>
                            </div>

                            <div class="text-center mt-5">
                                <p class="small text-muted mb-4 text-center mx-auto" style="max-width: 400px;">Nếu bạn có bất kỳ thắc mắc nào về đơn hàng, vui lòng liên hệ hotline <strong>1800 1234</strong> để được hỗ trợ nhanh nhất.</p>
                                <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill px-4 me-2"><i class="bi bi-printer me-2"></i> In đơn hàng</button>
                                <a href="product.php" class="btn btn-dark btn-sm rounded-pill px-4">Tiếp tục mua hàng</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
