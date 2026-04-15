<?php
// Bắt đầu phiên làm việc
require_once 'admin_auth.php';

// Nhúng file kết nối CSDL Postgres
require_once '../includes/db.php';

/**
 * 1. XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
 * Sử dụng Form với phương thức POST để thay đổi status (Completed / Cancelled)
 */
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Câu lệnh SQL cập nhật trạng thái
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    // Lưu thông báo vào URL và reload trang
    header("Location: orders.php?msg=updated");
    exit;
}

/**
 * 2. TRUY VẤN DANH SÁCH ĐƠN HÀNG
 */
// Lấy toàn bộ đơn hàng, ưu tiên những đơn mới nhất xếp trên (DESC)
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

$pageTitle = "Quản lý đơn hàng | Admin NHK Mobile";
$basePath = "../";
include 'includes/admin_header.php';
?>

        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Duyệt Đơn Hàng</h2>
                 <p class="text-secondary small mb-0">Quản lý và cập nhật trạng thái giao dịch khách hàng.</p>
            </div>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <!-- Hiển thị thông báo khi cập nhật thành công -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-primary alert-dismissible fade show mb-4 border-0 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> Trạng thái đơn hàng đã được cập nhật!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Tiếp nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp PHP Duyệt danh sách đơn hàng -->
                        <?php foreach($orders as $o): 
                            // Lấy chi tiết các sản phẩm trong đơn này kèm theo ảnh từ bảng products
                            $stmtItems = $pdo->prepare("SELECT order_items.*, products.image FROM order_items LEFT JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                            $stmtItems->execute([$o['id']]);
                            $items = $stmtItems->fetchAll();
                        ?>
                        <tr>
                            <td class="text-secondary fw-bold small">#ORD-<?php echo $o['id']; ?></td>
                            <td>
                                 <div class="fw-bold"><?php echo $o['customer_name']; ?></div>
                                 <div class="small text-secondary"><i class="bi bi-phone"></i> <?php echo $o['customer_phone']; ?></div>
                                 <!-- Hiển thị danh sách sản phẩm có kèm ảnh -->
                                 <div class="mt-2">
                                     <?php foreach($items as $item): ?>
                                         <div class="d-flex align-items-center gap-2 bg-light rounded px-2 py-1 mb-1 border" style="font-size: 0.75rem;">
                                             <img src="../assets/images/<?php echo $item['image']; ?>" class="rounded" style="width: 24px; height: 24px; object-fit: contain;" onerror="this.src='https://placehold.co/24'">
                                             <span class="fw-bold text-dark"><?php echo $item['product_name']; ?></span> 
                                             <span class="text-secondary">x<?php echo $item['quantity']; ?></span>
                                         </div>
                                     <?php endforeach; ?>
                                 </div>
                            </td>
                            <td class="fw-bold text-primary"><?php echo number_format($o['total_price'], 0, ',', '.'); ?>₫</td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal px-2"><?php echo $o['payment_method']; ?></span>
                                <?php if(isset($o['is_installment']) && $o['is_installment']): ?>
                                    <div class="mt-1"><span class="badge bg-primary-subtle text-primary border-0 fw-bold px-2 py-1" style="font-size: 0.65rem;">TRẢ GÓP 0%</span></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Màu sắc hiển thị badge theo trạng thái -->
                                <?php 
                                    $badgeClass = 'bg-warning text-dark';
                                    $s = mb_strtolower($o['status'], 'UTF-8');
                                    if (str_contains($s, 'đã duyệt')) $badgeClass = 'bg-info text-white';
                                    elseif (str_contains($s, 'đang giao')) $badgeClass = 'bg-primary text-white';
                                    elseif (str_contains($s, 'hoàn thành')) $badgeClass = 'bg-success text-white';
                                    elseif (str_contains($s, 'hủy')) $badgeClass = 'bg-danger text-white';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> border-0 px-3 py-2 rounded-pill small">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <!-- Nút In Hóa Đơn -->
                                <a href="invoice.php?order_id=<?php echo $o['id']; ?>" target="_blank" class="btn btn-sm btn-light border p-2 text-primary shadow-sm me-2" title="In Hóa Đơn">
                                    <i class="bi bi-printer"></i>
                                </a>
                                
                                <!-- Form Cập nhật trạng thái bằng Dropdown -->
                                <form action="orders.php" method="POST" class="d-inline-flex gap-2 align-items-center">
                                    <input type="hidden" name="id" value="<?php echo $o['id']; ?>">
                                    <select name="status" class="form-select form-select-sm border-secondary-subtle rounded-3" style="width: auto;" onchange="this.form.submit()">
                                        <option value="Chờ duyệt" <?php if($o['status'] == 'Chờ duyệt') echo 'selected'; ?>>Chờ duyệt</option>
                                        <option value="Đã duyệt" <?php if($o['status'] == 'Đã duyệt') echo 'selected'; ?>>Đã duyệt</option>
                                        <option value="Đang giao" <?php if($o['status'] == 'Đang giao') echo 'selected'; ?>>Đang giao</option>
                                        <option value="Hoàn thành" <?php if($o['status'] == 'Hoàn thành') echo 'selected'; ?>>Hoàn thành</option>
                                        <option value="Đã hủy" <?php if($o['status'] == 'Đã hủy') echo 'selected'; ?>>Đã hủy</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php include 'includes/admin_footer.php'; ?>
