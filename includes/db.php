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

// 1. Cấu hình kết nối - ƯU TIÊN DATABASE_URL TỪ RENDER
// Render sẽ tự động set biến môi trường DATABASE_URL
$databaseUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? null);

// Fallback: Nếu không có DATABASE_URL, dùng Supabase (cho local dev)
if (!$databaseUrl) {
    $databaseUrl = 'postgresql://postgres.qfaslglevzkujkmylxfx:' . rawurlencode('@Khanh2006') . '@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres';
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
    $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? 'Anhkhoi2006@');

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
    try { $pdo->exec("ALTER TABLE news ADD COLUMN IF NOT EXISTS category VARCHAR(100) DEFAULT 'Technology';"); } catch (\PDOException $e) {}
    try { $pdo->exec("ALTER TABLE news ADD COLUMN IF NOT EXISTS excerpt TEXT;"); } catch (\PDOException $e) {}
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
    
// Thêm cột session_id cho bảng cart_items nếu chưa có (để tương thích với guest users)
    try { $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS session_id VARCHAR(255);"); } catch (\PDOException $e) {}
    
    // Đảm bảo có bảng Giỏ hàng (Cart Items) với cấu trúc đúng
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS cart_items (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            product_id INT REFERENCES products(id) ON DELETE CASCADE,
            quantity INT DEFAULT 1,
            session_id VARCHAR(255),
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (user_id, product_id)
        );
    "); } catch (\PDOException $e) {}
    
    // Xóa constraint cũ dựa trên (session_id, product_id) nếu tồn tại
    try { 
        $pdo->exec("ALTER TABLE cart_items DROP CONSTRAINT IF EXISTS cart_items_session_product_unique;"); 
    } catch (\PDOException $e) { /* Bỏ qua nếu không tồn tại */ }
    
    // Thêm ràng buộc duy nhất mới dựa trên (user_id, product_id) để ON CONFLICT hoạt động chính xác
    try { 
        $pdo->exec("ALTER TABLE cart_items ADD CONSTRAINT cart_items_user_product_unique UNIQUE (user_id, product_id);"); 
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
    try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user';"); } catch (\PDOException $e) {}

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

    // Đảm bảo có bảng Lưu trữ Lịch sử Thao tác Admin (Admin Logs)
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_logs (
            id SERIAL PRIMARY KEY,
            admin_id INT REFERENCES admins(id) ON DELETE SET NULL,
            action_type VARCHAR(50) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

    // Đảm bảo có bảng Chatbot Rules
    try { 
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS chatbot_rules (
            id SERIAL PRIMARY KEY,
            keyword VARCHAR(255) NOT NULL,
            response TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        "); 
        
        // Chèn dữ liệu mẫu nếu bảng có ít hơn 50 rule
        $stmt = $pdo->query("SELECT COUNT(*) FROM chatbot_rules");
        if ($stmt->fetchColumn() < 50) {
            $pdo->exec("TRUNCATE TABLE chatbot_rules RESTART IDENTITY;");
            $pdo->exec("
            INSERT INTO chatbot_rules (keyword, response) VALUES
            ('chào', 'Dạ NHK Mobile xin chào anh/chị! 👋 Em là trợ lý ảo, sẵn sàng hỗ trợ 24/7. Anh/chị cần tư vấn sản phẩm hay hỏi về dịch vụ ạ?'),
            ('hi', 'Hi anh/chị! NHK Mobile ở đây để hỗ trợ. Anh/chị đang tìm chiếc điện thoại nào ạ? 😊'),
            ('hello', 'Hello anh/chị! Chào mừng đến NHK Mobile – nơi hội tụ công nghệ đỉnh cao. Em có thể giúp gì không ạ?'),
            ('cảm ơn', 'Dạ không có gì ạ! Cảm ơn anh/chị đã tin tưởng NHK Mobile. Chúc anh/chị một ngày thật vui! 😊'),
            ('tạm biệt', 'Dạ tạm biệt anh/chị! Nếu cần hỗ trợ thêm, cứ nhắn em nhé. NHK Mobile luôn sẵn sàng! 🌟'),
            ('ok', 'Dạ vâng ạ! Anh/chị còn muốn hỏi thêm điều gì không? Em luôn sẵn sàng hỗ trợ!'),
            ('được rồi', 'Dạ vậy là ổn ạ! Nếu cần thêm thông tin gì cứ nhắn em nhé 😊'),
            ('giá', 'Dạ anh/chị xem giá chi tiết tại trang Sản phẩm ạ. NHK Mobile cam kết giá cạnh tranh nhất thị trường, không nơi nào rẻ hơn!'),
            ('rẻ', 'Dạ NHK Mobile cam kết giá tốt nhất! Nếu anh/chị tìm được nơi rẻ hơn, bên em giảm thêm 5% so với giá đó. Tham khảo thêm trên web nhé!'),
            ('khuyến mãi', 'Dạ hiện NHK Mobile đang có: giảm 15% máy cũ, tặng phụ kiện khi mua máy mới, trả góp 0%. Anh/chị ghé trang chủ xem ngay nhé!'),
            ('giảm giá', 'Dạ bên em có Flash Sale mỗi ngày, giảm đến 30% mẫu máy chọn lọc ạ. Thành viên đăng ký tài khoản nhận thêm ưu đãi độc quyền!'),
            ('flash sale', 'Dạ Flash Sale diễn ra mỗi ngày trên trang chủ với giá cực sốc ạ! Kết thúc lúc 23:59, anh/chị tranh thủ săn deal nhé! 🔥'),
            ('voucher', 'Dạ nhận voucher bằng cách: đăng ký tài khoản (50K), giới thiệu bạn bè (100K/lần), theo dõi fanpage NHK Mobile ạ. Rất nhiều ưu đãi chờ anh/chị!'),
            ('iphone', 'Dạ NHK Mobile là đại lý ủy quyền Apple chính thức! Có đủ iPhone 17 Series, 16 Series, 15 Series – hàng VN/A chính hãng, bảo hành Apple 12 tháng ạ.'),
            ('samsung', 'Dạ Samsung tại NHK Mobile đều là hàng SSVN chính hãng, bảo hành 12 tháng tại mọi trung tâm Samsung toàn quốc ạ. Có Galaxy S25 Ultra, A56, A35 – rất đáng mua!'),
            ('xiaomi', 'Dạ Xiaomi bên em có Redmi giá rẻ đến Xiaomi 15 Ultra cao cấp ạ. ROM Quốc tế sẵn tiếng Việt, hiệu năng mạnh, pin khủng – rất đáng tiền!'),
            ('oppo', 'Dạ OPPO có Reno13 Series, Find N5, A5 Pro – hàng chính hãng bảo hành 12 tháng ạ. Camera đẹp, thiết kế sang trọng, rất phù hợp tặng người thân!'),
            ('vivo', 'Dạ Vivo có X200 Ultra, V40, Y series – pin trâu, sạc nhanh, camera ấn tượng ạ. Anh/chị xem thêm trong mục Sản phẩm nhé!'),
            ('realme', 'Dạ Realme có nhiều mẫu giá tốt, hiệu năng cao ạ. GT7 Pro nổi bật với Snapdragon mạnh và sạc siêu nhanh 120W!'),
            ('tư vấn', 'Dạ để tư vấn đúng máy nhất, anh/chị dùng máy để làm gì (chụp ảnh, gaming, làm việc)? Ngân sách khoảng bao nhiêu ạ?'),
            ('chọn máy', 'Dạ NHK Mobile sẵn sàng tư vấn! Anh/chị cho em biết ngân sách và nhu cầu sử dụng chính để em gợi ý đúng nhất nhé ạ.'),
            ('dưới 5 triệu', 'Dạ ngân sách dưới 5 triệu em gợi ý: Xiaomi Redmi 13C, OPPO A38, Samsung A15 – pin trâu, màn hình lớn, đủ dùng cho nhu cầu cơ bản ạ!'),
            ('dưới 10 triệu', 'Dạ tầm 7-10 triệu có nhiều lựa chọn ngon: Samsung A55, OPPO Reno11, Xiaomi 14T – hiệu năng tốt, camera đẹp, dùng rất mượt ạ!'),
            ('trên 15 triệu', 'Dạ trên 15 triệu anh/chị có thể thoải mái chọn iPhone 16, Samsung S25, Xiaomi 15 Pro – flagship đỉnh cao, trải nghiệm xuất sắc ạ!'),
            ('chụp ảnh', 'Dạ ưu tiên camera, em gợi ý: iPhone 16 Pro (camera Pro cực đỉnh), Samsung S25 Ultra (zoom 100x), OPPO Find N5 (Hasselblad) – tùy ngân sách anh/chị nhé!'),
            ('gaming', 'Dạ máy gaming cần chip mạnh và pin khủng ạ. Em gợi ý: ASUS ROG Phone 9, Xiaomi 15 Ultra (Snapdragon 8 Elite), Samsung S25+ – chơi game cực mượt!'),
            ('pin trâu', 'Dạ cần pin trâu em gợi ý: Xiaomi Redmi Note 14 Pro (5500mAh, sạc 120W), Vivo Y200t (6000mAh), Samsung M55 – dùng cả ngày thoải mái ạ!'),
            ('bảo hành', 'Dạ NHK Mobile bảo hành chính hãng 12 tháng máy mới, 6 tháng pin cho máy cũ ạ. 30 ngày đầu lỗi phần cứng đổi máy mới 1:1 miễn phí!'),
            ('đổi trả', 'Dạ chính sách đổi trả: 30 ngày đổi máy mới nếu lỗi nhà sản xuất, 14 ngày trả hàng hoàn tiền nếu không hài lòng (máy nguyên vẹn). Yên tâm mua sắm!'),
            ('trả hàng', 'Dạ anh/chị có thể trả hàng trong 14 ngày kể từ khi nhận ạ. Vào Đơn hàng → Chi tiết đơn → Yêu cầu trả hàng để gửi yêu cầu nhé!'),
            ('hoàn tiền', 'Dạ sau khi shop nhận hàng trả về và kiểm tra OK, bên em hoàn tiền 3-5 ngày làm việc qua tài khoản ngân hàng anh/chị đã đăng ký ạ.'),
            ('trả góp', 'Dạ trả góp 0% lãi suất qua: thẻ tín dụng Visa/Mastercard (3-24 tháng), Home Credit, FE Credit, MCredit ạ. Chỉ cần CMND + hợp đồng lao động!'),
            ('ship', 'Dạ NHK Mobile miễn phí giao hàng toàn quốc! TP.HCM & Hà Nội giao trong 2 giờ, tỉnh thành khác 1-3 ngày. Được kiểm tra hàng trước khi nhận ạ!'),
            ('giao hàng', 'Dạ bên em giao hàng qua GHN, GHTK, Ninja Van ạ. Anh/chị được kiểm tra hàng trước khi thanh toán – đảm bảo an toàn tuyệt đối!'),
            ('thanh toán', 'Dạ hỗ trợ: COD (trả khi nhận), chuyển khoản, MoMo, ZaloPay, thẻ ATM/Visa/Mastercard và trả góp 0% ạ. Rất tiện lợi!'),
            ('momo', 'Dạ bên em nhận thanh toán qua MoMo ạ. Chọn phương thức MoMo khi checkout sẽ được quét QR thanh toán ngay!'),
            ('imei', 'Dạ bấm *#06# trên điện thoại lấy IMEI 15 số, vào mục Bảo hành trên web NHK Mobile để tra cứu hạn bảo hành và lịch sử sửa chữa ạ.'),
            ('sửa chữa', 'Dạ NHK Mobile có trung tâm sửa chữa uy tín, kỹ thuật viên được Apple & Samsung đào tạo ạ. Thay màn hình, pin, sạc – giá minh bạch, bảo hành linh kiện 3 tháng!'),
            ('màn hình', 'Dạ bị vỡ màn hình bên em hỗ trợ thay màn hình chính hãng ạ. Đem máy đến trực tiếp hoặc gọi 0375 352 347 để báo giá nhé!'),
            ('pin', 'Dạ thay pin tại NHK Mobile dùng pin chính hãng, bảo hành 6 tháng ạ. iPhone, Samsung, Xiaomi đều có sẵn – thay khoảng 30-45 phút!'),
            ('cài đặt', 'Dạ mua máy tại NHK Mobile được cài app, chuyển dữ liệu từ máy cũ sang máy mới miễn phí ạ. Nhân viên phục vụ tận tình ngay tại shop!'),
            ('đăng ký', 'Dạ vào trang Đăng ký, điền email và mật khẩu là có tài khoản ngay ạ. Thành viên mới được tặng voucher 50K cho đơn hàng đầu tiên!'),
            ('đăng nhập', 'Dạ quên mật khẩu thì vào Đăng nhập → Quên mật khẩu → nhập email để nhận link đặt lại ạ. Vẫn không được thì gọi 0375 352 347 nhé!'),
            ('tài khoản', 'Dạ vào trang Hồ sơ để xem thông tin cá nhân, lịch sử đơn hàng, yêu thích và yêu cầu trả hàng ạ. Quản lý tất cả ở một nơi rất tiện!'),
            ('phụ kiện', 'Dạ có đầy đủ phụ kiện chính hãng: sạc nhanh, cáp USB-C/Lightning, ốp lưng, kính cường lực, tai nghe, pin dự phòng ạ. Mua kèm máy giảm 20-30%!'),
            ('ốp lưng', 'Dạ ốp lưng có nhiều loại: ốp cứng, ốp dẻo, ốp chống sốc, ốp da cao cấp – giá từ 99K đến 599K ạ. Anh/chị thích loại nào?'),
            ('tai nghe', 'Dạ có AirPods chính hãng Apple, Samsung Galaxy Buds, Sony WF series và nhiều thương hiệu khác ạ. Giá tốt, bảo hành đầy đủ nhé!'),
            ('uy tín', 'Dạ NHK Mobile hoạt động hơn 10 năm, là đại lý ủy quyền Apple, Samsung, Xiaomi, OPPO ạ. Hàng chính hãng 100%, cam kết hoàn tiền nếu phát hiện hàng giả!'),
            ('chính hãng', 'Dạ tất cả sản phẩm NHK Mobile đều chính hãng 100%, có tem bảo hành của hãng, hóa đơn VAT đầy đủ ạ. Anh/chị hoàn toàn yên tâm!'),
            ('hotline', 'Dạ hotline hỗ trợ 24/7 của NHK Mobile: ☎️ 0375 352 347 ạ. Cũng có thể chat trực tiếp tại đây, em luôn sẵn sàng!'),
            ('máy cũ', 'Dạ máy cũ NHK Mobile được kiểm định kỹ, đạt chuẩn Likenew 99% nguyên zin chưa qua sửa chữa ạ. Bảo hành pin 6 tháng, giá chỉ 60-70% máy mới!'),
            ('mua hàng', 'Dạ để mua hàng, chọn sản phẩm trên web, bấm Thêm vào giỏ rồi thanh toán là xong ạ. Nhân viên bên em sẽ gọi xác nhận đơn hàng ngay!');
            ");
        }
    } catch (\PDOException $e) {
        error_log("[DB] Chatbot Rules creation error: " . $e->getMessage());
    }

    // ─── BẢNG YÊU CẦU TRẢ HÀNG / HOÀN TIỀN ─────────────────────────────────
    try { $pdo->exec("
        CREATE TABLE IF NOT EXISTS return_requests (
            id              SERIAL PRIMARY KEY,
            order_id        INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
            user_id         INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            customer_name   VARCHAR(255) NOT NULL,
            customer_phone  VARCHAR(20),
            order_code      VARCHAR(50),
            reason_type     VARCHAR(100),
            reason          TEXT NOT NULL,
            images          TEXT,
            status          VARCHAR(50) NOT NULL DEFAULT 'Chờ duyệt',
            admin_note      TEXT,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    "); } catch (\PDOException $e) {}

} catch (\PDOException $e) {
    error_log("[DB] Schema management error: " . $e->getMessage());
    // Không die ở đây vì kết nối đã thành công, chỉ là lỗi migration
}
?>