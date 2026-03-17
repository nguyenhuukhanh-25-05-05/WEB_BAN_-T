<?php
/**
 * TỆP KẾT NỐI CƠ SỞ DỮ LIỆU POSTGRESQL
 * Sử dụng PDO (PHP Data Objects) để đảm bảo an toàn và bảo mật cho ứng dụng.
 */

// Thông số kết nối (Thay đổi theo cấu hình máy bạn)
$host = 'localhost';
$port = '5432';
$db   = 'web_ban_dien_thoai';
$user = 'postgres';
$pass = '123456'; // Mật khẩu PostgreSQL của bạn

// Chuỗi kết nối DSN
$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Dữ liệu trả về dạng mảng kết hợp
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Sử dụng prepared statements thật
];

try {
     // Khởi tạo đối tượng kết nối $pdo
     $pdo = new PDO($dsn, $user, $pass, $options);
     // echo "Kết nối thành công!"; // Bỏ comment để kiểm tra
} catch (\PDOException $e) {
     // Nếu có lỗi, dừng chương trình và thông báo
     die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>
