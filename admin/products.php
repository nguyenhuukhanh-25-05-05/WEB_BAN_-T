<?php
require_once 'admin_auth.php';

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
    if (!verify_csrf_token()) {
        die("Yêu cầu không hợp lệ (CSRF Token mismatch)");
    }
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image = $_POST['image'] ? $_POST['image'] : "placeholder.png"; 

    if ($id) {
        // Nếu có ID -> CẬP NHẬT sản phẩm hiện tại
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, image = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $description, $image, $id]);
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

// B.1 XÓA NHIỀU SẢN PHẨM (Bulk Delete)
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_ids'])) {
    if (!verify_csrf_token()) {
        die("Yêu cầu không hợp lệ (CSRF Token mismatch)");
    }
    $ids = $_POST['selected_ids'];
    // Chuyển mảng IDs thành chuỗi để dùng trong câu lệnh SQL IN (?, ?, ...)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    header("Location: products.php?msg=deleted");
    exit;
}

// C. TOGGLE SẢN PHẨM NỔI BẬT
if (isset($_GET['toggle_featured'])) {
    $id = $_GET['toggle_featured'];
    $stmt = $pdo->prepare("UPDATE products SET is_featured = NOT is_featured WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?msg=success");
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

    <!-- MOBILE HEADER -->
    <div class="mobile-header d-lg-none">
        <button class="btn btn-light border-0 me-3" id="sidebarToggle">
            <i class="bi bi-list fs-3"></i>
        </button>
        <img src="../assets/images/logo-k.svg" height="15" alt="Logo">
    </div>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR QUẢN TRỊ -->
    <aside class="sidebar text-white" id="sidebarMenu">
        <div class="mb-5 px-3 d-flex justify-content-between align-items-center">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
             <button class="btn btn-link text-white d-lg-none p-0" id="sidebarClose">
                <i class="bi bi-x-lg fs-4"></i>
             </button>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin active"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin"><i class="bi bi-people"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-check"></i> Bảo hành IMEI</a>
            <a href="news.php" class="nav-link-admin"><i class="bi bi-newspaper"></i> Tin tức & Tech</a>
            
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
            <div class="d-flex gap-2">
                <!-- Nút Xóa nhiều (ẩn mặc định, hiện khi chọn checkbox) -->
                <button type="submit" form="bulkDeleteForm" name="bulk_delete" id="bulkDeleteBtn" class="btn btn-outline-danger shadow-sm px-4 py-2 rounded-3 d-none" onclick="return confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')">
                    <i class="bi bi-trash me-2"></i> Xóa mục đã chọn
                </button>
                <!-- Nút bật Modal thêm mới -->
                <button class="btn btn-primary shadow-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="bi bi-plus-lg me-2"></i> Thêm máy mới
                </button>
            </div>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4">
            <!-- Hiển thị thông báo thành công nếu có -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_GET['msg'] == 'success' ? 'Dữ liệu đã được cập nhật thành công!' : ($_GET['msg'] == 'deleted' ? 'Sản phẩm đã được xóa khỏi hệ thống!' : 'Thao tác thành công!'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="bulkDeleteForm" action="products.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="small text-uppercase text-secondary">
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
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
                                    <input type="checkbox" name="selected_ids[]" value="<?php echo e($p['id']); ?>" class="form-check-input select-item">
                                </td>
                                <td>
                                    <img src="../assets/images/<?php echo e($p['image']); ?>" class="product-img-admin rounded-3" onerror="this.src='https://placehold.co/60'">
                                </td>
                            <td>
                                <div class="fw-bold d-flex align-items-center gap-2">
                                    <?php echo e($p['name']); ?>
                                    <?php if($p['is_featured']): ?>
                                        <i class="bi bi-star-fill text-warning" title="Nổi bật (Hiện trên trang chủ)"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border fw-normal"><?php echo e($p['category']); ?></span></td>
                            <td class="fw-bold text-primary"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</td>
                            <td><span class="text-secondary"><?php echo e($p['stock']); ?> chiếc</span></td>
                            <td class="text-end">
                                <!-- Nút Đánh dấu / Gỡ bỏ Nổi bật -->
                                <a href="products.php?toggle_featured=<?php echo e($p['id']); ?>" class="btn btn-sm border p-2 <?php echo $p['is_featured'] ? 'btn-warning text-white' : 'btn-light text-warning'; ?>" title="Ghim lên đầu trang chủ">
                                    <i class="bi bi-star<?php echo $p['is_featured'] ? '-fill' : ''; ?>"></i>
                                </a>
                                <!-- Nút Sửa: Truyền ID qua biến GET 'edit' -->
                                <a href="products.php?edit=<?php echo e($p['id']); ?>" class="btn btn-sm btn-light border p-2 ms-1"><i class="bi bi-pencil text-primary"></i></a>
                                <!-- Nút Xóa: Truyền ID qua biến GET 'delete' kèm confirm -->
                                <a href="products.php?delete=<?php echo e($p['id']); ?>" class="btn btn-sm btn-light border p-2 text-danger ms-1" onclick="return confirm('Toàn bộ thông tin máy sẽ bị xóa, bạn chắc chứ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </form>
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
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                        
                        <!-- Input ẩn ID: Dùng để phân biệt Thêm (trống) hay Sửa (có ID) -->
                        <input type="hidden" name="id" value="<?php echo $editProduct ? e($editProduct['id']) : ''; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Tên điện thoại</label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo $editProduct ? e($editProduct['name']) : ''; ?>" placeholder="VD: iPhone 15 Pro Max" required>
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
                                <input type="number" name="price" class="form-control rounded-3" value="<?php echo $editProduct ? e($editProduct['price']) : ''; ?>" placeholder="VD: 30000000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Số lượng tồn kho</label>
                                <input type="number" name="stock" class="form-control rounded-3" value="<?php echo $editProduct ? e($editProduct['stock']) : ''; ?>" placeholder="VD: 10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Tên tệp hình ảnh</label>
                                <input type="text" name="image" class="form-control rounded-3" value="<?php echo $editProduct ? e($editProduct['image']) : ''; ?>" placeholder="VD: iphone15.png">
                                <div class="form-text x-small">Tên tệp trong folder assets/images/</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Mô tả ngắn gọn</label>
                                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Nhập cấu hình sơ bộ..."><?php echo $editProduct ? e($editProduct['description']) : ''; ?></textarea>
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
    <script>
        // Sidebar Toggle for Mobile
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function toggleSidebar() {
            sidebarMenu.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.classList.toggle('overflow-hidden');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);

        // Xử lý chọn tất cả checkbox
        const selectAll = document.getElementById('selectAll');
        const selectItems = document.querySelectorAll('.select-item');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function toggleBulkDeleteBtn() {
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            if (checkedCount > 0) {
                bulkDeleteBtn.classList.remove('d-none');
            } else {
                bulkDeleteBtn.classList.add('d-none');
            }
        }

        selectAll.addEventListener('change', function() {
            selectItems.forEach(item => {
                item.checked = this.checked;
            });
            toggleBulkDeleteBtn();
        });

        selectItems.forEach(item => {
            item.addEventListener('change', function() {
                // Nếu có 1 item không được chọn thì bỏ check selectAll
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    // Nếu tất cả item đều được chọn thì check selectAll
                    const allChecked = Array.from(selectItems).every(i => i.checked);
                    selectAll.checked = allChecked;
                }
                toggleBulkDeleteBtn();
            });
        });
    </script>
</body>
</html>
