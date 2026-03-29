<?php
// 1. Tìm DATABASE_URL (Cách phổ biến nhất trên Render/Heroku)
$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl)
    $databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? null;

if ($databaseUrl) {
    // Nếu có URL, bóc tách thông tin
    $dbParts = parse_url($databaseUrl);
    $host = $dbParts['host'] ?? 'localhost';
    $port = $dbParts['port'] ?? '5432';
    $db = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : '';
    $user = $dbParts['user'] ?? '';
    $pass = $dbParts['pass'] ?? '';
} else {
    // 2. Dự phòng các biến lẻ (DB_HOST, DB_USER...)codex
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
    $pdo = new PDO($dsn, $user, $pass, $options);

    // KHỞI TẠO SCHEMA: Chỉ chạy init_db.sql (bao gồm INSERT data mẫu)
    // khi bảng products còn RỖNG (lần đầu deploy / database mới)
    // Tránh INSERT lặp lại mỗi lần tải trang vì init_db.sql không có ON CONFLICT cho products
    $sqlFile = __DIR__ . '/../php/config/init_db.sql';
    if (file_exists($sqlFile)) {
        $productCount = 0;
        try { $productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(); } catch (\PDOException $e) {}

        if ($productCount === 0) {
            // Database rỗng → chạy toàn bộ (tạo bảng + INSERT data mẫu)
            $sql = file_get_contents($sqlFile);
            try { $pdo->exec($sql); } catch (\PDOException $e) { /* Bỏ qua lỗi migration */ }
        }
    }

    // MIGRATION FALLBACK: Luôn chạy để đảm bảo các bảng/cột mới luôn tồn tại
    // (an toàn với IF NOT EXISTS, chạy mỗi lần nhưng không sinh data trùng)
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
    try { $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.00;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0;"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);"); } catch (\PDOException $e) {}


} catch (\PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>