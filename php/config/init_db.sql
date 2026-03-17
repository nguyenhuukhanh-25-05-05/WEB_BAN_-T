-- Database Schema cho NHK Mobile (PostgreSQL)

-- 1. Bảng sản phẩm (Lưu trữ thông tin điện thoại, phụ kiện)
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

-- 2. Bảng đơn hàng (Lưu trữ thông tin khách mua hàng)
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    total_price DECIMAL(15, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending, Processing, Completed, Cancelled
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bảng Chi tiết đơn hàng (Lưu trữ danh sách máy trong mỗi đơn)
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    product_name VARCHAR(255),
    price DECIMAL(15, 2),
    quantity INT
);

-- 4. Bảng Giỏ hàng bền vững (Lưu giỏ hàng theo session_id)
CREATE TABLE IF NOT EXISTS cart_items (
    id SERIAL PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT REFERENCES products(id),
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(session_id, product_id)
);

-- 5. Bảng Admin (Lưu trữ tài khoản quản trị)
CREATE TABLE IF NOT EXISTS admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dữ liệu mẫu Admin (Mặc định: admin / admin123 - Lưu ý nên băm password nếu dùng thật)
INSERT INTO admins (username, password) VALUES ('admin', 'admin123') ON CONFLICT (username) DO NOTHING;

-- Dữ liệu mẫu Sản phẩm (Seed data cực kỳ đầy đủ)
INSERT INTO products (name, category, price, stock, image, description) VALUES 
('iPhone 17 Pro Max', 'Apple', 32990000, 45, 'ai_ip17_pm.png', 'Siêu phẩm Apple 2026 với trí tuệ nhân tạo tích hợp sâu.'),
('iPhone 16 Pro', 'Apple', 28990000, 20, 'ip16_pro.png', 'Thiết kế sang trọng, hiệu năng mạnh mẽ.'),
('Samsung Galaxy S25 Ultra', 'Samsung', 29490000, 30, 's25_ultra.png', 'Đỉnh cao công nghệ màn hình và camera từ Samsung.'),
('Samsung Galaxy Z Fold 7', 'Samsung', 41000000, 10, 'zfold7.png', 'Điện thoại màn hình gập thế hệ mới nhất.'),
('Xiaomi 15 Pro', 'Xiaomi', 18500000, 50, 'xiaomi15.png', 'Flagship Xiaomi với sạc siêu nhanh và camera Leica.'),
('Oppo Find X8', 'Oppo', 17900000, 15, 'oppo_x8.png', 'Nghệ thuật nhiếp ảnh di động.'),
('iPad Pro M4', 'Tablet', 25500000, 25, 'ipad_m4.png', 'Máy tính bảng mạnh nhất thế giới hiện nay.'),
('AirPods Pro 3', 'Accessory', 5900000, 100, 'airpods3.png', 'Âm thanh không dây trung thực.'),
('Apple Watch Ultra 3', 'Watch', 21000000, 12, 'aw_ultra3.png', 'Đồng hồ thông minh dành cho người thích thám hiểm.');
