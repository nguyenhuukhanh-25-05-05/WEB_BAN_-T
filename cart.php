<?php
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';

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
    
    // Lấy thông tin sản phẩm từ CSDL để chắc chắn ID tồn tại
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
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
    // Quay lại trang giỏ hàng để hiển thị
    header("Location: cart.php");
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
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]); // Nếu số <= 0 thì xóa luôn
            removeFromCartDB($pdo, $id);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;    // Ngược lại cập nhật số lượng mới
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

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <h1 class="display-5 fw-bold mb-5 italic">Giỏ hàng của bạn.</h1>

            <!-- Nếu giỏ hàng trống -->
            <?php if (empty($cartItems)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-secondary mb-4"></i>
                    <h3>Giỏ hàng đang trống</h3>
                    <p class="text-secondary">Hãy quay lại sắm cho mình một chiếc điện thoại mới nhé!</p>
                    <a href="product.php" class="btn btn-dark rounded-pill px-5 mt-3">Tiếp tục mua sắm</a>
                </div>
            <?php else: ?>
                <!-- Form gửi dữ liệu bằng POST để cập nhật giỏ hàng -->
                <form action="cart.php" method="POST">
                    <div class="row g-5">
                        <div class="col-lg-8">
                            <?php foreach ($cartItems as $id => $item): 
                                $subtotal = $item['price'] * $item['qty']; // Tính thành tiền cho từng sản phẩm
                                $total += $subtotal; // Cộng dồn vào tổng tiền cuối cùng
                            ?>
                                <div class="border-bottom pb-4 mb-4 d-flex align-items-center justify-content-between">
                                     <div class="d-flex align-items-center gap-4">
                                          <div class="bg-light rounded-4 p-3" style="width: 100px;">
                                               <img src="assets/images/<?php echo $item['image']; ?>" class="img-fluid" onerror="this.src='https://via.placeholder.com/100'">
                                          </div>
                                          <div>
                                               <h5 class="fw-bold mb-1"><?php echo $item['name']; ?></h5>
                                               <p class="text-primary fw-bold mb-0"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                                          </div>
                                     </div>
                                     <div class="text-end">
                                          <div class="d-flex align-items-center gap-3">
                                               <!-- Input nhập số lượng -->
                                               <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $item['qty']; ?>" class="form-control text-center rounded-pill" style="width: 70px;">
                                               <a href="cart.php?remove=<?php echo $id; ?>" class="text-danger" onclick="return confirm('Xóa khỏi giỏ hàng?')"><i class="bi bi-trash"></i></a>
                                          </div>
                                          <div class="mt-2 small fw-bold">Thành tiền: <?php echo number_format($subtotal, 0, ',', '.'); ?>₫</div>
                                     </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <button type="submit" name="update_cart" class="btn btn-outline-dark rounded-pill px-4">Cập nhật giỏ hàng</button>
                        </div>

                        <!-- Cột tính tổng tiền -->
                        <div class="col-lg-4">
                            <div class="bg-light rounded-5 p-5 position-sticky" style="top: 100px;">
                                <h4 class="fw-bold mb-4">Tổng cộng</h4>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Tạm tính</span>
                                    <span><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                                    <span class="text-secondary">Giao hàng</span>
                                    <span class="text-success fw-bold">Miễn phí</span>
                                </div>
                                <div class="d-flex justify-content-between mb-5">
                                    <h4 class="fw-bold">Tổng tiền</h4>
                                    <h4 class="fw-bold text-primary"><?php echo number_format($total, 0, ',', '.'); ?>₫</h4>
                                </div>
                                <a href="checkout.php" class="btn btn-dark btn-lg w-100 rounded-pill py-3 fw-bold">Tiến hành đặt hàng</a>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
