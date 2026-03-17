<?php
// Bắt đầu phiên làm việc để quản lý giỏ hàng
session_start();

// Nhúng file kết nối cơ sở dữ liệu Postgres
require_once 'includes/db.php';

// Xử lý tìm kiếm và lọc danh mục
$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['q']) ? $_GET['q'] : null;

// Xây dựng câu lệnh SQL cơ bản
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

// Nếu có lọc theo hãng (Apple, Samsung...)
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Nếu có từ khóa tìm kiếm
if ($search) {
    $sql .= " AND name ILIKE ?"; // ILIKE trong Postgres là tìm kiếm không phân biệt hoa thường
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

// Chuẩn bị và thực thi truy vấn an toàn (tránh SQL Injection)
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Cấu hình thông tin trang
$pageTitle = $search ? "Kết quả tìm kiếm: $search" : ($category ? "Điện thoại $category" : "Tất cả điện thoại");
$basePath = "";

// Nhúng phần đầu trang (Header)
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <!-- Phần tiêu đề trang -->
            <header class="mb-5 d-flex justify-content-between align-items-end">
                <div>
                    <h1 class="display-5 fw-bold mb-0">
                        <?php echo $search ? "Tìm kiếm: '$search'" : ($category ? $category : "Sản phẩm."); ?>
                    </h1>
                    <p class="text-secondary mt-2">Tìm thấy <?php echo count($products); ?> thiết bị phù hợp.</p>
                </div>
                <!-- Bộ lọc nhanh danh mục -->
                <div class="d-none d-md-flex gap-2">
                    <a href="product.php" class="btn btn-sm <?php echo !$category ? 'btn-dark' : 'btn-outline-dark'; ?> rounded-pill px-3">Tất cả</a>
                    <a href="product.php?category=Apple" class="btn btn-sm <?php echo $category == 'Apple' ? 'btn-dark' : 'btn-outline-dark'; ?> rounded-pill px-3">Apple</a>
                    <a href="product.php?category=Samsung" class="btn btn-sm <?php echo $category == 'Samsung' ? 'btn-dark' : 'btn-outline-dark'; ?> rounded-pill px-3">Samsung</a>
                </div>
            </header>

            <!-- Danh sách sản phẩm -->
            <div class="row g-4">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-secondary h5">Không tìm thấy sản phẩm nào khớp với yêu cầu của bạn.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 border-0 product-card-hover bg-white p-3 rounded-4 shadow-sm">
                            <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                                <div class="text-center mb-3 p-3">
                                    <img src="assets/images/<?php echo $p['image']; ?>" class="img-fluid" alt="<?php echo $p['name']; ?>" style="max-height: 180px;" onerror="this.src='https://placehold.co/200x250?text=Phone'">
                                </div>
                                <div class="card-body p-0">
                                    <h6 class="fw-bold text-dark mb-1"><?php echo $p['name']; ?></h6>
                                    <p class="card-text text-secondary small mb-3 text-truncate"><?php echo $p['description']; ?></p>
                                    <p class="text-primary fw-bold mb-0"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</p>
                                    <div class="mt-3">
                                        <!-- Nút thêm vào giỏ hàng truyền ID qua GET -->
                                        <a href="cart.php?add=<?php echo $p['id']; ?>" class="btn btn-dark btn-sm rounded-pill px-4 w-100">Mua ngay</a>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php 
// Nhúng phần chân trang (Footer)
include 'includes/footer.php'; 
?>
