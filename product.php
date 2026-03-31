<?php
// Bắt đầu phiên làm việc để quản lý giỏ hàng
session_start();

// Nhúng file kết nối cơ sở dữ liệu Postgres
require_once 'includes/db.php';

// Xử lý tìm kiếm, lọc danh mục và sắp xếp
$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['q']) ? $_GET['q'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Lấy danh sách tất cả các hãng (Categories) từ DB để làm bộ lọc động
$stmtCats = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = $stmtCats->fetchAll(PDO::FETCH_COLUMN);

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

// Phân loại sắp xếp
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

// Chuẩn bị và thực thi truy vấn an toàn (tránh SQL Injection)
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Cấu hình thông tin trang
$pageTitle = $search ? "Kết quả tìm kiếm: " . e($search) : ($category ? "Điện thoại " . e($category) : "Tất cả điện thoại");
$basePath = "";

include 'includes/header.php';
?>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.btn-premium-glass { 
    background: rgba(255,255,255,0.8); 
    border: 1px solid rgba(0,0,0,0.1); 
    color: #1d1d1f; 
    border-radius: 980px; 
    transition: all 0.3s ease; 
    font-weight: 500; 
    text-decoration: none;
}
.btn-premium-glass:hover { 
    background: #fff; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
    transform: translateY(-2px); 
    color: #1d1d1f;
}
.btn-premium-glass.active, .btn-premium-glass.active:hover { 
    background: var(--apple-blue); 
    color: #ffffff !important; 
    border-color: var(--apple-blue); 
    box-shadow: 0 4px 15px rgba(0, 113, 227, 0.3);
}
.filter-brands-scroll {
    padding: 10px 0;
    margin-bottom: -10px; /* offset padding */
}
</style>

    <main class="bg-premium-light min-vh-100">
        <section class="py-huge mt-5">
            <div class="container px-xl-5">
                <!-- Phần tiêu đề trang -->
                <header class="mb-5 d-flex flex-column gap-4 animate-fade-in">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                        <div>
                            <h1 class="display-3 fw-800 mb-0 tracking-tight">
                                <?php echo $search ? "Kết quả cho <span class='text-primary'>'" . e($search) . "'</span>" : ($category ? e($category) : "Tất cả sản phẩm."); ?>
                            </h1>
                            <p class="text-secondary h5 fw-light mt-3">Tìm thấy <?php echo count($products); ?> siêu phẩm công nghệ.</p>
                        </div>
                        
                        <!-- Bộ chọn sắp xếp -->
                        <div class="sort-wrapper">
                             <form action="product.php" method="GET" id="sortForm" class="d-flex align-items-center gap-2">
                                  <?php if($category): ?><input type="hidden" name="category" value="<?php echo e($category); ?>"><?php endif; ?>
                                  <?php if($search): ?><input type="hidden" name="q" value="<?php echo e($search); ?>"><?php endif; ?>
                                  <span class="text-secondary small fw-bold text-uppercase">Sắp xếp:</span>
                                  <select name="sort" class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-3 py-2 cursor-pointer" onchange="this.form.submit()">
                                       <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                       <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                                       <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                                  </select>
                             </form>
                        </div>
                    </div>

                    <!-- Bộ lọc hãng động -->
                    <div class="filter-brands-scroll d-flex gap-2 overflow-auto pb-2 scrollbar-hide">
                        <a href="product.php" class="btn btn-premium-glass <?php echo !$category ? 'active' : ''; ?> px-4 text-nowrap">Tất cả</a>
                        <?php foreach($categories as $cat): ?>
                             <a href="product.php?category=<?php echo urlencode($cat); ?>" 
                                class="btn btn-premium-glass <?php echo $category == $cat ? 'active' : ''; ?> px-4 text-nowrap">
                                <?php echo e($cat); ?>
                             </a>
                        <?php endforeach; ?>
                    </div>
                </header>

                <!-- Danh sách sản phẩm V2.0 -->
                <div class="row g-4 pt-4">
                    <?php if (empty($products)): ?>
                        <div class="col-12 text-center py-5">
                            <div class="glass-panel p-5 rounded-max border-dashed">
                                <i class="bi bi-search display-1 mb-4 opacity-10"></i>
                                <p class="text-secondary h5">Rất tiếc, không tìm thấy sản phẩm nào khớp với yêu cầu của bạn.</p>
                                <a href="product.php" class="btn-apple rounded-pill px-5 py-2 mt-4 shadow-lg">Quay lại cửa hàng</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $index => $p): ?>
                        <div class="col-6 col-md-4 col-lg-3 animate-reveal" style="animation-delay: <?php echo $index * 0.05; ?>s">
                            <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="text-decoration-none">
                                <div class="card-glass-product h-100">
                                    <div class="img-wrapper-premium">
                                        <img src="assets/images/<?php echo e($p['image']); ?>" class="img-fluid" alt="<?php echo e($p['name']); ?>" onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                                    </div>
                                    <div class="p-4 pt-2">
                                        <span class="category-tag mb-1 d-inline-block"><?php echo e($p['category']); ?></span>
                                        <h6 class="fw-bold text-dark mb-2 text-truncate"><?php echo e($p['name']); ?></h6>
                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <span class="price-premium"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</span>
                                            <div class="btn-buy-mini btn-add-to-cart-ajax" data-product-id="<?php echo e($p['id']); ?>">
                                                <i class="bi bi-plus-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

<?php 
// Nhúng phần chân trang (Footer)
include 'includes/footer.php'; 
?>
