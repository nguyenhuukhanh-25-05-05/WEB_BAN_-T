<?php
// Bắt đầu phiên làm việc
session_start();

// Nhúng file kết nối CSDL
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';
require_once 'includes/auth_functions.php';

// YÊU CẦU ĐĂNG NHẬP MỚI CHO THANH TOÁN
require_login();

// Thực hiện đồng bộ giỏ hàng ngay khi bắt đầu
syncCartWithDatabase($pdo);

// Kiểm tra giỏ hàng có đồ không, nếu không quay lại trang chủ
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cartItems) && !isset($_GET['order'])) {
    header("Location: index.php");
    exit;
}

// Tính tổng tiền cần thanh toán
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['qty'];
}

/**
 * XỬ LÝ ĐẶT HÀNG (Khi khách nhấn nút "Xác nhận đặt hàng")
 */
if (isset($_POST['place_order'])) {
    if (!verify_csrf_token()) {
        die("Yêu cầu không hợp lệ (CSRF Token mismatch)");
    }
    // Lấy thông tin từ Form gửi lên qua POST
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $payment = $_POST['payment_method'];
    $userId = get_logged_in_user_id(); // Lấy ID người dùng đang đăng nhập
    $isInstallment = isset($_SESSION['is_installment']) && $_SESSION['is_installment'] ? 'true' : 'false';

    // Thực hiện chèn đơn hàng vào bảng orders trong Postgres
    $sqlOrder = "INSERT INTO orders (customer_name, customer_phone, total_price, status, payment_method, user_id, is_installment) VALUES (?, ?, ?, 'Chờ duyệt', ?, ?, ?) RETURNING id";
    $stmtOrder = $pdo->prepare($sqlOrder);
    $stmtOrder->execute([$name, $phone, $total, $payment, $userId, $isInstallment]);
    $orderId = $stmtOrder->fetchColumn(); // Lấy ID vừa chèn (Postgres dùng RETURNING)

    // Lưu từng sản phẩm trong giỏ vào bảng order_items
    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
    $stmtItem = $pdo->prepare($sqlItem);
    foreach ($cartItems as $pid => $item) {
        $stmtItem->execute([$orderId, $pid, $item['name'], $item['price'], $item['qty']]);
    }
    
    // Sau khi lưu đơn thành công, xóa sạch giỏ hàng trong Session và Database
    unset($_SESSION['cart']);
    unset($_SESSION['is_installment']); // Xóa flag trả góp sau khi đặt hàng
    
    $stmtClearCart = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ?");
    $stmtClearCart->execute([session_id()]);
    
    // Chuyển hướng sang trang thông báo thành công
    header("Location: checkout.php?order=success");
    exit;
}

// Cấu hình trang
$pageTitle = "Thanh toán | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <!-- Hiển thị thông báo khi đặt hàng thành công -->
            <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
                <div class="glass-panel p-5 text-center animate-reveal border-0 py-huge">
                    <div class="mb-4 opacity-50 animate-float">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-3">Đơn hàng thành công!</h2>
                    <p class="text-secondary h5 fw-light max-w-600 mx-auto px-lg-5 mb-5">
                        Cảm ơn bạn đã tin tưởng NHK Mobile. Đơn hàng của bạn đang được xử lý và nhân viên sẽ liên hệ xác nhận trong giây lát.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="index.php" class="btn-apple btn-lg px-5 shadow-lg">Về trang chủ</a>
                        <a href="order_history.php" class="btn-outline-apple btn-lg px-5">Theo dõi đơn hàng</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-5">
                    <div class="col-lg-7 animate-reveal">
                        <h2 class="display-5 fw-bold mb-5">Thông tin nhận hàng.</h2>
                        <form action="checkout.php" method="POST" id="checkoutForm">
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-uppercase text-secondary">Họ và tên khách hàng</label>
                                    <input type="text" name="full_name" class="form-control rounded-pill border-light bg-light p-3 px-4" placeholder="Ví dụ: Nguyễn Văn A" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-secondary">Số điện thoại</label>
                                    <input type="tel" name="phone" class="form-control rounded-pill border-light bg-light p-3 px-4" placeholder="0333..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-secondary">Email (Tùy chọn)</label>
                                    <input type="email" class="form-control rounded-pill border-light bg-light p-3 px-4" placeholder="email@example.com">
                                </div>
                                <div class="col-md-12 mt-5">
                                     <h3 class="fw-bold mb-4">Phương thức thanh toán</h3>
                                     <div class="glass-panel p-3 mb-3 d-flex align-items-center gap-3 border-light clickable-option">
                                          <input type="radio" name="payment_method" value="COD" checked id="cod" class="ms-2">
                                          <label class="fw-bold mb-0 flex-grow-1 py-1" for="cod">Thanh toán khi nhận hàng (COD)</label>
                                          <i class="bi bi-cash-stack text-success fs-4 me-2"></i>
                                     </div>
                                     <div class="glass-panel p-3 d-flex align-items-center gap-3 border-light clickable-option">
                                          <input type="radio" name="payment_method" value="Momo" id="momo" class="ms-2">
                                          <label class="fw-bold mb-0 flex-grow-1 py-1" for="momo">Chuyển khoản / Ví điện tử</label>
                                          <i class="bi bi-qr-code text-primary fs-4 me-2"></i>
                                     </div>
                                </div>
                            </div>
                    </div>

                    <!-- Tóm tắt đơn hàng V2.0 -->
                    <div class="col-lg-5 animate-reveal" style="animation-delay: 0.2s">
                        <div class="glass-panel p-5 sticky-top border-2" style="top: 100px;">
                            <h4 class="fw-bold mb-4">Đơn hàng của bạn</h4>
                            <div class="cart-items-summary mb-4">
                                <?php foreach ($cartItems as $pid => $item): ?>
                                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-light">
                                     <div class="bg-white rounded-3 p-1 shadow-sm" style="width: 60px; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;">
                                         <img src="assets/images/<?php echo e($item['image']); ?>" class="img-fluid" style="max-height: 45px;" onerror="this.src='https://placehold.co/55'">
                                     </div>
                                     <div class="flex-grow-1">
                                          <div class="fw-bold small"><?php echo e($item['name']); ?></div>
                                          <div class="text-secondary small">SL: <?php echo (int)$item['qty']; ?></div>
                                     </div>
                                     <div class="fw-bold text-nowrap"><?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>₫</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-5">
                                <h4 class="fw-bold mb-0">Tổng tiền:</h4>
                                <h4 class="price-premium text-primary mb-0" style="font-size: 2rem;">
                                    <?php echo number_format($total, 0, ',', '.'); ?>₫
                                </h4>
                            </div>
                            <button type="submit" name="place_order" class="btn-apple w-100 rounded-pill py-3 fw-bold mt-5 shadow-lg">Xác nhận thanh toán</button>
                        </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
