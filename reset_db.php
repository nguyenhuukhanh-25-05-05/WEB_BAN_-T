<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Database - NHK Mobile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 50px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #d9534f; }
        .btn { display: inline-block; padding: 15px 30px; background: #d9534f; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
        .btn:hover { background: #c9302c; }
        .btn-success { background: #5cb85c; }
        .btn-success:hover { background: #4cae4c; }
        .warning { background: #fcf8e3; border: 1px solid #f0ad4e; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d9edf7; border: 1px solid #5bc0de; padding: 15px; border-radius: 5px; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Reset Database</h1>
        
        <div class="warning">
            <strong>Cảnh báo:</strong> Hành động này sẽ XÓA TẤT CẢ dữ liệu hiện tại và tạo lại database sạch!
        </div>

        <div class="info">
            <h3>Sau khi reset, bạn sẽ có:</h3>
            <ul>
                <li><strong>Admin:</strong> <code>admin</code> / <code>admin123</code></li>
                <li><strong>User:</strong> <code>test@test.com</code> / <code>Test123!</code></li>
                <li><strong>Products:</strong> 5 sản phẩm mẫu</li>
                <li><strong>News:</strong> 3 bài viết</li>
                <li><strong>Orders:</strong> 0 (sạch)</li>
                <li><strong>Reviews:</strong> 0 (sạch)</li>
            </ul>
        </div>

        <p><strong>Bạn có chắc chắn muốn tiếp tục?</strong></p>

        <a href="?confirm=yes&action=reset" class="btn">🗑️ Reset Ngay</a>
        <a href="index.php" class="btn btn-success">↩️ Quay về trang chủ</a>

        <?php
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes' && isset($_GET['action']) && $_GET['action'] === 'reset') {
            echo '<hr><h2>Đang xử lý...</h2>';
            
            try {
                require_once 'includes/db.php';
                $pdo->beginTransaction();

                echo "<h3>✅ Đang xóa tables cũ...</h3><ul>";
                
                // Drop tất cả tables
                $tables = [
                    'order_items', 'orders', 'cart_items', 'reviews', 
                    'wishlists', 'repair_history', 'warranties', 
                    'password_resets', 'products', 'users', 'admins', 'news'
                ];

                foreach ($tables as $table) {
                    $pdo->exec("DROP TABLE IF EXISTS $table CASCADE");
                    echo "<li>✅ Đã xóa: $table</li>";
                }
                echo "</ul>";

                echo "<h3>✅ Đang tạo tables mới...</h3><ul>";

                // Tạo lại tất cả tables
                $pdo->exec("CREATE TABLE admins (
                    id SERIAL PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL
                )");
                echo "<li>✅ Created: admins</li>";

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
                echo "<li>✅ Created: users</li>";

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
                echo "<li>✅ Created: products</li>";

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
                echo "<li>✅ Created: orders</li>";

                $pdo->exec("CREATE TABLE order_items (
                    id SERIAL PRIMARY KEY,
                    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
                    product_id INT REFERENCES products(id) ON DELETE SET NULL,
                    product_name VARCHAR(255),
                    quantity INT NOT NULL,
                    price DECIMAL(15,2) NOT NULL
                )");
                echo "<li>✅ Created: order_items</li>";

                $pdo->exec("CREATE TABLE cart_items (
                    id SERIAL PRIMARY KEY,
                    session_id VARCHAR(255),
                    user_id INT REFERENCES users(id) ON DELETE CASCADE,
                    product_id INT REFERENCES products(id) ON DELETE CASCADE,
                    quantity INT DEFAULT 1,
                    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE (session_id, product_id)
                )");
                echo "<li>✅ Created: cart_items</li>";

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
                echo "<li>✅ Created: reviews</li>";

                $pdo->exec("CREATE TABLE wishlists (
                    id SERIAL PRIMARY KEY,
                    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE (user_id, product_id)
                )");
                echo "<li>✅ Created: wishlists</li>";

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
                echo "<li>✅ Created: warranties</li>";

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
                echo "<li>✅ Created: repair_history</li>";

                $pdo->exec("CREATE TABLE password_resets (
                    id SERIAL PRIMARY KEY,
                    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                    reset_token VARCHAR(255) NOT NULL UNIQUE,
                    expires_at TIMESTAMP NOT NULL,
                    is_used BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                echo "<li>✅ Created: password_resets</li>";

                $pdo->exec("CREATE TABLE news (
                    id SERIAL PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT,
                    image VARCHAR(255),
                    tags VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                echo "<li>✅ Created: news</li>";
                
                echo "</ul>";

                // Insert dữ liệu mẫu
                echo "<h3>✅ Đang thêm dữ liệu mẫu...</h3><ul>";

                // Admin
                $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)")->execute(['admin', $adminPass]);
                echo "<li>✅ Admin: admin / admin123</li>";

                // User
                $userPass = password_hash('Test123!', PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users (fullname, email, password, status, phone, address) VALUES (?, ?, ?, 'active', '0901234567', '123 Đường Test, Quận 1, TP.HCM')")->execute(['Test User', 'test@test.com', $userPass]);
                echo "<li>✅ User: test@test.com / Test123!</li>";

                // Products
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
                echo "<li>✅ 5 Products</li>";

                // News
                $pdo->exec("INSERT INTO news (title, content, tags) VALUES 
                    ('Chào mừng bạn đến với NHK Mobile', 'Cửa hàng chuyên cung cấp các sản phẩm công nghệ cao cấp...', 'Apple, Samsung, Event'),
                    ('iPhone 17 Pro Max - Siêu phẩm AI', 'Trải nghiệm AI đỉnh cao với camera thông minh...', 'iPhone, Apple, AI'),
                    ('Samsung S25 Ultra - Màn hình vô cực', 'Màn hình AMOLED 120Hz tuyệt đẹp...', 'Samsung, Android')");
                echo "<li>✅ 3 News articles</li>";
                
                echo "</ul>";

                $pdo->commit();
                
                echo "<hr><div style='background:#dff0d8; padding:20px; border-radius:5px;'>";
                echo "<h2>✅ DATABASE RESET THÀNH CÔNG!</h2>";
                echo "<h3>🔐 Tài khoản:</h3>";
                echo "<p><strong>Admin:</strong> <code>admin</code> / <code>admin123</code></p>";
                echo "<p><strong>User:</strong> <code>test@test.com</code> / <code>Test123!</code></p>";
                echo "<p><strong>Products:</strong> 5 | <strong>News:</strong> 3 | <strong>Orders:</strong> 0</p>";
                echo "<br><a href='index.php' class='btn btn-success'>🚀 Vào Website</a>";
                echo "<a href='login.php' class='btn'>🔑 Đăng nhập</a>";
                echo "</div>";

            } catch (Exception $e) {
                $pdo->rollBack();
                echo "<div style='background:#f2dede; padding:20px; border-radius:5px; color:#a94442;'>";
                echo "<h2>❌ LỖI:</h2>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
