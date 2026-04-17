<?php
/**
 * NHK Mobile - Database Reset Utility
 *
 * DANGER: This script will WIPE all data and reset the database to
 * its initial state for testing.
 * 
 * CLEAN DATA:
 * - 1 Admin: admin / admin123
 * - 1 Test User: testuser / test123
 * - Sample products
 * - NO orders, NO warranties, NO reviews
 */
session_start();
require_once 'includes/db.php';

// Simple security check: Only allow if explicitly requested via GET or if admin is logged in
if (!isset($_GET['confirm']) && !isset($_SESSION['admin_id'])) {
    die("To reset the database, please visit: reset_database.php?confirm=yes");
}

try {
    $pdo->beginTransaction();

    echo "<h2>🔄 Đang dọn dẹp database...</h2><br>";

    // 1. Drop all existing tables (Order matters due to Foreign Keys)
    $tables = [
        'order_items',
        'orders',
        'cart_items',
        'reviews',
        'wishlists',
        'repair_history',
        'warranties',
        'password_resets',
        'products',
        'users',
        'admins',
        'news'
    ];

    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table CASCADE");
        echo "✅ Dropped table: $table<br>";
    }

    echo "<br><h3>📦 Tạo lại tables...</h3><br>";

    // 2. Create Tables

    // Admins
    $pdo->exec("CREATE TABLE admins (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");
    echo "✅ Created: admins<br>";

    // Users
    $pdo->exec("CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        status VARCHAR(20) DEFAULT 'active',
        phone VARCHAR(20),
        address TEXT,
        username VARCHAR(50),
        last_password_reset TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: users<br>";

    // Products
    $pdo->exec("CREATE TABLE products (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL,
        category VARCHAR(100),
        price DECIMAL(15,2) NOT NULL,
        stock INT DEFAULT 0,
        image VARCHAR(255),
        description TEXT,
        specs TEXT,
        is_featured BOOLEAN DEFAULT FALSE,
        rating DECIMAL(3,2) DEFAULT 0.00,
        review_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: products<br>";

    // Orders
    $pdo->exec("CREATE TABLE orders (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES users(id) ON DELETE SET NULL,
        customer_name VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_address TEXT,
        total_price DECIMAL(15,2) NOT NULL,
        payment_method VARCHAR(50) DEFAULT 'COD',
        is_installment BOOLEAN DEFAULT FALSE,
        status VARCHAR(50) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: orders<br>";

    // Order Items
    $pdo->exec("CREATE TABLE order_items (
        id SERIAL PRIMARY KEY,
        order_id INT REFERENCES orders(id) ON DELETE CASCADE,
        product_id INT REFERENCES products(id) ON DELETE SET NULL,
        product_name VARCHAR(255),
        quantity INT NOT NULL,
        price DECIMAL(15,2) NOT NULL
    )");
    echo "✅ Created: order_items<br>";

    // Cart Items
    $pdo->exec("CREATE TABLE cart_items (
        id SERIAL PRIMARY KEY,
        session_id VARCHAR(255),
        user_id INT REFERENCES users(id) ON DELETE CASCADE,
        product_id INT REFERENCES products(id) ON DELETE CASCADE,
        quantity INT DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (session_id, product_id)
    )");
    echo "✅ Created: cart_items<br>";

    // Reviews
    $pdo->exec("CREATE TABLE reviews (
        id SERIAL PRIMARY KEY,
        product_id INT REFERENCES products(id) ON DELETE CASCADE,
        user_id INT REFERENCES users(id) ON DELETE SET NULL,
        reviewer_name VARCHAR(255),
        reviewer_email VARCHAR(255),
        rating INT CHECK (rating >= 1 AND rating <= 5),
        title VARCHAR(255),
        content TEXT,
        verified_purchase INT DEFAULT 0,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: reviews<br>";

    // Wishlists
    $pdo->exec("CREATE TABLE wishlists (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (user_id, product_id)
    )");
    echo "✅ Created: wishlists<br>";

    // Warranties
    $pdo->exec("CREATE TABLE warranties (
        id SERIAL PRIMARY KEY,
        product_id INT REFERENCES products(id) ON DELETE SET NULL,
        order_id INT REFERENCES orders(id) ON DELETE SET NULL,
        imei VARCHAR(20) UNIQUE NOT NULL,
        customer_name VARCHAR(255),
        customer_phone VARCHAR(20),
        expires_at DATE,
        status VARCHAR(50) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: warranties<br>";

    // Repair History
    $pdo->exec("CREATE TABLE repair_history (
        id SERIAL PRIMARY KEY,
        warranty_id INT REFERENCES warranties(id) ON DELETE CASCADE,
        repair_date DATE NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        location VARCHAR(255),
        repair_id VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: repair_history<br>";

    // Password Resets
    $pdo->exec("CREATE TABLE password_resets (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        reset_token VARCHAR(255) NOT NULL UNIQUE,
        expires_at TIMESTAMP NOT NULL,
        is_used BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: password_resets<br>";

    // News
    $pdo->exec("CREATE TABLE news (
        id SERIAL PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        image VARCHAR(255),
        tags VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Created: news<br>";

    echo "<br><h3>📝 Đang thêm dữ liệu mẫu...</h3><br>";

    // 3. Insert Seed Data

    // Default Admin (Username: admin, Password: admin123)
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)")->execute(['admin', $adminPass]);
    echo "✅ Admin: admin / admin123<br>";

    // Test User (Email: test@test.com, Password: test123)
    $userPass = password_hash('Test123!', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (fullname, email, password, status, phone, address) VALUES (?, ?, ?, 'active', '0901234567', '123 Đường Test, Quận 1, TP.HCM')")->execute(['Test User', 'test@test.com', $userPass]);
    echo "✅ User: test@test.com / Test123!<br>";

    // All Products (mapped to available images in assets/images)
    $products = [
        // Apple
        ['iPhone 17 Pro Max',   'Apple',   32990000, 50, 'ai_ip17_pm.png',        'Siêu phẩm AI thế hệ mới với chip A19 Pro và camera đột phá.',            '256GB, 12GB RAM, A19 Pro, Camera 48MP Fusion', true],
        ['iPhone 16 Pro',       'Apple',   27990000, 40, 'ai_ip16_pro.png',        'Hiệu năng vượt bậc với chip A18 Pro và màn hình Super Retina XDR.',      '256GB, 8GB RAM, A18 Pro, Camera 48MP', true],
        ['iPhone 16e',          'Apple',   19990000, 35, 'ai_ip16e.png',           'iPhone nhỏ gọn, mạnh mẽ với chip A16 Bionic và thiết kế tinh tế.',       '128GB, 6GB RAM, A16 Bionic', false],
        ['iPhone 15 Pro Max',   'Apple',   22990000, 20, 'ai_ip15_pm.png',         'Titan tự nhiên, chip A17 Pro - đỉnh cao nhiếp ảnh di động.',             '256GB, 8GB RAM, A17 Pro, Camera 48MP', false],
        // Samsung
        ['Samsung S25 Ultra',   'Samsung', 29490000, 30, 'ai_s25_ultra.png',       'Đỉnh cao màn hình vô cực với S Pen thông minh và Galaxy AI.',            '512GB, 16GB RAM, Snapdragon 8 Elite, S Pen', true],
        ['Samsung S24 Ultra',   'Samsung', 23990000, 25, 'ai_s24_ultra.png',       'Màn hình Dynamic AMOLED 2X 120Hz, camera 200MP siêu sắc nét.',           '256GB, 12GB RAM, Snapdragon 8 Gen 3, 200MP', false],
        ['Samsung S23',         'Samsung', 13990000, 30, 'ai_s23.png',             'Hiệu năng Snapdragon 8 Gen 2  bền bỉ trong tầm tay.',                    '128GB, 8GB RAM, Snapdragon 8 Gen 2', false],
        // Xiaomi
        ['Xiaomi 17 Ultra',     'Xiaomi',  24500000, 15, 'ai_mi17_ultra.png',      'Camera Leica thế hệ 4,  sạc siêu nhanh 90W, Snapdragon 8 Elite.',        '256GB, 16GB RAM, Snapdragon 8 Elite, Leica Camera', true],
        ['Xiaomi 15T',          'Xiaomi',  16990000, 20, 'ai_mi15t.png',           'Màn hình AMOLED 144Hz, sạc nhanh 67W, Dimensity 9300+.',                 '256GB, 12GB RAM, Dimensity 9300+, 144Hz AMOLED', false],
        ['Xiaomi Mix Flip',     'Xiaomi',  21990000, 10, 'ai_mi_flip.png',         'Điện thoại màn hình gập Snapdragon 8 Gen 3 tinh tế và sang trọng.',      '256GB, 12GB RAM, Snapdragon 8 Gen 3, Gập đứng', false],
        // OPPO
        ['OPPO Find X10',       'OPPO',    22990000, 20, 'oppo_findx10.png',       'Camera Hasselblad đỉnh cao, sạc siêu nhanh 100W, Dimensity 9400.',       '256GB, 16GB RAM, Dimensity 9400, Hasselblad Camera', true],
        ['OPPO K300',           'OPPO',    10990000, 35, 'oppo_k300.png',          'Pin khổng lồ 6000mAh, sạc nhanh 80W, hiệu năng tốt trong tầm giá.',     '128GB, 8GB RAM, Snapdragon 695, 6000mAh', false],
        ['OPPO Mix Flip 5090',  'OPPO',    24990000, 12, 'oppo_mixflip5090.png',   'Điện thoại flip cao cấp với chip Snapdragon 8 Elite và màn hình AMOLED.','256GB, 12GB RAM, Snapdragon 8 Elite, Gập đứng', false],
        // OnePlus
        ['OnePlus 13',          'OnePlus', 15500000, 20, 'oneplus13.png',          'Mượt mà bậc nhất phân khúc, Snapdragon 8 Elite, sạc siêu nhanh 100W.',   '256GB, 12GB RAM, Snapdragon 8 Elite, 100W', false],
        ['OnePlus 15',          'OnePlus', 19990000, 18, 'oneplus15.png',          'Camera Hasselblad Satellite, hiệu năng Snapdragon 8 Gen 3 đỉnh cao.',    '256GB, 16GB RAM, Snapdragon 8 Gen 3, Hasselblad', false],
        ['OnePlus 15R',         'OnePlus', 12990000, 25, 'oneplus15r.png',         'Hiệu năng mạnh mẽ, pin dung lượng lớn, thiết kế đẹp trong tầm giá.',    '128GB, 8GB RAM, Snapdragon 7+ Gen 3', false],
        // Realme
        ['Realme GT 8 Pro',     'Realme',  17990000, 22, 'realme_gt8pro.png',      'Snap 8s Gen 3,  sạc nhanh 120W, màn hình 144Hz - hiệu năng top tầm giá.','256GB, 12GB RAM, Snapdragon 8s Gen 3, 144Hz', true],
        ['Realme GT9',          'Realme',  14990000, 28, 'realme_gt9.png',         'Dimensity 9300+, sạc 100W, camera 50MP sắc nét, thiết kế gaming.',       '256GB, 12GB RAM, Dimensity 9300+, 50MP', false],
        ['Realme GT 8 Pro Blue','Realme',  17990000, 15, 'realmegt8problue.png',   'Phiên bản màu xanh đặc biệt của Realme GT 8 Pro - hiệu năng vượt trội.', '256GB, 12GB RAM, Snapdragon 8s Gen 3, Xanh Dương', false],
        ['Realme GT7',          'Realme',  11990000, 30, 'ai_realme_gt7.png',      'Chip Dimensity 8350, sạc 80W, pin 5500mAh - trải nghiệm gaming mượt mà.','128GB, 8GB RAM, Dimensity 8350, 5500mAh', false],
        // Vivo
        ['Vivo X200 Black',     'Vivo',    21990000, 18, 'ai_vivo_x200_black.png', 'Camera Zeiss 200MP,  sạc nhanh 90W, Dimensity 9400 - ảnh đẹp tuyệt đỉnh.','256GB, 16GB RAM, Dimensity 9400, Zeiss 200MP', true],
        ['Vivo X300',           'Vivo',    25990000, 12, 'ai_vivo_x300.png',       'Flagship mới nhất của Vivo với camera Zeiss và Snapdragon 8 Elite.',     '512GB, 16GB RAM, Snapdragon 8 Elite, Zeiss Camera', false],
        // Honor
        ['Honor Magic 10',      'Honor',   18990000, 20, 'honor_magic10.png',      'Hiệu năng mạnh mẽ, thiết kế slim đẹp, camera AI thế hệ mới.',            '256GB, 12GB RAM, Snapdragon 8 Gen 3, AI Camera', false],
        ['Honor Magic 9',       'Honor',   15990000, 22, 'honor_magic9.png',       'Màn hình OLED cong, chip Kirin 9010, camera 50MP siêu sắc nét.',         '256GB, 12GB RAM, Kirin 9010, OLED Cong', false],
        // Nubia
        ['Nubia Magic 15',      'Nubia',   18990000, 15, 'nubia_magic15.png',      'Gaming phone mạnh nhất, tản nhiệt đỉnh cao, màn hình 165Hz AMOLED.',     '256GB, 16GB RAM, Snapdragon 8 Elite, 165Hz Gaming', false],
        ['Nubia V1000',         'Nubia',   12990000, 20, 'nubia_v1000.png',        'Pin siêu dung lượng 6500mAh, sạc nhanh 65W, Snapdragon 7 Gen 3.',        '256GB, 12GB RAM, Snapdragon 7 Gen 3, 6500mAh', false],
        ['Nubia V90',           'Nubia',   8990000,  25, 'nubia_v90.png',          'Tầm trung giá tốt, pin 5000mAh bền bỉ, màn hình Full HD+ 90Hz.',         '128GB, 8GB RAM, Snapdragon 695, 90Hz', false],
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, description, specs, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    echo "✅ Inserted " . count($products) . " products<br>";

    // Default News
    $pdo->exec("INSERT INTO news (title, content, tags) VALUES 
        ('Chào mừng bạn đến với NHK Mobile', 'Cửa hàng chuyên cung cấp các sản phẩm công nghệ cao cấp...', 'Apple, Samsung, Event'),
        ('iPhone 17 Pro Max - Siêu phẩm AI', 'Trải nghiệm AI đỉnh cao với camera thông minh...', 'iPhone, Apple, AI'),
        ('Samsung S25 Ultra - Màn hình vô cực', 'Màn hình AMOLED 120Hz tuyệt đẹp...', 'Samsung, Android')");
    echo "✅ Inserted 3 news articles<br>";

    $pdo->commit();
    
    echo "<br><hr>";
    echo "<h2>✅ DATABASE RESET SUCCESSFUL!</h2><br>";
    echo "<div style='background:#f8f9fa; padding:20px; border-radius:10px; margin:20px 0;'>";
    echo "<h3>🔐 Tài khoản:</h3>";
    echo "<p><strong>Admin:</strong> username: <code>admin</code> | password: <code>admin123</code></p>";
    echo "<p><strong>User:</strong> email: <code>test@test.com</code> | password: <code>Test123!</code></p>";
    echo "</div>";
    echo "<p>📦 Products: 27 sản phẩm</p>";
    echo "<p>📰 News: 3 bài viết</p>";
    echo "<p>🛒 Orders: 0 (sạch)</p>";
    echo "<p>⭐ Reviews: 0 (sạch)</p>";
    echo "<p>🎫 Warranties: 0 (sạch)</p>";
    echo "<br><a href='index.php' style='background:#007AFF; color:white; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold;'>🚀 Go to Website</a>";
    echo " | <a href='login.php' style='background:#6c757d; color:white; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold;'>🔑 Login</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    die("<br><strong>❌ ERROR DURING RESET:</strong> " . $e->getMessage());
}
?>
