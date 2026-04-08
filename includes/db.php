<?php
/**
 * Tệp kết nối cơ sở dữ liệu (Database Connection)
 * - Tự động nhận diện DATABASE_URL từ môi trường (Render/Heroku).
 * - Khởi tạo bảng và dữ liệu mẫu nếu Database trống.
 * - Chứa cơ chế "Self-Healing" (Tự sửa lỗi) để bổ sung các cột dữ liệu mới.
 */

// 1. Tìm DATABASE_URL (Cách phổ biến nhất trên Render/Heroku)
$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl)
    $databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? null;

if ($databaseUrl) {
    // Nếu có URL, bóc tách thông tin từ chuỗi kết nối
    $dbParts = parse_url($databaseUrl);
    $host = $dbParts['host'] ?? 'localhost';
    $port = $dbParts['port'] ?? '5432';
    $db = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : '';
    $user = $dbParts['user'] ?? '';
    $pass = $dbParts['pass'] ?? '';
} else {
    // 2. Dự phòng các biến lẻ (DB_HOST, DB_USER...)
    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost');
    $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '5432');
    $db = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'web_ban_dien_thoai');
    $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'postgres');
    $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? 'Anhkhoi2006@');
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Khởi tạo kết nối PDO
    $pdo = new PDO($dsn, $user, $pass, $options);

    /**
     * KHỞI TẠO SCHEMA LẦN ĐẦU
     * Chỉ chạy init_db.sql (tạo bảng và chèn sản phẩm mẫu) khi bảng products còn trống.
     */
    $sqlFile = __DIR__ . '/../php/config/init_db.sql';
    if (file_exists($sqlFile)) {
        $productCount = 0;
        try { 
            $productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(); 
        } catch (\PDOException $e) {
            // Lỗi bảng không tồn tại -> productCount giữ nguyên là 0 để chạy khởi tạo
        }

        if ($productCount === 0) {
            $sql = file_get_contents($sqlFile);
            try { $pdo->exec($sql); } catch (\PDOException $e) { /* Bỏ qua lỗi migration nếu có */ }
        }
    }

    /**
     * MIGRATION FALLBACK (Cơ chế tự sửa lỗi)
     * Luôn chạy các lệnh sau để đảm bảo DB luôn có đủ bảng/cột mới nhất.
     */
    
    // Đảm bảo có bảng Đánh giá (Reviews)
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS reviews (
            id SERIAL PRIMARY KEY,
            product_id INT REFERENCES products(id) ON DELETE CASCADE,
            user_id INT REFERENCES users(id) ON DELETE SET NULL,
            reviewer_name VARCHAR(255),
            reviewer_email VARCHAR(255),
            rating INT CHECK (rating >= 1 AND rating <= 5),
            title VARCHAR(255),
            content TEXT,
            verified_purchase INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

    // Bổ sung các cột bị thiếu do nâng cấp hệ thống (is_installment, rating, vv...)
    try { $pdo->exec("ALTER TABLE reviews ADD COLUMN IF NOT EXISTS image VARCHAR(255);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE news ADD COLUMN IF NOT EXISTS tags VARCHAR(255);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.00;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0;"); } catch (\PDOException $e) {}
    
    // Cập nhật cấu trúc bảng Orders (Đơn hàng)
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS is_installment BOOLEAN DEFAULT FALSE;"); } catch (\PDOException $e) {}
    
    // Cập nhật cấu trúc bảng Giỏ hàng (Cart Items)
    try { $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}

} catch (\PDOException $e) {
    die("Lỗi nghiêm trọng khi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>