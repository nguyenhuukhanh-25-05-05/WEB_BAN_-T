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

    // Featured Products
    $products = [
        ['iPhone 17 Pro Max', 'Apple', 32990000, 50, 'ai_ip17_pm.png', 'Siêu phẩm AI thế hệ mới.', true],
        ['Samsung S25 Ultra', 'Samsung', 29490000, 30, 'ai_s25_ultra.png', 'Đỉnh cao màn hình vô cực.', true],
        ['Xiaomi 17 Ultra', 'Xiaomi', 24500000, 15, 'ai_mi17_ultra.png', 'Camera Leica thế hệ 4.', true],
        ['OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus13.png', 'Mượt mà nhất phân khúc.', false],
        ['iPhone 16e', 'Apple', 19990000, 25, 'ai_ip16e.png', 'iPhone nhỏ gọn thế hệ mới nhất.', false]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    echo "✅ Inserted 5 products<br>";

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
    echo "<p>📦 Products: 5 sản phẩm</p>";
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
