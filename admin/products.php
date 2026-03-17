<?php
// Khởi tạo phiên làm việc (Session)
session_start();

/**
 * KIỂM TRA ĐĂNG NHẬP
 * Nếu chưa đăng nhập (Session admin_logged_in không tồn tại),
 * thì đá người dùng về trang login.php
 */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Nhúng file kết nối cơ sở dữ liệu Postgres
require_once '../includes/db.php';

// Tiêu đề trang quản trị
$pageTitle = "Quản lý sản phẩm";
$basePath = "../";

/**
 * 1. XỬ LÝ LOGIC CRUD (THÊM / SỬA / XÓA)
 * Logic này xử lý các yêu cầu gửi từ Form PHP bằng phương thức POST và GET.
 */

// A. THÊM HOẶC CẬP NHẬT SẢN PHẨM (Xử lý khi nhấn nút Lưu)
if (isset($_POST['save_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image = "placeholder.png"; // Tạm thời dùng hình mặc định, có thể nâng cấp upload file sau

    if ($id) {
        // Nếu có ID -> CẬP NHẬT sản phẩm hiện tại
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $description, $id]);
        $msg = "success";
    } else {
        // Nếu không có ID -> THÊM MỚI sản phẩn vào bảng
        $sql = "INSERT INTO products (name, category, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $image, $description]);
        $msg = "success";
    }
    // Chuyển hướng lại trang để tránh gửi lại form khi F5
    header("Location: products.php?msg=$msg");
    exit;
}

// B. XÓA SẢN PHẨM (Xử lý khi nhấn nút Xóa trên danh sách)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?msg=deleted");
    exit;
}

/**
 * 2. LẤY DỮ LIỆU HIỂN THỊ
 */

// Lấy toàn bộ danh sách sản phẩm để hiện ra bảng (sắp xếp mới nhất lên đầu)
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();

// KIỂM TRA NẾU ĐANG TRONG CHẾ ĐỘ SỬA (Lấy data sản phẩm cũ đổ vào Modal)
$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Style riêng cho Admin -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <!-- THANH SIDEBAR TRÁI -->
    <aside class="sidebar text-white d-none d-lg-block">
        <div class="mb-5 px-3">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin active"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            
            <div class="mt-5 pt-5 border-top border-secondary mx-3">
                 <a href="../index.php" class="nav-link-admin text-info ps-0 mb-2"><i class="bi bi-box-arrow-left"></i> Xem Website</a>
                 <a href="logout.php" class="nav-link-admin text-danger ps-0 small"><i class="bi bi-power"></i> Đăng xuất</a>
            </div>
        </nav>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Kho sản phẩm</h2>
                 <p class="text-secondary small mb-0">Quản lý kho hàng bằng PHP PDO & PostgreSQL.</p>
            </div>
            <!-- Nút bật Modal thêm mới -->
            <button class="btn btn-primary shadow-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="bi bi-plus-lg me-2"></i> Thêm máy mới
            </button>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4">
            <!-- Hiển thị thông báo thành công nếu có -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_GET['msg'] == 'success' ? 'Dữ liệu đã được cập nhật thành công!' : 'Sản phẩm đã được xóa khỏi hệ thống!'; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Hình ảnh</th>
                            <th>Tên máy</th>
                            <th>Hãng</th>
                            <th>Giá niêm yết</th>
                            <th>Tồn kho</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp PHP Duyệt mảng sản phẩm lấy từ DB -->
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/<?php echo $p['image']; ?>" class="product-img-admin rounded-3" onerror="this.src='https://placehold.co/60'">
                            </td>
                            <td><div class="fw-bold"><?php echo $p['name']; ?></div></td>
                            <td><span class="badge bg-light text-dark border fw-normal"><?php echo $p['category']; ?></span></td>
                            <td class="fw-bold text-primary"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</td>
                            <td><span class="text-secondary"><?php echo $p['stock']; ?> chiếc</span></td>
                            <td class="text-end">
                                <!-- Nút Sửa: Truyền ID qua biến GET 'edit' -->
                                <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border p-2"><i class="bi bi-pencil text-primary"></i></a>
                                <!-- Nút Xóa: Truyền ID qua biến GET 'delete' kèm confirm -->
                                <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border p-2 text-danger ms-1" onclick="return confirm('Toàn bộ thông tin máy sẽ bị xóa, bạn chắc chứ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL THÊM / SỬA SẢN PHẨM -->
    <!-- Modal này sẽ tự bật lên nếu người dùng nhấn SỬA (nhờ logic PHP phía dưới) -->
    <div class="modal fade <?php echo $editProduct ? 'show' : ''; ?>" id="productModal" tabindex="-1" <?php echo $editProduct ? 'style="display: block; background: rgba(0,0,0,0.5)"' : ''; ?>>
        <div class="modal-dialog modal-lg border-0">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <!-- FORM PHP: Gửi dữ liệu về chính file products.php bằng phương thức POST -->
                <form action="products.php" method="POST">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold"><?php echo $editProduct ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm mới'; ?></h5>
                        <!-- Nếu trong chế độ Sửa, nút X sẽ reload trang để thoát mode Sửa -->
                        <a href="products.php" class="btn-close"></a>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Input ẩn ID: Dùng để phân biệt Thêm (trống) hay Sửa (có ID) -->
                        <input type="hidden" name="id" value="<?php echo $editProduct ? $editProduct['id'] : ''; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Tên điện thoại</label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['name'] : ''; ?>" placeholder="VD: iPhone 15 Pro Max" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Danh mục hãng</label>
                                <select name="category" class="form-select rounded-3">
                                    <option value="Apple" <?php echo ($editProduct && $editProduct['category'] == 'Apple') ? 'selected' : ''; ?>>Apple</option>
                                    <option value="Samsung" <?php echo ($editProduct && $editProduct['category'] == 'Samsung') ? 'selected' : ''; ?>>Samsung</option>
                                    <option value="Xiaomi" <?php echo ($editProduct && $editProduct['category'] == 'Xiaomi') ? 'selected' : ''; ?>>Xiaomi</option>
                                    <option value="Oppo" <?php echo ($editProduct && $editProduct['category'] == 'Oppo') ? 'selected' : ''; ?>>Oppo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Giá bán (VNĐ)</label>
                                <input type="number" name="price" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['price'] : ''; ?>" placeholder="VD: 30000000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Số lượng tồn kho</label>
                                <input type="number" name="stock" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['stock'] : ''; ?>" placeholder="VD: 10" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Mô tả ngắn gọn</label>
                                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Nhập cấu hình sơ bộ..."><?php echo $editProduct ? $editProduct['description'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <a href="products.php" class="btn btn-light rounded-3 px-4">Đóng</a>
                        <!-- Nút Submit của Form PHP -->
                        <button type="submit" name="save_product" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                            <i class="bi bi-save me-2"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
