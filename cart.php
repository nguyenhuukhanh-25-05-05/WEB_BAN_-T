<?php
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';
require_once 'includes/auth_functions.php';

syncCartWithDatabase($pdo);

if (isset($_GET['add'])) {
    require_login();
    
    $productId = (int)$_GET['add'];
    $installment = isset($_GET['installment']) ? (int)$_GET['installment'] : 0;
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        if ($installment === 1) {
            $_SESSION['cart'] = [];
            $_SESSION['is_installment'] = true;
        } else {
            $_SESSION['is_installment'] = false;
        }

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['qty']++;
        } else {
            $_SESSION['cart'][$productId] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'qty' => 1
            ];
        }
        syncCartWithDatabase($pdo);
    }

    if ($installment === 1) {
        header("Location: checkout.php");
    } else {
        header("Location: cart.php");
    }
    exit;
}

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    removeFromCartDB($pdo, $id);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['update_cart'])) {
    if (!verify_csrf_token()) {
        die("Yêu cầu không hợp lệ (CSRF Token mismatch)");
    }
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
            removeFromCartDB($pdo, $id);
        } else {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = $qty;
            }
        }
    }
    syncCartWithDatabase($pdo);
    header("Location: cart.php");
    exit;
}

$pageTitle = "Giỏ hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';

$total = 0;
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<main class="section-padding mt-5">
    <div class="container">
        <h1 class="hero-title text-start mb-5" style="font-size: 48px;">Giỏ hàng của bạn.</h1>

        <?php if (empty($cartItems)): ?>
            <div class="bg-light rounded-4 p-5 text-center">
                <div class="mb-4 opacity-10">
                    <i class="bi bi-cart-x" style="font-size: 100px;"></i>
                </div>
                <h3 class="fw-bold">Giỏ hàng còn trống.</h3>
                <p class="text-secondary mb-4">Hãy chọn cho mình những siêu phẩm công nghệ mới nhất nhé!</p>
                <a href="product.php" class="btn-primary-apple px-5 py-3">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <form action="cart.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <div class="row g-5">
                    <div class="col-lg-8">
                        <?php foreach ($cartItems as $id => $item): 
                            $subtotal = $item['price'] * $item['qty'];
                            $total += $subtotal;
                        ?>
                            <div id="cart-row-<?php echo (int)$id; ?>" class="bg-white border rounded-4 p-4 mb-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="bg-light rounded-3 overflow-hidden" style="width: 80px; aspect-ratio: 1; display: flex; align-items: center; justify-content: center;">
                                        <img src="assets/images/<?php echo e($item['image']); ?>" class="img-fluid" style="max-height: 80%; object-fit: contain;" onerror="this.src='https://placehold.co/100'">
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1" style="font-size: 16px;"><?php echo e($item['name']); ?></h5>
                                        <p class="text-primary fw-bold mb-0"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                                    </div>
                                </div>
                                <div class="text-end d-flex align-items-center gap-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" name="qty[<?php echo (int)$id; ?>]" value="<?php echo (int)$item['qty']; ?>" class="form-control text-center rounded-pill bg-light border-0 cart-qty-input" data-product-id="<?php echo (int)$id; ?>" style="width: 60px; height: 36px; font-size: 14px;">
                                        <a href="cart.php?remove=<?php echo (int)$id; ?>" class="text-danger p-2" onclick="return confirm('Xóa khỏi giỏ hàng?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <div id="subtotal-<?php echo (int)$id; ?>" class="fw-bold text-dark d-none d-md-block" style="min-width: 120px;">
                                        <?php echo number_format($subtotal, 0, ',', '.'); ?>₫
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-light rounded-4 p-4 p-md-5 sticky-top" style="top: 100px;">
                            <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                            <div class="d-flex justify-content-between mb-3 text-secondary">
                                <span class="small">Tạm tính</span>
                                <span class="cart-total-value fw-bold"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4 border-bottom pb-3 text-secondary">
                                <span class="small">Giao hàng</span>
                                <span class="text-success fw-bold small">Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between mb-5">
                                <h5 class="fw-bold">Tổng cộng</h5>
                                <h4 class="text-primary fw-bold cart-total-value">
                                    <?php echo number_format($total, 0, ',', '.'); ?>₫
                                </h4>
                            </div>
                            <a href="checkout.php" class="btn-primary-apple btn-lg w-100 py-3">Thanh toán ngay</a>
                            <p class="text-center mt-3 mb-0 small text-secondary">An toàn & Bảo mật 100%</p>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
