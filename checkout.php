<?php
/**
 * NHK Mobile - Checkout Process
 * 
 * Description: Finalizes the purchase flow by collecting customer 
 * information, payment preference, and creating the order record in 
 * the database. Supports standard and installment flags.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.2
 * Date: 2026-04-19
 */
// QUAN TRỌNG: Phải load auth_functions.php TRƯỚC để nó quản lý session
// Không gọi session_start() ở đây vì auth_functions.php đã lo
require_once 'includes/auth_functions.php';
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';

// Authentication requirement for checkout
require_login();

// Synchronize cart state for current session
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
    // Lấy thông tin từ Form gửi lên qua POST
    $name    = trim($_POST['full_name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '') ?: 'Tại cửa hàng';  // Fallback khi rỗng
    $payment = $_POST['payment_method'] ?? 'COD';
    $userId  = get_logged_in_user_id(); // null nếu là admin
    // Cast boolean đúng cho PostgreSQL
    $isInstallmentVal = (isset($_SESSION['is_installment']) && $_SESSION['is_installment'] === true) ? 'true' : 'false';
    
    // Validate thông tin
    if (empty($name) || empty($phone)) {
        $error = "Vui lòng điền đầy đủ họ tên và số điện thoại";
    } else {
        try {
            // Kiểm tra kết nối DB trước
            if (!$pdo) {
                throw new Exception("Không có kết nối cơ sở dữ liệu");
            }

            // Thực hiện chèn đơn hàng vào bảng orders trong Postgres
            // Ghi chú: is_installment dùng string 'true'/'false' cho PostgreSQL BOOLEAN qua PDO
            $sqlOrder = "INSERT INTO orders (customer_name, customer_phone, customer_address, total_price, status, payment_method, user_id, is_installment) 
                         VALUES (?, ?, ?, ?, 'Chờ duyệt', ?, ?, CAST(? AS BOOLEAN)) RETURNING id";
            $stmtOrder = $pdo->prepare($sqlOrder);
            $stmtOrder->execute([$name, $phone, $address, $total, $payment, $userId, $isInstallmentVal]);
            
            // Lấy ID vừa chèn từ RETURNING id
            $orderId = $stmtOrder->fetchColumn();

            if (!$orderId) {
                throw new Exception("Không thể lấy ID đơn hàng sau khi tạo");
            }

            // Lưu từng sản phẩm trong giỏ vào bảng order_items
            $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
            $stmtItem = $pdo->prepare($sqlItem);
            foreach ($cartItems as $pid => $item) {
                $stmtItem->execute([$orderId, $pid, $item['name'], $item['price'], (int)$item['qty']]);
            }
            
            // Lưu thông tin đơn vừa đặt vào session để trang thành công có thể hiển thị/tra cứu
            $_SESSION['last_order_id'] = $orderId;
            $_SESSION['last_order_phone'] = $phone;
            
            // Sau khi lưu đơn thành công, xóa sạch giỏ hàng trong Session và Database
            unset($_SESSION['cart']);
            unset($_SESSION['is_installment']); // Xóa flag trả góp sau khi đặt hàng
            
            // Xóa giỏ hàng trong DB theo user_id (đúng với cách lưu cart)
            $clearUserId = $userId ?? (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null);
            if ($clearUserId) {
                $stmtClearCart = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $stmtClearCart->execute([$clearUserId]);
            }
            
            // Fallback: xóa theo session_id nếu có
            $stmtClearCartSession = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ?");
            $stmtClearCartSession->execute([session_id()]);
            
            // Chuyển hướng sang trang thông báo thành công
            header("Location: checkout.php?order=success");
            exit;
        } catch (Exception $e) {
            error_log("[Checkout] Order creation error: " . $e->getMessage());
            $error = "Có lỗi xảy ra khi tạo đơn hàng: " . $e->getMessage() . ". Vui lòng thử lại hoặc liên hệ cửa hàng.";
        }
    }
}

// Cấu hình trang
$pageTitle = "Thanh toán | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <!-- Hiển thị thông báo khi đặt hàng thành công (Premium UI) -->
            <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
                <div class="row justify-content-center py-5">
                    <div class="col-md-8 col-lg-6">
                        <div class="card border-0 rounded-5 shadow-sm p-4 p-md-5 text-center bg-white">
                            <div class="success-icon-wrapper mb-4">
                                <div class="nav-icon bg-success bg-opacity-10 text-success mx-auto rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 50px;">
                                    <i class="bi bi-check-circle-fill bounce-in"></i>
                                </div>
                            </div>
                            
                            <h1 class="fw-800 mb-3">Đặt hàng thành công!</h1>
                            <p class="text-secondary fs-5 mb-4 px-md-4">
                                Cảm ơn bạn đã tin tưởng **NHK Mobile**. Đơn hàng của bạn đã được tiếp nhận và đang chờ bộ phận kỹ thuật kiểm tra kho sản phẩm.
                            </p>
                            
                            <div class="bg-light rounded-4 p-4 mb-5 text-start border">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <span class="fw-700 small text-uppercase letter-spacing text-muted">Bước tiếp theo</span>
                                </div>
                                <p class="small text-secondary mb-0">Chúng tôi sẽ gọi điện xác nhận lại với bạn trong vòng 15-30 phút tới. Vui lòng giữ máy.</p>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center mt-2">
                                <a href="track_order.php?order_id=<?php echo $_SESSION['last_order_id']; ?>&phone=<?php echo urlencode($_SESSION['last_order_phone'] ?? ''); ?>" class="btn-main btn-primary px-5 py-3 rounded-pill fw-700 shadow">
                                    <i class="bi bi-geo-alt me-2"></i>Theo dõi đơn hàng
                                </a>
                                <a href="index.php" class="btn-main btn-outline px-5 py-3 rounded-pill fw-700 text-dark border-dark">
                                    <i class="bi bi-house me-2"></i>Quay về trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Giao diện trang thanh toán (Nhập thông tin) -->
                <form action="checkout.php" method="POST" id="checkoutForm">
                <div class="row g-5">
                    <div class="col-lg-7">
                        <h2 class="fw-bold mb-4">Thông tin nhận hàng.</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger border-0 rounded-3 small fw-600 mb-4"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Họ và tên khách hàng</label>
                                    <input type="text" name="full_name" class="form-control rounded-3 border-0 bg-light p-3" placeholder="Ví dụ: Nguyễn Văn A" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Số điện thoại liên lạc</label>
                                    <input type="tel" name="phone" class="form-control rounded-3 border-0 bg-light p-3" placeholder="0333..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Địa chỉ Email (Không bắt buộc)</label>
                                    <input type="email" class="form-control rounded-3 border-0 bg-light p-3" placeholder="email@example.com">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Địa chỉ giao hàng</label>
                                    <textarea name="address" class="form-control rounded-3 border-0 bg-light p-3" rows="2" placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố (để trống = nhận tại cửa hàng)"></textarea>
                                </div>
                                <div class="col-md-12 mt-4">
                                     <h2 class="fw-bold mb-4">Cách thức thanh toán</h2>
                                     <div class="bg-white border rounded-4 p-3 mb-3 d-flex align-items-center gap-3">
                                          <input type="radio" name="payment_method" value="COD" checked id="cod">
                                          <label class="fw-bold mb-0 flex-grow-1" for="cod">Trả tiền mặt khi nhận hàng (COD)</label>
                                          <i class="bi bi-cash text-success"></i>
                                     </div>
                                     <div class="bg-white border rounded-4 p-3 d-flex align-items-center gap-3">
                                          <input type="radio" name="payment_method" value="Momo" id="momo">
                                          <label class="fw-bold mb-0 flex-grow-1" for="momo">Chuyển khoản Online / Ví Momo</label>
                                          <i class="bi bi-phone text-primary"></i>
                                     </div>
                                </div>
                            </div>
                    </div>

                    <!-- Tóm tắt đơn hàng bên phải -->
                    <div class="col-lg-5">
                        <div class="bg-light rounded-5 p-5 position-sticky" style="top: 100px;">
                            <h4 class="fw-bold mb-4 italic">Đơn hàng của bạn</h4>
                            <div class="cart-items-summary mb-4">
                                <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                                     <img src="assets/images/<?php echo $item['image']; ?>" width="55" class="rounded bg-white p-2 shadow-sm" onerror="this.src='https://placehold.co/55'">
                                     <div class="flex-grow-1">
                                          <div class="fw-bold small"><?php echo $item['name']; ?></div>
                                          <div class="text-secondary small">Số lượng: <?php echo $item['qty']; ?></div>
                                     </div>
                                     <div class="fw-bold text-nowrap"><?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>₫</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <h4 class="fw-bold">Tổng cộng:</h4>
                                <h4 class="fw-bold text-primary"><?php echo number_format($total, 0, ',', '.'); ?>₫</h4>
                            </div>
                            <button type="submit" name="place_order" class="btn btn-dark w-100 rounded-pill py-3 fw-bold mt-5 shadow">Xác nhận đặt mua ngay</button>
                        </div>
                    </div>
                </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
 
// checkout fixed 2026-04-26 
