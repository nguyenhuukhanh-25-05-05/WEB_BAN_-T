-- Database Schema cho NHK Mobile (PostgreSQL)
-- Lưu ý: Trong PostgreSQL, bạn cần tạo database trước nếu chưa có

-- Bảng sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(15, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng đơn hàng
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    total_price DECIMAL(15, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending, Processing, Completed, Cancelled
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dữ liệu mẫu (Seed data)
INSERT INTO products (name, category, price, stock, image) VALUES 
('iPhone 17 Pro Max', 'Apple', 32990000, 45, 'ai_ip17_pm.png'),
('Samsung Galaxy S25 Ultra', 'Samsung', 29490000, 30, 's25_ultra.png');
