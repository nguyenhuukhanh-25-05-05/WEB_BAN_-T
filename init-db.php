<?php
/**
 * TRÌNH KHỞI TẠO CƠ SỞ DỮ LIỆU (DATABASE INITIALIZER)
 * Chạy file này một lần duy nhất trên Render để tạo các bảng cần thiết.
 * Sau khi chạy xong, hãy XÓA file này để đảm bảo bảo mật.
 */

require_once 'includes/db.php';

try {
    // Đọc nội dung file SQL
    $sql = file_get_contents('php/config/init_db.sql');
    
    // Thực thi câu lệnh SQL
    $pdo->exec($sql);
    
    echo "<h2 style='color: green;'>✅ Đã khởi tạo CSDL thành công!</h2>";
    echo "<p>Các bảng 'products' và 'orders' đã được tạo và thêm dữ liệu mẫu.</p>";
    echo "<p><strong>QUAN TRỌNG:</strong> Hãy xóa file 'init-db.php' này khỏi dự án của bạn ngay bây giờ để đảm bảo bảo mật.</p>";
    echo "<a href='index.php'>Quay lại trang chủ</a>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Lỗi khi khởi tạo CSDL:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
