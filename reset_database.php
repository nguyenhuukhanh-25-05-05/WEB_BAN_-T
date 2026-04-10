<?php
/**
 * NHK Mobile - Database Reset Utility
 * 
 * DANGER: This script will WIPE all data and reset the database to 
 * its initial state for testing.
 */
session_start();
require_once 'includes/db.php';

// Simple security check: Only allow if explicitly requested via GET or if admin is logged in
if (!isset($_GET['confirm']) && !isset($_SESSION['admin_id'])) {
    die("To reset the database, please visit: reset_database.php?confirm=yes");
}

try {
    $pdo->beginTransaction();

    echo "Cleaning up database...<br>";

    // 1. Drop all existing tables (Order matters due to Foreign Keys)
    $tables = [
        'order_items',
        'orders',
        'cart_items',
        'reviews',
        'warranties',
        'products',
        'users',
        'admins',
        'news'
    ];

    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table CASCADE");
        echo "- Dropped table: $table<br>";
    }

    echo "Recreating tables...<br>";

    // 2. Create Tables
    
    // Admins
    $pdo->exec("CREATE TABLE admins (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");

    // Users
    $pdo->exec("CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        status VARCHAR(20) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Products
    $pdo->exec("CREATE TABLE products (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
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

    // Order Items
    $pdo->exec("CREATE TABLE order_items (
        id SERIAL PRIMARY KEY,
        order_id INT REFERENCES orders(id) ON DELETE CASCADE,
        product_id INT REFERENCES products(id) ON DELETE SET NULL,
        product_name VARCHAR(255),
        quantity INT NOT NULL,
        price DECIMAL(15,2) NOT NULL
    )");

    // Cart Items
    $pdo->exec("CREATE TABLE cart_items (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES users(id) ON DELETE CASCADE,
        product_id INT REFERENCES products(id) ON DELETE CASCADE,
        quantity INT DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

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

    // Warranties
    $pdo->exec("CREATE TABLE warranties (
        id SERIAL PRIMARY KEY,
        product_id INT REFERENCES products(id) ON DELETE SET NULL,
        imei VARCHAR(20) UNIQUE NOT NULL,
        customer_name VARCHAR(255),
        customer_phone VARCHAR(20),
        expires_at DATE,
        status VARCHAR(50) DEFAULT 'Active'
    )");

    // News
    $pdo->exec("CREATE TABLE news (
        id SERIAL PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        image VARCHAR(255),
        tags VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Inserting seed data...<br>";

    // 3. Insert Seed Data
    
    // Default Admin (Password: 123)
    $adminPass = password_hash('123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)")->execute(['admin', $adminPass]);

    // Default User (Password: 123)
    $userPass = password_hash('123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)")->execute(['Khánh Nguyễn', 'khanh@gmail.com', $userPass]);

    // Featured Products
    $products = [
        ['iPhone 17 Pro Max', 'Apple', 32990000, 50, 'ai_ip17_pm.png', 'Siêu phẩm AI thế hệ mới.', true],
        ['Samsung S25 Ultra', 'Samsung', 29490000, 30, 'ai_s25_ultra.png', 'Đỉnh cao màn hình vô cực.', true],
        ['Xiaomi 17 Ultra', 'Xiaomi', 24500000, 15, 'ai_mi17_ultra.png', 'Camera Leica thế hệ 4.', true],
        ['OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus13.png', 'Mượt mà nhất phân khúc.', false]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }

    // Default News
    $pdo->exec("INSERT INTO news (title, content, tags) VALUES ('Chào mừng bạn đến với NHK Mobile', 'Cửa hàng chuyên cung cấp các sản phẩm công nghệ cao cấp...', 'Apple, Samsung, Event')");

    $pdo->commit();
    echo "<br><strong>DATABASE RESET SUCCESSFUL!</strong><br>";
    echo "Admin: admin / 123<br>";
    echo "User: khanh@gmail.com / 123<br>";
    echo "<a href='index.php'>Go to Website</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    die("<br><strong>ERROR DURING RESET:</strong> " . $e->getMessage());
}
