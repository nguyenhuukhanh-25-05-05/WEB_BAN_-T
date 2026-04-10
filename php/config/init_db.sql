-- NHK MOBILE - INITIAL DATABASE SCHEMA 2026
-- Compatible with PostgreSQL (Render/Local)

-- 1. DROP EXISTING TABLES
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS cart_items CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS warranties CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS admins CASCADE;
DROP TABLE IF EXISTS news CASCADE;

-- 2. CREATE TABLES

CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
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
);

CREATE TABLE orders (
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
);

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id) ON DELETE SET NULL,
    product_name VARCHAR(255),
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL
);

CREATE TABLE cart_items (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
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
);

CREATE TABLE warranties (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE SET NULL,
    imei VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(20),
    expires_at DATE,
    status VARCHAR(50) DEFAULT 'Active'
);

CREATE TABLE news (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    tags VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. SEED DATA

-- Default Admin: admin / 123
INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$8.K6..v.8.K6..v.8.K6..v.8.K6..v.8.K6..v.8.K6..v.');

-- Default Products
INSERT INTO products (name, category, price, stock, image, description, is_featured) VALUES 
('iPhone 17 Pro Max', 'Apple', 32990000, 50, 'ai_ip17_pm.png', 'Siêu phẩm AI thế hệ mới.', TRUE),
('Samsung S25 Ultra', 'Samsung', 29490000, 30, 'ai_s25_ultra.png', 'Đỉnh cao màn hình vô cực.', TRUE),
('Xiaomi 17 Ultra', 'Xiaomi', 24500000, 15, 'ai_mi17_ultra.png', 'Camera Leica thế hệ 4.', TRUE),
('OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus13.png', 'Mượt mà nhất phân khúc.', FALSE);
