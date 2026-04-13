<?php
// Bắt đầu phiên làm việc
require_once 'admin_auth.php';

// Nhúng file kết nối CSDL Postgres
require_once '../includes/db.php';

/**
 * 1. XỬ LÝ LOGIC (THÊM / SỬA / XÓA BẢO HÀNH)
 */

if (isset($_POST['save_warranty'])) {
    $id = $_POST['id'] ?? null;
    $imei = trim($_POST['imei']);
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'] ? $_POST['order_id'] : null;
    $status = $_POST['status'];
    $expires_at = $_POST['expires_at'];

    try {
        if ($id) {
            // Cập nhật
            $sql = "UPDATE warranties SET imei = ?, product_id = ?, order_id = ?, status = ?, expires_at = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$imei, $product_id, $order_id, $status, $expires_at, $id]);
        } else {
            // Thêm mới
            $sql = "INSERT INTO warranties (imei, product_id, order_id, status, expires_at) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$imei, $product_id, $order_id, $status, $expires_at]);
        }
        header("Location: warranties.php?msg=success");
        exit;
    } catch (PDOException $e) {
        // Lỗi trùng IMEI hoặc khóa ngoại
        if ($e->getCode() == '23505') {
            $error = "Lỗi: Số IMEI này đã được kích hoạt trước đó!";
        } elseif ($e->getCode() == '23503') {
            $error = "Lỗi: Mã đơn hàng (Order ID) không tồn tại trong hệ thống. Vui lòng kiểm tra lại.";
        } else {
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM warranties WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: warranties.php?msg=deleted");
    exit;
}

/**
 * 2. LẤY DỮ LIỆU HIỂN THỊ
 */

// Khởi tạo biến editData mặc định là null (chế độ thêm mới)
$editData = null;

// Nếu URL có ?edit=ID thì lấy dữ liệu bản ghi đó để điền vào form
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmtEdit = $pdo->prepare("SELECT * FROM warranties WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editData = $stmtEdit->fetch() ?: null;
}

// Lấy danh sách bảo hành kèm tên sản phẩm
$sql = "
    SELECT w.*, p.name as product_name 
    FROM warranties w 
    LEFT JOIN products p ON w.product_id = p.id 
    ORDER BY w.created_at DESC
";
$stmt = $pdo->query($sql);
$warranties = $stmt->fetchAll();

// Lấy danh sách sản phẩm để cho vào dropdown
$stmtProd = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
$productsList = $stmtProd->fetchAll();


$pageTitle = "Quản lý Bảo hành | Admin NHK Mobile";
$basePath = "../";
include 'includes/admin_header.php';
?>
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Quản lý Bảo hành (IMEI)</h2>
                 <p class="text-secondary small mb-0">Quản lý mã định danh thiết bị bán ra và thời hạn bảo hành.</p>
            </div>
            <button class="btn btn-primary shadow-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#warrantyModal">
                <i class="bi bi-plus-lg me-2"></i> Kích hoạt IMEI mới
            </button>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Thao tác thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mb-4 border-0 rounded-3"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Mã IMEI</th>
                            <th>Sản phẩm áp dụng</th>
                            <th>Đơn hàng</th>
                            <th>Ngày hết hạn</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($warranties as $w): ?>
                        <tr>
                            <td class="fw-bold fw-monospace text-primary">
                                <i class="bi bi-upc-scan me-1"></i> <?php echo htmlspecialchars($w['imei']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($w['product_name']); ?></td>
                            <td>
                                <?php if($w['order_id']): ?>
                                    <a href="invoice.php?order_id=<?php echo $w['order_id']; ?>" target="_blank" class="text-decoration-none">#ORD-<?php echo $w['order_id']; ?></a>
                                <?php else: ?>
                                    <span class="text-secondary small">Kích hoạt tay</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary">
                                <?php echo date('d/m/Y', strtotime($w['expires_at'])); ?>
                                <?php if(strtotime($w['expires_at']) < time()): ?>
                                    <br><span class="badge bg-danger-subtle text-danger border px-2 mt-1">Hết hạn</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $badge = 'success';
                                    if ($w['status'] == 'Voided' || $w['status'] == 'Từ chối') $badge = 'danger';
                                    if ($w['status'] == 'Expired' || $w['status'] == 'Hết hạn') $badge = 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>-subtle text-dark border fw-normal px-2"><?php echo $w['status']; ?></span>
                            </td>
                            <td class="text-end">
                                <a href="warranties.php?edit=<?php echo $w['id']; ?>" class="btn btn-sm btn-light border p-2"><i class="bi bi-pencil text-primary"></i></a>
                                <a href="warranties.php?delete=<?php echo $w['id']; ?>" class="btn btn-sm btn-light border p-2 text-danger ms-1" onclick="return confirm('Xóa trạm bảo hành IMEI này?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($warranties) === 0): ?>
                        <tr><td colspan="6" class="text-center py-4 text-secondary">Chưa có mã IMEI nào được kích hoạt.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL THÔNG TIN BẢO HÀNH -->
    <div class="modal fade <?php echo $editData ? 'show' : ''; ?>" id="warrantyModal" tabindex="-1" <?php echo $editData ? 'style="display: block; background: rgba(0,0,0,0.5)"' : ''; ?>>
        <div class="modal-dialog border-0">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form action="warranties.php" method="POST">
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold mb-0"><?php echo $editData ? 'Sửa thông tin bảo hành' : 'Kích hoạt IMEI mới'; ?></h5>
                        <a href="warranties.php" class="btn-close"></a>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số IMEI (Định danh 15 chữ số) *</label>
                            <input type="text" name="imei" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($editData['imei'] ?? ''); ?>" required placeholder="VD: 351234567890123">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sản phẩm áp dụng *</label>
                            <select name="product_id" class="form-select bg-light border-0" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach($productsList as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo (isset($editData['product_id']) && $editData['product_id'] == $p['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mã Hóa đơn / Order ID (Tùy chọn)</label>
                            <input type="number" name="order_id" class="form-control bg-light border-0" value="<?php echo $editData['order_id'] ?? ''; ?>" placeholder="Nhập ID đơn hàng (nếu bán qua hệ thống)">
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Trạng thái *</label>
                                <select name="status" class="form-select bg-light border-0">
                                    <option value="Active" <?php echo (isset($editData['status']) && $editData['status'] == 'Active') ? 'selected' : ''; ?>>Hợp lệ (Active)</option>
                                    <option value="Expired" <?php echo (isset($editData['status']) && $editData['status'] == 'Expired') ? 'selected' : ''; ?>>Hết hạn</option>
                                    <option value="Từ chối" <?php echo (isset($editData['status']) && $editData['status'] == 'Từ chối') ? 'selected' : ''; ?>>Từ chối bảo hành (Rơi vỡ)</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Ngày hết hạn *</label>
                                <input type="date" name="expires_at" class="form-control bg-light border-0" value="<?php echo isset($editData['expires_at']) ? date('Y-m-d', strtotime($editData['expires_at'])) : date('Y-m-d', strtotime('+12 months')); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <a href="warranties.php" class="btn btn-light px-4 rounded-pill">Hủy bỏ</a>
                        <button type="submit" name="save_warranty" class="btn btn-primary px-4 rounded-pill shadow-sm">Lưu dữ liệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        <?php if ($editData): ?>
        // Mở sẵn Modal nền nếu có biến GET edit
        var myModal = new bootstrap.Modal(document.getElementById('warrantyModal'), {
            keyboard: false, backdrop: 'static'
        });
        myModal.show();
        <?php endif; ?>
    </script>

<?php include 'includes/admin_footer.php'; ?>
