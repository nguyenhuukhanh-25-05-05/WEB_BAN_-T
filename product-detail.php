<?php 
require_once 'includes/db.php';
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: product.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

$pageTitle = "NHK Mobile | " . $product['name'];
$basePath = "";
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="product-detail-img-wrapper bg-white p-5 rounded-4 shadow-sm text-center">
                        <img src="assets/images/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>" onerror="this.src='https://via.placeholder.com/600x700?text=Phone'">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ps-lg-5">
                        <nav aria-label="breadcrumb" class="mb-4">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="product.php" class="text-decoration-none">Sản phẩm</a></li>
                                <li class="breadcrumb-item active"><?php echo $product['category']; ?></li>
                            </ol>
                        </nav>
                        
                        <h1 class="display-5 fw-bold mb-3"><?php echo $product['name']; ?></h1>
                        <p class="h3 text-primary fw-bold mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                        
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3 text-uppercase small letter-spacing">Mô tả sản phẩm</h6>
                            <p class="text-secondary leading-relaxed">
                                <?php echo nl2br($product['description'] ? $product['description'] : 'Sản phẩm chính hãng với hiệu năng mạnh mẽ.'); ?>
                            </p>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-dark btn-lg rounded-pill py-3 fw-bold text-center text-white">Thêm vào giỏ hàng</a>
                            <a href="#" class="btn btn-outline-dark btn-lg rounded-pill py-3 fw-bold text-center">Mua trả góp 0%</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
