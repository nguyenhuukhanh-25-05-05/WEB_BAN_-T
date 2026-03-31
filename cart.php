<?php
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';
require_once 'includes/auth_functions.php';

// Thực hiện đồng bộ giỏ hàng ngay khi bắt đầu
syncCartWithDatabase($pdo);

/**
 * 1. XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ HÀNG
 * Nhận ID từ URL (VD: cart.php?add=5)
 */
if (isset($_GET['add'])) {
    // YÊU CẦU ĐĂNG NHẬP MỚI CHO MUA HÀNG
    require_login();
    
    $productId = (int)$_GET['add'];
    $installment = isset($_GET['installment']) ? (int)$_GET['installment'] : 0;
    
    // Lấy thông tin sản phẩm từ CSDL để chắc chắn ID tồn tại
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        // Nếu mua TRẢ GÓP: Xóa sạch giỏ cũ và bật flag trả góp
        if ($installment === 1) {
            $_SESSION['cart'] = [];
            $_SESSION['is_installment'] = true;
        } else {
            // Nếu mua THƯỜNG: Tắt flag trả góp
            $_SESSION['is_installment'] = false;
        }

        // Nếu chưa có giỏ hàng, khởi tạo mảng trống
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        // Nếu sản phẩm đã có trong giỏ, tăng số lượng
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['qty']++;
        } else {
            // Nếu chưa có, thêm mới với số lượng là 1
            $_SESSION['cart'][$productId] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'qty' => 1
            ];
        }
        // Gọi hàm đồng bộ ngay lập tức để lưu DB
        syncCartWithDatabase($pdo);
    }

    // Nếu là trả góp, đi thẳng đến trang thanh toán
    if ($installment === 1) {
        header("Location: checkout.php");
    } else {
        // Quay lại trang giỏ hàng để hiển thị
        header("Location: cart.php");
    }
    exit;
}

/**
 * 2. XỬ LÝ XÓA SẢN PHẨM KHỎI GIỎ
 */
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    removeFromCartDB($pdo, $id); // Xóa khỏi DB
    header("Location: cart.php");
    exit;
}

/**
 * 3. XỬ LÝ CẬP NHẬT SỐ LƯỢNG (Khi nhấn nút Cập nhật)
 */
if (isset($_POST['update_cart'])) {
    if (!verify_csrf_token()) {
        die("Yêu cầu không hợp lệ (CSRF Token mismatch)");
    }
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]); // Nếu số <= 0 thì xóa luôn
            removeFromCartDB($pdo, $id);
        } else {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = $qty;    // Ngược lại cập nhật số lượng mới
            }
        }
    }
    syncCartWithDatabase($pdo); // Đồng bộ lại toàn bộ sau update
    header("Location: cart.php");
    exit;
}

// Cấu hình Header
$pageTitle = "Giỏ hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';

$total = 0;
// Lấy danh sách sản phẩm trong giỏ hàng từ Session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

    <main class="py-huge">
        <div class="container">
            <h1 class="display-3 fw-bold mb-5 animate-reveal">Giỏ hàng của bạn.</h1>

            <?php if (empty($cartItems)): ?>
                <div class="glass-panel p-5 text-center animate-reveal">
                    <div class="mb-4 opacity-25">
                        <i class="bi bi-cart-x" style="font-size: 80px;"></i>
                    </div>
                    <h3 class="fw-bold">Giỏ hàng còn trống.</h3>
                    <p class="text-secondary mb-4">Hãy chọn cho mình những siêu phẩm công nghệ mới nhất nhé!</p>
                    <a href="product.php" class="btn-apple rounded-pill px-5 py-3 shadow-lg">Tiếp tục mua sắm</a>
                </div>
            <?php else: ?>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <div class="row g-5">
                        <div class="col-lg-8 animate-reveal">
                            <?php foreach ($cartItems as $id => $item): 
                                $subtotal = $item['price'] * $item['qty'];
                                $total += $subtotal;
                            ?>
                                <div id="cart-row-<?php echo (int)$id; ?>" class="glass-panel p-4 mb-4 d-flex align-items-center justify-content-between border-light">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="bg-light rounded-4 overflow-hidden" style="width: 100px; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;">
                                            <img src="assets/images/<?php echo e($item['image']); ?>" class="img-fluid" style="max-height: 80%;" onerror="this.src='https://via.placeholder.com/100'">
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1"><?php echo e($item['name']); ?></h5>
                                            <p class="price-premium text-primary mb-0"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="number" name="qty[<?php echo (int)$id; ?>]" value="<?php echo (int)$item['qty']; ?>" class="form-control text-center rounded-pill border-light bg-light cart-qty-input" data-product-id="<?php echo (int)$id; ?>" style="width: 70px; height: 40px;">
                                            <a href="cart.php?remove=<?php echo (int)$id; ?>" class="btn btn-light rounded-circle shadow-sm" onclick="return confirm('Xóa khỏi giỏ hàng?')">
                                                <i class="bi bi-trash text-danger"></i>
                                            </a>
                                        </div>
                                        <div id="subtotal-<?php echo (int)$id; ?>" class="mt-2 small fw-bold text-secondary">
                                            Tổng: <?php echo number_format($subtotal, 0, ',', '.'); ?>₫
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- TOTAL SUMMARY V2.0 -->
                        <div class="col-lg-4 animate-reveal" style="animation-delay: 0.2s">
                            <div class="glass-panel p-5 sticky-top" style="top: 100px; border-width: 2px;">
                                <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                                <div class="d-flex justify-content-between mb-3 text-secondary">
                                    <span>Tạm tính</span>
                                    <span class="cart-total-value"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4 border-bottom pb-3 text-secondary">
                                    <span>Giao hàng</span>
                                    <span class="text-success fw-bold">Miễn phí</span>
                                </div>
                                <div class="d-flex justify-content-between mb-5">
                                    <h4 class="fw-bold">Tổng cộng</h4>
                                    <h4 class="price-premium text-primary cart-total-value" style="font-size: 1.8rem;">
                                        <?php echo number_format($total, 0, ',', '.'); ?>₫
                                    </h4>
                                </div>
                                <a href="checkout.php" class="btn-apple btn-lg w-100 py-3 shadow-xl">Thanh toán ngay</a>
                                <p class="text-center mt-3 mb-0 small text-secondary">An toàn & Bảo mật 100%</p>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
