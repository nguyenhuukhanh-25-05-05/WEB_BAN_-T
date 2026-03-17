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
    // Lấy thông tin từ Form gửi lên qua POST
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $payment = $_POST['payment_method'];
    $userId = get_logged_in_user_id(); // Lấy ID người dùng đang đăng nhập

    // Thực hiện chèn đơn hàng vào bảng orders trong Postgres
    $sqlOrder = "INSERT INTO orders (customer_name, customer_phone, total_price, status, payment_method, user_id) VALUES (?, ?, ?, 'Chờ duyệt', ?, ?) RETURNING id";
    $stmtOrder = $pdo->prepare($sqlOrder);
    $stmtOrder->execute([$name, $phone, $total, $payment, $userId]);
    $orderId = $stmtOrder->fetchColumn(); // Lấy ID vừa chèn (Postgres dùng RETURNING)

    // Lưu từng sản phẩm trong giỏ vào bảng order_items
    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
    $stmtItem = $pdo->prepare($sqlItem);
    foreach ($cartItems as $pid => $item) {
        $stmtItem->execute([$orderId, $pid, $item['name'], $item['price'], $item['qty']]);
    }
    
    // Sau khi lưu đơn thành công, xóa sạch giỏ hàng trong Session và Database
    unset($_SESSION['cart']);
    
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
                <div class="text-center py-5">
                    <img src="assets/images/logo-k.svg" height="40" class="mb-4 d-block mx-auto opacity-25">
                    <i class="bi bi-send-check display-1 text-primary mb-4 d-block"></i>
                    <h2 class="fw-bold">Đơn hàng đã được gửi đi!</h2>
                    <p class="text-secondary max-w-600 mx-auto">Đơn đặt hàng của bạn đã được gửi đến ban quản trị để phê duyệt. Vui lòng chờ nhân viên kiểm tra tình trạng kho và xác nhận lại với bạn trong ít phút tới.</p>
                    <div class="mt-5">
                        <a href="index.php" class="btn btn-dark rounded-pill px-5 py-3 fw-bold">Về trang chủ</a>
                        <a href="order_history.php" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold ms-3">Theo dõi đơn hàng</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Giao diện trang thanh toán (Nhập thông tin) -->
                <div class="row g-5">
                    <div class="col-lg-7">
                        <h2 class="fw-bold mb-4">Thông tin nhận hàng.</h2>
                        <form action="checkout.php" method="POST" id="checkoutForm">
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
                        </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
