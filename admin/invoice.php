<?php
// Bắt đầu phiên làm việc
require_once 'admin_auth.php';

// Nhúng file kết nối CSDL Postgres
require_once '../includes/db.php';

// Lấy ID đơn hàng
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    die("Không tìm thấy mã đơn hàng.");
}

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// Lấy chi tiết sản phẩm
$stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmtItems->execute([$order_id]);
$items = $stmtItems->fetchAll();

$pageTitle = "In Hóa Đơn #" . $order['id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Inline CSS cho bản in (Print) -->
    <style>
        body {
            background-color: #f0f0f0; /* Nền xám khi xem trên web */
            color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .invoice-container {
            background-color: #fff;
            width: 210mm; /* Khổ giấy A4 */
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            filter: brightness(0); /* Black and white logo for printing */
            height: 40px;
            margin-bottom: 15px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .table-invoice th {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #000;
            text-transform: uppercase;
            font-size: 13px;
        }
        .table-invoice td {
            vertical-align: middle;
        }
        .total-row {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .footer-note {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        /* Style chỉ áp dụng khi IN RA GIẤY (Ctrl+P) */
        @media print {
            body {
                background-color: #fff;
                margin: 0;
            }
            .invoice-container {
                margin: 0;
                padding: 10mm; /* Bớt padding khi in thật */
                width: 100%;
                box-shadow: none; /* Bỏ bóng mờ */
            }
            .no-print {
                display: none !important; /* Ẩn các nút bấm khi in */
            }
        }
    </style>
</head>
<body onload="window.print()"> <!-- Tự động mở hộp thoại in khi tải xong trang -->

    <!-- Thanh công cụ nổi khi xem trên màn hình vi tính -->
    <div class="text-center py-3 bg-dark text-white no-print sticky-top shadow">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2"><i class="bi bi-printer me-2"></i>In Hóa Đơn (Ctrl+P)</button>
        <button onclick="window.close()" class="btn btn-outline-light px-4">Đóng</button>
    </div>

    <!-- KHUNG HÓA ĐƠN A4 -->
    <div class="invoice-container">
        
        <!-- Header Hóa Đơn -->
        <div class="invoice-header row align-items-center">
            <div class="col-6">
                <img src="../assets/images/logo-k.svg" class="company-logo" alt="NHK Mobile">
                <div class="small">
                    <strong>Trung Tâm Bảo Hành & Bán Lẻ NHK Mobile</strong><br>
                    123 Đường Công Nghệ, Q. Cầu Giấy, TP. Hà Nội<br>
                    Hotline: 0333 427 187<br>
                    Email: support@nhkmobile.vn
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="invoice-title">HÓA ĐƠN BÁN HÀNG</div>
                <div class="text-secondary mt-2">
                    Mã đơn: <strong>#ORD-<?php echo str_pad($order['id'], 5, "0", STR_PAD_LEFT); ?></strong><br>
                    Ngày lập: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </div>
                <!-- Giả lập in mã vạch (Barcode bằng font chữ đơn giản hoặc layout CSS) -->
                <div class="mt-3" style="font-family: monospace; font-size: 24px; letter-spacing: -2px;">
                    |||| || || | || | || |
                </div>
                <div class="small text-secondary">ORD-<?php echo $order['id']; ?></div>
            </div>
        </div>

        <!-- Thông tin Khách hàng -->
        <div class="row mb-5">
            <div class="col-12">
                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Thông tin khách hàng</h6>
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="150" class="text-secondary">Họ và tên:</td>
                        <td class="fw-bold fs-5"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary">Số điện thoại:</td>
                        <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary">Địa chỉ:</td>
                        <td>(Thu thập từ tài khoản nếu có)</td>
                    </tr>
                    <tr>
                        <td class="text-secondary">Phương thức TT:</td>
                        <td>Thanh toán khi nhận hàng (<?php echo htmlspecialchars($order['payment_method']); ?>)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Bảng chi tiết sản phẩm -->
        <table class="table table-invoice mb-5">
            <thead>
                <tr>
                    <th width="50">STT</th>
                    <th>Tên sản phẩm</th>
                    <th width="100" class="text-center">Số lượng</th>
                    <th width="150" class="text-end">Đơn giá</th>
                    <th width="150" class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 1;
                $subtotal = 0;
                foreach($items as $item): 
                    $lineTotal = $item['price'] * $item['quantity'];
                    $subtotal += $lineTotal;
                ?>
                <tr>
                    <td><?php echo $stt++; ?></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-end"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</td>
                    <td class="text-end"><?php echo number_format($lineTotal, 0, ',', '.'); ?>₫</td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Dòng tiền -->
                <tr>
                    <td colspan="4" class="text-end pt-4">Tạm tính:</td>
                    <td class="text-end pt-4"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end">Phí vận chuyển:</td>
                    <td class="text-end">0₫ (Miễn phí)</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end">Thuế VAT (10%):</td>
                    <td class="text-end">Đã bao gồm</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="text-end pt-3">TỔNG CỘNG LÚC THANH TOÁN:</td>
                    <td class="text-end fs-4 pt-3 text-primary"><?php echo number_format($order['total_price'], 0, ',', '.'); ?>₫</td>
                </tr>
            </tbody>
        </table>

        <!-- Chữ ký -->
        <div class="row text-center mt-5 pt-5">
            <div class="col-6">
                <span class="fw-bold text-uppercase d-block mb-5">Khách hàng</span>
                <span class="text-secondary small">(Ký và ghi rõ họ tên)</span>
            </div>
            <div class="col-6">
                <span class="fw-bold text-uppercase d-block mb-5">Nhân viên bán hàng</span>
                <span class="text-secondary small">(Ký và ghi rõ họ tên)</span>
            </div>
        </div>

        <div class="footer-note">
            Cảm ơn Quý khách đã mua sắm tại NHK Mobile. Hóa đơn này có giá trị như phiếu bảo hành.<br>
            Vui lòng giữ lại hóa đơn để được hỗ trợ tốt nhất trong quá trình sử dụng.
        </div>
    </div>
</body>
</html>
