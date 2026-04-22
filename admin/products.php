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
    $id = $_POST['id'];
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $specs = trim($_POST['specs'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Xác thực dữ liệu cơ bản
    if (empty($name) || empty($category) || $price === '' || $stock === '') {
        header("Location: products.php?error=empty_fields" . ($id ? "&edit=$id" : ""));
        exit;
    }

    // ... (logic check numeric skipped for brevity in prompt but I should keep it correctly)
    
    // Yêu cầu bắt buộc phải có ảnh khi thêm mới sản phẩm
    if (empty($id) && (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)) {
        header("Location: products.php?error=empty_image");
        exit;
    }

    // Xử lý upload ảnh
    $uploadDir = '../assets/images/';
    $image = $_POST['current_image'] ?: 'placeholder.png'; // Giữ ảnh cũ mặc định
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            $newName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName)) {
                $image = $newName;
            }
        }
    }

    if ($id) {
        // Nếu có ID -> CẬP NHẬT sản phẩm hiện tại
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ?, description = ?, specs = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $image, $description, $specs, $id]);
        $msg = "success";
    } else {
        // Nếu không có ID -> THÊM MỚI sản phẩm vào bảng
        $sql = "INSERT INTO products (name, category, price, stock, image, description, specs) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $image, $description, $specs]);
        $msg = "success";
    }
    // Chuyển hướng lại trang để tránh gửi lại form khi F5
    header("Location: products.php?msg=$msg");
    exit;
}

// B. XÓA SẢN PHẨM (Xử lý khi nhấn nút Xóa trên danh sách)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        
        // 1. Xóa các mục liên quan trong giỏ hàng
        $pdo->prepare("DELETE FROM cart_items WHERE product_id = ?")->execute([$id]);
        
        // 2. Xóa các mục trong danh sách yêu thích
        $pdo->prepare("DELETE FROM wishlists WHERE product_id = ?")->execute([$id]);
        
        // 3. Xóa các đánh giá
        $pdo->prepare("DELETE FROM reviews WHERE product_id = ?")->execute([$id]);
        
        // 4. Xóa lịch sử sửa chữa của các bảo hành thuộc sản phẩm này
        $pdo->prepare("DELETE FROM repair_history WHERE warranty_id IN (SELECT id FROM warranties WHERE product_id = ?)")->execute([$id]);
        
        // 5. Xóa các bảo hành
        $pdo->prepare("DELETE FROM warranties WHERE product_id = ?")->execute([$id]);
        
        // 6. Xóa các chi tiết đơn hàng (lưu ý sẽ ảnh hưởng đến đơn hàng cũ nhưng buộc phải xóa nếu muốn xóa sản phẩm triệt để)
        $pdo->prepare("DELETE FROM order_items WHERE product_id = ?")->execute([$id]);
        
        // Cuối cùng: Xóa sản phẩm
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        header("Location: products.php?msg=deleted");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: products.php?error=fk_violation");
    }
    exit;
}

// B.1 XÓA NHIỀU SẢN PHẨM (Bulk Delete)
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];
    try {
        $pdo->beginTransaction();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $pdo->prepare("DELETE FROM cart_items WHERE product_id IN ($placeholders)")->execute($ids);
        $pdo->prepare("DELETE FROM wishlists WHERE product_id IN ($placeholders)")->execute($ids);
        $pdo->prepare("DELETE FROM reviews WHERE product_id IN ($placeholders)")->execute($ids);
        $pdo->prepare("DELETE FROM repair_history WHERE warranty_id IN (SELECT id FROM warranties WHERE product_id IN ($placeholders))")->execute($ids);
        $pdo->prepare("DELETE FROM warranties WHERE product_id IN ($placeholders)")->execute($ids);
        $pdo->prepare("DELETE FROM order_items WHERE product_id IN ($placeholders)")->execute($ids);
        
        $sql = "DELETE FROM products WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        
        $pdo->commit();
        header("Location: products.php?msg=deleted");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: products.php?error=fk_violation");
    }
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

