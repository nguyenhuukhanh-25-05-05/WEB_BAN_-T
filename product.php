<?php
session_start();
require_once 'includes/db.php';

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['q']) ? $_GET['q'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$stmtCats = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = $stmtCats->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $sql .= " AND name ILIKE ?"; 
    $params[] = "%$search%";
}

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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = $search ? "Tìm kiếm: " . e($search) : ($category ? "Điện thoại " . e($category) : "Tất cả điện thoại");
$basePath = "";

include 'includes/header.php';
?>

<main class="min-vh-100 bg-premium-light">
    <section class="section-padding mt-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="mb-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4 mb-5 animate-reveal">
                    <div>
                        <h1 class="display-3 fw-800 text-gradient mb-0">
                            <?php echo $search ? "Kết quả cho '" . e($search) . "'" : ($category ? e($category) : "Tất cả sản phẩm"); ?>
                        </h1>
                        <p class="h5 text-secondary fw-light m-0 mt-3">Tìm thấy <?php echo count($products); ?> siêu phẩm công nghệ.</p>
                    </div>
                    
                    <!-- Sort -->
                    <div class="sort-wrapper">
                        <form action="product.php" method="GET" class="d-flex align-items-center gap-3">
                            <?php if($category): ?><input type="hidden" name="category" value="<?php echo e($category); ?>"><?php endif; ?>
                            <?php if($search): ?><input type="hidden" name="q" value="<?php echo e($search); ?>"><?php endif; ?>
                            <span class="text-secondary small fw-bold tracking-widest text-uppercase">Sắp xếp:</span>
                            <select name="sort" class="form-select border-0 bg-white rounded-pill px-4 py-2 shadow-sm" style="min-width: 180px;" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="d-flex gap-3 overflow-auto pb-4 animate-reveal" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <style>.brand-pill::-webkit-scrollbar { display: none; }</style>
                    <a href="product.php" class="btn rounded-pill px-4 py-2 border shadow-sm transition-all <?php echo !$category ? 'bg-dark text-white border-dark' : 'bg-white text-dark'; ?>">Tất cả</a>
                    <?php foreach($categories as $cat): ?>
                        <a href="product.php?category=<?php echo urlencode($cat); ?>" 
                            class="btn rounded-pill px-4 py-2 border shadow-sm transition-all <?php echo $category == $cat ? 'bg-dark text-white border-dark' : 'bg-white text-dark'; ?>">
                            <?php echo e($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center py-5 animate-reveal">
                        <i class="bi bi-search display-1 mb-4 opacity-10"></i>
                        <p class="text-secondary h5 fw-light">Rất tiếc, không tìm thấy sản phẩm nào khớp với yêu cầu của bạn.</p>
                        <a href="product.php" class="btn btn-primary rounded-pill px-5 py-3 mt-4">Quay lại cửa hàng</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                    <div class="product-card-modern animate-reveal">
                        <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="p-img-box">
                            <img src="assets/images/<?php echo e($p['image']); ?>" alt="<?php echo e($p['name']); ?>" onerror="this.src='https://placehold.co/300x400/f5f5f7/1d1d1f?text=Phone'">
                        </a>
                        <div class="p-brand"><?php echo e($p['category']); ?></div>
                        <h3 class="p-title"><?php echo e($p['name']); ?></h3>
                        <div class="p-price"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</div>
                        <div class="p-actions">
                            <a href="#" class="btn-p-buy btn-add-to-cart-ajax" data-product-id="<?php echo e($p['id']); ?>">Mua</a>
                            <a href="product-detail.php?id=<?php echo e($p['id']); ?>" class="btn-p-view">Chi tiết</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
