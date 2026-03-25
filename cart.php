<?php
require_once 'includes/db.php';
require_once 'includes/cart_functions.php';
require_once 'includes/auth_functions.php';

// Thực hiện đồng bộ giỏ hàng ngay khi bắt đầu
syncCartWithDatabase($pdo);

/**
 * 1. XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ HÀNG
 */
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

/**
 * 2. XỬ LÝ XÓA SẢN PHẨM KHỎI GIỎ
 */
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    removeFromCartDB($pdo, $id);
    header("Location: cart.php");
    exit;
}

// Cấu hình Header
$pageTitle = "Giỏ hàng | NHK Mobile";
$basePath = "";
include 'includes/header.php';

$total = 0;
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <h1 class="display-5 fw-bold mb-5 italic">Giỏ hàng của bạn.</h1>

            <?php if (empty($cartItems)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-secondary mb-4"></i>
                    <h3>Giỏ hàng đang trống</h3>
                    <p class="text-secondary">Hãy quay lại sắm cho mình một chiếc điện thoại mới nhé!</p>
                    <a href="product.php" class="btn btn-dark rounded-pill px-5 mt-3">Tiếp tục mua sắm</a>
                </div>
            <?php else: ?>
                <div class="row g-5">
                    <div class="col-lg-8">
                        <?php foreach ($cartItems as $id => $item): 
                            $subtotal = $item['price'] * $item['qty'];
                            $total += $subtotal;
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
                                           <input type="number" 
                                                  class="form-control text-center rounded-pill cart-qty-input" 
                                                  data-id="<?php echo $id; ?>" 
                                                  value="<?php echo $item['qty']; ?>" 
                                                  min="1" 
                                                  style="width: 70px;">
                                           <a href="cart.php?remove=<?php echo $id; ?>" class="text-danger" onclick="return confirm('Xóa khỏi giỏ hàng?')"><i class="bi bi-trash"></i></a>
                                      </div>
                                      <div class="mt-2 small fw-bold">Thành tiền: <span class="subtotal-<?php echo $id; ?>"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span></div>
                                 </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-light rounded-5 p-5 position-sticky" style="top: 100px;">
                            <h4 class="fw-bold mb-4">Tổng cộng</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Tạm tính</span>
                                <span class="cart-total-display"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                                <span class="text-secondary">Giao hàng</span>
                                <span class="text-success fw-bold">Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between mb-5">
                                <h4 class="fw-bold">Tổng tiền</h4>
                                <h4 class="fw-bold text-primary cart-total-display"><?php echo number_format($total, 0, ',', '.'); ?>₫</h4>
                            </div>
                            <a href="checkout.php" class="btn btn-dark btn-lg w-100 rounded-pill py-3 fw-bold">Tiến hành đặt hàng</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.querySelectorAll('.cart-qty-input').forEach(input => {
            input.addEventListener('change', async function() {
                const productId = this.getAttribute('data-id');
                const newQty = parseInt(this.value);

                if (newQty < 1) {
                    if (confirm('Bạn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        window.location.href = 'cart.php?remove=' + productId;
                    } else {
                        this.value = 1;
                    }
                    return;
                }

                try {
                    const response = await fetch('api/update_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ product_id: productId, qty: newQty })
                    });

                    const data = await response.json();
                    if (data.success) {
                        document.querySelector('.subtotal-' + productId).innerText = data.subtotal;
                        document.querySelectorAll('.cart-total-display').forEach(el => el.innerText = data.total);
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                } catch (error) {
                    console.error('Update cart error:', error);
                }
            });
        });
    </script>

<?php include 'includes/footer.php'; ?>