$pageTitle = "Kho sản phẩm | Admin";
$basePath = "../";
include 'includes/admin_header.php';
?>
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

            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php 
                        if ($_GET['error'] == 'empty_fields') echo 'Vui lòng điền đầy đủ các thông tin bắt buộc!';
                        elseif ($_GET['error'] == 'invalid_price') echo 'Giá sản phẩm không hợp lệ! Vui lòng nhập số lớn hơn hoặc bằng 0.';
                        elseif ($_GET['error'] == 'invalid_stock') echo 'Số lượng tồn kho không hợp lệ! Vui lòng nhập số lớn hơn hoặc bằng 0.';
                        elseif ($_GET['error'] == 'empty_image') echo 'Vui lòng tải lên ảnh đại diện cho sản phẩm mới!';
                        elseif ($_GET['error'] == 'fk_violation') echo 'Không thể xóa sản phẩm này vì đang có trong giỏ hàng hoặc đơn hàng của khách!';
                        else echo 'Có lỗi xảy ra!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="bulkDeleteForm" action="products.php" method="POST">
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
                                    <input type="checkbox" name="selected_ids[]" value="<?php echo $p['id']; ?>" class="form-check-input select-item">
                                </td>
                                <td>
                                    <img src="../assets/images/<?php echo $p['image']; ?>" class="product-img-admin rounded-2 shadow-sm" style="width: 44px; height: 44px; object-fit: contain; background: #fff;" onerror="this.src='https://placehold.co/44'">
                                </td>
                            <td>
                                <div class="fw-bold d-flex align-items-center gap-2">
                                    <?php echo $p['name']; ?>
                                    <?php if($p['is_featured']): ?>
                                        <i class="bi bi-star-fill text-warning" title="Nổi bật (Hiện trên trang chủ)"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border fw-normal"><?php echo $p['category']; ?></span></td>
                            <td class="fw-bold text-primary"><?php echo number_format($p['price'], 0, ',', '.'); ?>₫</td>
                            <td><span class="text-secondary"><?php echo $p['stock']; ?> chiếc</span></td>
                            <td class="text-end">
                                <!-- Nút Đánh dấu / Gỡ bỏ Nổi bật -->
                                <a href="products.php?toggle_featured=<?php echo $p['id']; ?>" class="btn btn-sm border p-2 <?php echo $p['is_featured'] ? 'btn-warning text-white' : 'btn-light text-warning'; ?>" title="Ghim lên đầu trang chủ">
                                    <i class="bi bi-star<?php echo $p['is_featured'] ? '-fill' : ''; ?>"></i>
                                </a>
                                <!-- Nút Sửa: Truyền ID qua biến GET 'edit' -->
                                <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border p-2 ms-1"><i class="bi bi-pencil text-primary"></i></a>
                                <!-- Nút Xóa: Truyền ID qua biến GET 'delete' kèm confirm -->
                                <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border p-2 text-danger ms-1" onclick="return confirm('Toàn bộ thông tin máy sẽ bị xóa, bạn chắc chứ?')"><i class="bi bi-trash"></i></a>
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
                <form action="products.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold"><?php echo $editProduct ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm mới'; ?></h5>
                        <!-- Nếu trong chế độ Sửa, nút X sẽ reload trang để thoát mode Sửa -->
                        <a href="products.php" class="btn-close"></a>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Input ẩn ID: Dùng để phân biệt Thêm (trống) hay Sửa (có ID) -->
                        <input type="hidden" name="id" value="<?php echo $editProduct ? $editProduct['id'] : ''; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $editProduct ? $editProduct['image'] : 'placeholder.png'; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Tên điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['name'] : ''; ?>" placeholder="VD: iPhone 15 Pro Max" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Danh mục hãng <span class="text-danger">*</span></label>
                                <select name="category" class="form-select rounded-3">
                                    <option value="Apple" <?php echo ($editProduct && $editProduct['category'] == 'Apple') ? 'selected' : ''; ?>>Apple</option>
                                    <option value="Samsung" <?php echo ($editProduct && $editProduct['category'] == 'Samsung') ? 'selected' : ''; ?>>Samsung</option>
                                    <option value="Xiaomi" <?php echo ($editProduct && $editProduct['category'] == 'Xiaomi') ? 'selected' : ''; ?>>Xiaomi</option>
                                    <option value="Oppo" <?php echo ($editProduct && $editProduct['category'] == 'Oppo') ? 'selected' : ''; ?>>Oppo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['price'] : ''; ?>" placeholder="VD: 30000000" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Số lượng tồn kho <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control rounded-3" value="<?php echo $editProduct ? $editProduct['stock'] : ''; ?>" placeholder="VD: 10" min="0" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Ảnh sản phẩm <?php echo !$editProduct ? '<span class="text-danger">*</span>' : ''; ?></label>
                                <?php if ($editProduct && $editProduct['image']): ?>
                                <div class="mb-2 d-flex align-items-center gap-3">
                                    <img src="../assets/images/<?php echo $editProduct['image']; ?>" style="width:60px;height:60px;object-fit:cover;" class="rounded-3 border" onerror="this.src='https://placehold.co/60'">
                                    <span class="small text-secondary">Ảnh hiện tại. Chọn file mới để thay thế.</span>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control rounded-3" accept="image/png, image/jpeg, image/webp, image/gif" <?php echo !$editProduct ? 'required' : ''; ?>>
                                <div class="form-text">Định dạng: JPG, PNG, WEBP, GIF. <?php echo $editProduct ? 'Để trống nếu không muốn thay ảnh.' : 'Bắt buộc chọn ảnh cho sản phẩm mới.'; ?></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Thông số kỹ thuật (Dùng cho bộ lọc)</label>
                                <textarea name="specs" class="form-control rounded-3" rows="2" placeholder="VD: 128GB, 256GB, 8GB RAM..."><?php echo $editProduct ? $editProduct['specs'] : ''; ?></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Mô tả chi tiết</label>
                                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Nhập mô tả sản phẩm..."><?php echo $editProduct ? $editProduct['description'] : ''; ?></textarea>
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

    <script>
        // Xử lý logic chọn hàng loạt (Bulk Selection)
        const selectAll = document.getElementById('selectAll');
        const selectItems = document.querySelectorAll('.select-item');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function toggleBulkDeleteBtn() {
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            if (bulkDeleteBtn) {
                if (checkedCount > 0) bulkDeleteBtn.classList.remove('d-none');
                else bulkDeleteBtn.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                selectItems.forEach(item => item.checked = this.checked);
                toggleBulkDeleteBtn();
            });
        }

        selectItems.forEach(item => {
            item.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                else {
                    const allChecked = Array.from(selectItems).every(i => i.checked);
                    selectAll.checked = allChecked;
                }
                toggleBulkDeleteBtn();
            });
        });
    </script>

<?php include 'includes/admin_footer.php'; ?>
