<?php
/**
 * NHK Mobile - Database Connection & Schema Management
 *
 * Description: Orchestrates the connection to PostgreSQL and implements
 * a "Self-Healing" schema layer that ensures all modern features
 * (reviews, installments, tagging) have required storage structures.
 *
 * Author: NguyenHuuKhanh
 * Version: 2.6
 * Date: 2026-04-16
 */

require_once __DIR__ . '/functions.php';

// 1. Cấu hình kết nối - SỬ DỤNG TRANSACTION POOLER (CỔNG 6543) ĐỂ ỔN ĐỊNH TRÊN RENDER
$databaseUrl = 'postgresql://postgres.qfaslglevzkujkmylxfx:' . urlencode('@Khanh2006') . '@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres';

// Nếu có biến môi trường từ Render và không phải database cũ, mới ghi đè
$envUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? null);
if ($envUrl && strpos($envUrl, 'render.com') === false) {
    $databaseUrl = $envUrl;
}

$connected = false;
$pdo = null;

if ($databaseUrl) {
    $dbParts = parse_url($databaseUrl);
    $host = $dbParts['host'] ?? '';
    $port = $dbParts['port'] ?? '5432';
    $db = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : '';
    $user = isset($dbParts['user']) ? urldecode($dbParts['user']) : '';
    $pass = isset($dbParts['pass']) ? urldecode($dbParts['pass']) : '';

    try {
        // Dùng SSL mode cho Supabase
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require;connect_timeout=10";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);
        $connected = true;
    } catch (PDOException $e) {
        error_log("[DB] Primary connection failed: " . $e->getMessage());
    }
}

// 2. Nếu không kết nối được, thử dùng biến môi trường riêng lẻ
if (!$connected) {
    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost');
    $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '5432');
    $db = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'web_ban_dien_thoai');
    $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'postgres');
    $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? '');

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;connect_timeout=5";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);
        $connected = true;
    } catch (PDOException $e) {
        error_log("[DB] Failed to connect using individual env vars: " . $e->getMessage());
        $pdo = null;
    }
}

// 3. Nếu vẫn không kết nối được, thử kết nối local development
if (!$connected) {
    try {
        $dsn = "pgsql:host=localhost;port=5432;dbname=web_ban_dien_thoai;connect_timeout=3";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, 'postgres', '', $options);
        $connected = true;
        error_log("[DB] Connected using local fallback");
    } catch (PDOException $e) {
        error_log("[DB] Failed to connect using local fallback: " . $e->getMessage());
        $pdo = null;
    }
}

