<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

$order = null;
$orders_list = [];
$error = null;
$items = [];

// Xử lý khi nhấn nút Tra cứu
if (isset($_GET['phone'])) {
    $phone = trim($_GET['phone']);
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

    if (empty($phone)) {
        $error = "Vui lòng nhập Số điện thoại đã đặt hàng.";
    } else {
        if (!empty($orderId)) {
            // Trường hợp 1: Tra cứu Đích danh 1 đơn (ID + Phone)
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_phone = ?");
            $stmt->execute([$orderId, $phone]);
            $order = $stmt->fetch();

            if (!$order) {
                $error = "Không tìm thấy đơn hàng #$orderId khớp với số điện thoại này.";
            } else {
                // Lấy chi tiết sản phẩm
                $stmtItems = $pdo->prepare("SELECT order_items.*, products.image FROM order_items LEFT JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll();
            }
        } else {
            // Trường hợp 2: Tra cứu Toàn bộ đơn theo số điện thoại
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_phone = ? ORDER BY created_at DESC");
            $stmt->execute([$phone]);
            $orders_list = $stmt->fetchAll();

            if (!$orders_list) {
                $error = "Số điện thoại này chưa có đơn hàng nào tại NHK Mobile.";
            } elseif (count($orders_list) === 1) {
                // Nếu chỉ có 1 đơn thì tự động hiển thị chi tiết luôn cho nhanh
                $order = $orders_list[0];
                $stmtItems = $pdo->prepare("SELECT order_items.*, products.image FROM order_items LEFT JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll();
            }
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
                                <label class="form-label small fw-bold text-uppercase tracking-wider text-secondary">Mã đơn hàng (Tùy chọn)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-hash"></i></span>
                                    <input type="number" name="order_id" class="form-control bg-light border-0 py-2" placeholder="VD: 1024" value="<?php echo htmlspecialchars($_GET['order_id'] ?? ''); ?>">
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
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm transition-all hover-lift">
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

                <!-- Multiple Results (Order List View) -->
                <?php if (!empty($orders_list) && count($orders_list) > 1 && !$order): ?>
                    <div class="animate-reveal">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="bg-primary rounded-pill px-3 py-1 text-white small fw-bold">Tìm thấy <?php echo count($orders_list); ?> đơn hàng</div>
                            <div class="text-secondary small">Dưới đây là lịch sử đặt hàng của bạn</div>
                        </div>
                        
                        <div class="row g-3">
                            <?php foreach ($orders_list as $o): ?>
                                <div class="col-12">
                                    <div class="track-card bg-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-receipt text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Đơn hàng #<?php echo $o['id']; ?></h6>
                                                <small class="text-secondary"><?php echo date('d/m/Y', strtotime($o['created_at'])); ?> • <?php echo number_format($o['total_price'], 0, ',', '.'); ?>đ</small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            <?php
                                                $s = mb_strtolower($o['status'], 'UTF-8');
                                                $badgeClass = 'bg-warning text-dark';
                                                if (str_contains($s, 'hoàn thành')) $badgeClass = 'bg-success text-white';
                                                elseif (str_contains($s, 'đang giao')) $badgeClass = 'bg-primary text-white';
                                                elseif (str_contains($s, 'hủy')) $badgeClass = 'bg-danger text-white';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3 py-2 small"><?php echo $o['status']; ?></span>
                                            <a href="track_order.php?order_id=<?php echo $o['id']; ?>&phone=<?php echo urlencode($phone); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

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

                    <!-- Order Timeline (Premium UI) -->
                    <div class="px-4 pb-4">
                        <div class="order-timeline d-flex justify-content-between position-relative mt-4 mb-5">
                            <div class="timeline-line"></div>
                            <?php
                                $steps = [
                                    ['label' => 'Đã đặt', 'icon' => 'bi-cart-check', 'match' => 'chờ'],
                                    ['label' => 'Đã duyệt', 'icon' => 'bi-file-earmark-check', 'match' => 'đã duyệt'],
                                    ['label' => 'Đang giao', 'icon' => 'bi-truck', 'match' => 'đang giao'],
                                    ['label' => 'Hoàn thành', 'icon' => 'bi-house-check', 'match' => 'hoàn thành']
                                ];
                                
                                $currentStep = 0;
                                $s = mb_strtolower($order['status'], 'UTF-8');
                                if (str_contains($s, 'hoàn thành')) $currentStep = 3;
                                elseif (str_contains($s, 'đang giao')) $currentStep = 2;
                                elseif (str_contains($s, 'đã duyệt')) $currentStep = 1;
                                
                                // Nếu bị hủy thì không hiện timeline bình thường
                                $isCancelled = str_contains($s, 'hủy');
                            ?>

                            <?php if (!$isCancelled): ?>
                                <?php foreach ($steps as $index => $step): ?>
                                    <div class="timeline-step <?php echo $index <= $currentStep ? 'active' : ''; ?> text-center position-relative z-1" style="width: 25%;">
                                        <div class="step-icon mx-auto mb-2 d-flex align-items-center justify-content-center">
                                            <i class="bi <?php echo $step['icon']; ?>"></i>
                                        </div>
                                        <span class="step-label d-block fw-bold small"><?php echo $step['label']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="w-100 text-center py-3 bg-danger bg-opacity-10 rounded-4">
                                    <p class="text-danger fw-bold mb-0"><i class="bi bi-x-octagon-fill me-2"></i> Đơn hàng này đã bị hủy bỏ</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <style>
                    .order-timeline .timeline-line {
                        position: absolute;
                        top: 20px;
                        left: 12.5%;
                        right: 12.5%;
                        height: 4px;
                        background: #eee;
                        z-index: 0;
                    }
                    .timeline-step .step-icon {
                        width: 44px;
                        height: 44px;
                        background: #eee;
                        border-radius: 50%;
                        color: #aaa;
                        font-size: 1.25rem;
                        border: 4px solid #fff;
                        transition: all 0.4s;
                    }
                    .timeline-step.active .step-icon {
                        background: var(--primary);
                        color: #fff;
                    }
                    .timeline-step.active .step-label {
                        color: var(--primary);
                    }
                    .timeline-step.active ~ .timeline-step .step-icon {
                        background: #eee;
                    }
                    /* Line coloring */
                    .timeline-line::before {
                        content: '';
                        position: absolute;
                        left: 0;
                        top: 0;
                        height: 100%;
                        background: var(--primary);
                        width: <?php echo ($currentStep / 3) * 100; ?>%;
                        transition: width 1s ease-in-out;
                    }
                    </style>

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
