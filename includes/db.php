<?php
/**
 * TỆP KẾT NỐI CƠ SỞ DỮ LIỆU POSTGRESQL
 * Sử dụng PDO (PHP Data Objects) để đảm bảo an toàn và bảo mật cho ứng dụng.
 * Hỗ trợ Biến môi trường (Environment Variables) để dễ dàng triển khai lên Render.com
 */

// Lấy thông tin kết nối từ biến môi trường (Nếu có), nếu không thì dùng giá trị mặc định Localhost
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'web_ban_dien_thoai';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASS') ?: '123456'; 

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
} catch (\PDOException $e) {
     // Nếu có lỗi, dừng chương trình và thông báo
     die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>
