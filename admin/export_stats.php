<?php
require_once 'admin_auth.php';
require_once '../includes/db.php';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=BaoCaoThongKe_NHKMobile_' . date('Ymd_His') . '.csv');

// Print UTF-8 BOM so Excel reads Vietnamese characters correctly
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Title row
fputcsv($output, ['BÁO CÁO THỐNG KÊ DOANH THU & HOẠT ĐỘNG - NHK MOBILE']);
fputcsv($output, ['Ngày xuất file:', date('d/m/Y H:i:s')]);
fputcsv($output, []);

// 1. Tổng quan
fputcsv($output, ['1. TỔNG QUAN HỆ THỐNG']);
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $stmt->fetch()['total_users'];
fputcsv($output, ['Tổng số Khách hàng:', $totalUsers]);

$stmt = $pdo->query("SELECT COUNT(*) as total_orders, SUM(total_price) as total_rev FROM orders WHERE status = 'Đã duyệt' or status = 'Hoàn thành' or status = 'Completed'");
$orderStats = $stmt->fetch();
fputcsv($output, ['Tổng số Đơn hàng Thành công:', $orderStats['total_orders']]);
fputcsv($output, ['Tổng Doanh thu (VNĐ):', number_format($orderStats['total_rev'], 0, ',', '.')]);
fputcsv($output, []);

// 2. Chi tiết đơn hàng gần đây
fputcsv($output, ['2. DANH SÁCH ĐƠN HÀNG']);
fputcsv($output, ['Mã Đơn', 'Khách hàng', 'Số Điện Thoại', 'Tổng Tiền (VNĐ)', 'Trạng Thái', 'Ngày Đặt']);

$stmtOrders = $pdo->query("SELECT id, customer_name, customer_phone, total_price, status, created_at FROM orders ORDER BY created_at DESC LIMIT 100");
while($row = $stmtOrders->fetch(PDO::FETCH_ASSOC)) {
    // Chuyển đổi status sang tiếng Việt dễ đọc hơn
    $statusText = $row['status'];
    switch (mb_strtolower($row['status'], 'UTF-8')) {
        case 'đã duyệt': $statusText = 'Đã Duyệt'; break;
        case 'chờ duyệt': $statusText = 'Chờ Duyệt'; break;
        case 'hoàn thành': case 'completed': $statusText = 'Hoàn Thành'; break;
        case 'đã hủy': case 'cancelled': $statusText = 'Đã Hủy'; break;
    }

    fputcsv($output, [
        '#ORD-'.$row['id'],
        $row['customer_name'],
        $row['customer_phone'],
        number_format($row['total_price'], 0, ',', '.') . ' VNĐ',
        $statusText,
        date('d/m/Y H:i', strtotime($row['created_at']))
    ]);
}
fputcsv($output, []);

// 3. Chi tiết người theo dõi bản tin
fputcsv($output, ['3. DANH SÁCH SUBSCRIBERS']);
fputcsv($output, ['Email', 'Ngày đăng ký']);
$stmtSubs = $pdo->query("SELECT email, created_at FROM subscribers ORDER BY created_at DESC LIMIT 100");
while($row = $stmtSubs->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['email'],
        date('d/m/Y H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;