// 4. Nếu tất cả đều thất bại, hiển thị lỗi thân thiện
if (!$connected || !$pdo) {
    http_response_code(503);
    $errorMsg = '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi kết nối - NHK Mobile</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f7; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-container { text-align: center; padding: 40px; max-width: 500px; }
        .error-icon { font-size: 64px; margin-bottom: 20px; }
        h1 { color: #1d1d1f; margin-bottom: 16px; }
        p { color: #6e6e73; line-height: 1.6; margin-bottom: 24px; }
        .btn { display: inline-block; padding: 14px 28px; background: #007AFF; color: white; text-decoration: none; border-radius: 980px; font-weight: 600; }
        .btn:hover { background: #0056b3; }
        .retry-info { margin-top: 24px; padding: 16px; background: #fff; border-radius: 12px; font-size: 14px; color: #86868b; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔧</div>
        <h1>Đang bảo trì hệ thống</h1>
        <p>Chúng tôi đang nâng cấp cơ sở dữ liệu để phục vụ bạn tốt hơn. Vui lòng thử lại sau vài phút.</p>
        <a href="/" class="btn">Thử lại</a>
        <div class="retry-info">
            Nếu lỗi tiếp tục xảy ra, vui lòng liên hệ hotline: <strong>1900 xxxx</strong>
        </div>
    </div>
</body>
</html>';
    die($errorMsg);
}

// Kết nối thành công, tiếp tục với schema management
try {

    // CHECK FOR FORCE RESET (via environment variable)
    // Set FORCE_DB_RESET=true in Render environment to trigger full reset
    try {
        $forceReset = getenv('FORCE_DB_RESET') === 'true' || ($_ENV['FORCE_DB_RESET'] ?? '') === 'true';
        
        if ($forceReset) {
            @error_log("[DB] FORCE RESET TRIGGERED - Dropping and recreating all tables...");
            
            // Drop tất cả tables
            $tables = [
                'password_resets', 'repair_history', 'order_items', 'orders',
                'cart_items', 'reviews', 'wishlists', 'warranties',
                'products', 'users', 'admins', 'news'
            ];
            foreach ($tables as $table) {
                try { $pdo->exec("DROP TABLE IF EXISTS $table CASCADE"); } catch (\PDOException $e) {}
            }
        }
    } catch (\Exception $e) {
        // Ignore FORCE_RESET errors
    }

    /**
     * KHỞI TẠO SCHEMA LẦN ĐẦU
     * Chỉ chạy init_db.sql (tạo bảng và chèn sản phẩm mẫu) khi bảng products còn trống
     * HOẶC khi FORCE_DB_RESET=true
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
            error_log("[DB] Initial schema created from init_db.sql");
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
    try { $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS specs TEXT;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE products ADD CONSTRAINT products_name_unique UNIQUE (name);"); } catch (\PDOException $e) {}
    
    // Cập nhật cấu trúc bảng Orders (Đơn hàng)
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_address TEXT;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS is_installment BOOLEAN DEFAULT FALSE;"); } catch (\PDOException $e) {}
    
    // Cập nhật cấu trúc bảng Giỏ hàng (Cart Items)
    try { $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS session_id VARCHAR(255);"); } catch (\PDOException $e) {}
    try { 
        // Thêm ràng buộc duy nhất để ON CONFLICT hoạt động chính xác trong syncCartWithDatabase
        $pdo->exec("ALTER TABLE cart_items ADD CONSTRAINT cart_items_session_product_unique UNIQUE (session_id, product_id);"); 
    } catch (\PDOException $e) { /* Bỏ qua nếu đã tồn tại */ }

    // Đảm bảo có bảng Bảo hành IMEI (Warranties)
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS warranties (
            id          SERIAL PRIMARY KEY,
            imei        VARCHAR(20) NOT NULL UNIQUE,
            product_id  INT REFERENCES products(id) ON DELETE SET NULL,
            order_id    INT REFERENCES orders(id) ON DELETE SET NULL,
            status      VARCHAR(50) NOT NULL DEFAULT 'Active',
            expires_at  DATE NOT NULL,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

    // Bổ sung cột bị thiếu trên bảng warranties legacy
    try { $pdo->exec("ALTER TABLE warranties ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE warranties ADD COLUMN IF NOT EXISTS customer_name VARCHAR(255);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE warranties ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE warranties ADD COLUMN IF NOT EXISTS order_id INT REFERENCES orders(id) ON DELETE SET NULL;"); } catch (\PDOException $e) {}

    // Đảm bảo có bảng Lịch sử Sửa chữa (Repair History)
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS repair_history (
            id          SERIAL PRIMARY KEY,
            warranty_id INT REFERENCES warranties(id) ON DELETE CASCADE,
            repair_date DATE NOT NULL,
            title       VARCHAR(255) NOT NULL,
            description TEXT,
            location    VARCHAR(255),
            repair_id   VARCHAR(50),
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

    // Bổ sung cột hồ sơ người dùng (profile)
    try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone   VARCHAR(20);");  } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT;");         } catch (\PDOException $e) {}

    // Đảm bảo có bảng Danh sách Yêu thích (Wishlists)
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS wishlists (
            id         SERIAL PRIMARY KEY,
            user_id    INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (user_id, product_id)
        );
    "); } catch (\PDOException $e) {}

    // Đảm bảo email trong bảng users là UNIQUE (phòng trường hợp migration cũ)
    try { $pdo->exec("ALTER TABLE users ADD CONSTRAINT users_email_unique UNIQUE (email);"); } catch (\PDOException $e) {}

    // Đảm bảo có bảng Password Resets cho chức năng quên mật khẩu
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            reset_token VARCHAR(255) NOT NULL UNIQUE,
            expires_at TIMESTAMP NOT NULL,
            is_used BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

    // Thêm cột username cho bảng users nếu chưa có (để tương thích)
    try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(50);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD CONSTRAINT users_username_unique UNIQUE (username);"); } catch (\PDOException $e) {}

    // Thêm cột reset_status cho bảng users để track password reset requests
    try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_password_reset TIMESTAMP;"); } catch (\PDOException $e) {}

} catch (\PDOException $e) {
    error_log("[DB] Schema management error: " . $e->getMessage());
    // Không die ở đây vì kết nối đã thành công, chỉ là lỗi migration
}
?>