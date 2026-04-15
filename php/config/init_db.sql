-- NHK MOBILE - INITIAL DATABASE SCHEMA 2026
-- Compatible with PostgreSQL (Render/Local)
-- CLEAN DATA: 1 Admin, 1 Test User, 5 Products, 3 News

-- 1. DROP EXISTING TABLES
DROP TABLE IF EXISTS password_resets CASCADE;
DROP TABLE IF EXISTS repair_history CASCADE;
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS cart_items CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS wishlists CASCADE;
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
    phone VARCHAR(20),
    address TEXT,
    username VARCHAR(50),
    last_password_reset TIMESTAMP,
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
    id          SERIAL PRIMARY KEY,
    product_id  INT REFERENCES products(id) ON DELETE SET NULL,
    order_id    INT REFERENCES orders(id) ON DELETE SET NULL,
    imei        VARCHAR(20) UNIQUE NOT NULL,
    customer_name  VARCHAR(255),
    customer_phone VARCHAR(20),
    expires_at  DATE,
    status      VARCHAR(50) DEFAULT 'Active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE repair_history (
    id          SERIAL PRIMARY KEY,
    warranty_id INT REFERENCES warranties(id) ON DELETE CASCADE,
    repair_date DATE NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    location    VARCHAR(255),
    repair_id   VARCHAR(50),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wishlists (
    id         SERIAL PRIMARY KEY,
    user_id    INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, product_id)
);

CREATE TABLE password_resets (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reset_token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE news (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    tags VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. SEED DATA (CLEAN - Chỉ để lại 1 admin + 1 user test)

-- Default Admin: admin / admin123
INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$Zs8VJhGqKX7nF6YqJ3mHLe.aB2cD3eF4gH5iJ6kL7mN8oP9qR0sT');

-- Test User: test@test.com / Test123!
INSERT INTO users (fullname, email, password, status, phone, address) 
VALUES ('Test User', 'test@test.com', '$2y$10$Yx9WkIhHpL8mG7zK4nOPu.vC3dE4fG5hI6jK7lM8nO9pQ0rR1sU', 'active', '0901234567', '123 Đường Test, Quận 1, TP.HCM');

-- Default Products (5 sản phẩm)
INSERT INTO products (name, category, price, stock, image, description, is_featured) VALUES
('iPhone 17 Pro Max', 'Apple', 32990000, 50, 'ai_ip17_pm.png', 'Siêu phẩm AI thế hệ mới.', TRUE),
('Samsung S25 Ultra', 'Samsung', 29490000, 30, 'ai_s25_ultra.png', 'Đỉnh cao màn hình vô cực.', TRUE),
('Xiaomi 17 Ultra', 'Xiaomi', 24500000, 15, 'ai_mi17_ultra.png', 'Camera Leica thế hệ 4.', TRUE),
('OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus13.png', 'Mượt mà nhất phân khúc.', FALSE),
('iPhone 16e', 'Apple', 19990000, 25, 'ai_ip16e.png', 'iPhone nhỏ gọn thế hệ mới nhất.', FALSE);

-- Default News (3 bài viết)
INSERT INTO news (title, content, tags) VALUES
('Chào mừng bạn đến với NHK Mobile', 'Cửa hàng chuyên cung cấp các sản phẩm công nghệ cao cấp...', 'Apple, Samsung, Event'),
('iPhone 17 Pro Max - Siêu phẩm AI', 'Trải nghiệm AI đỉnh cao với camera thông minh...', 'iPhone, Apple, AI'),
('Samsung S25 Ultra - Màn hình vô cực', 'Màn hình AMOLED 120Hz tuyệt đẹp...', 'Samsung, Android');

-- NO orders, NO warranties, NO reviews - CLEAN DATA!
