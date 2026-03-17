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

-- 5. Bảng User (Khách hàng)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Bảng Admin (Lưu trữ tài khoản quản trị)
CREATE TABLE IF NOT EXISTS admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cập nhật cấu trúc Orders (Thêm user_id để theo dõi lịch sử)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);

-- Cập nhật cấu trúc Cart Items (Cho phép liên kết với user_id thay vì chỉ session_id)
ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);

-- Dữ liệu mẫu Admin (Sử dụng để quản trị cửa hàng)
-- Tài khoản mặc định: nhk_admin / nhk@2026
INSERT INTO admins (username, password) VALUES ('nhk_admin', 'nhk@2026') ON CONFLICT (username) DO NOTHING;
-- Bạn cũng có thể dùng tài khoản phụ: admin / admin123
INSERT INTO admins (username, password) VALUES ('admin', 'admin123') ON CONFLICT (username) DO NOTHING;

-- Dữ liệu mẫu Sản phẩm (Tất cả ảnh thật trong assets/images)
INSERT INTO products (name, category, price, stock, image, description) VALUES 
('iPhone 17 Pro Max', 'Apple', 32990000, 45, 'ai_ip17_pm.png', 'Siêu phẩm Apple 2026 với trí tuệ nhân tạo tích hợp sâu.'),
('iPhone 16 Pro', 'Apple', 28990000, 20, 'ai_ip16_pro.png', 'Thiết kế sang trọng, hiệu năng mạnh mẽ.'),
('iPhone 15 Pro Max', 'Apple', 22500000, 15, 'ai_ip15_pm.png', 'Mạnh mẽ và bền bỉ với khung viền Titan.'),
('iPhone 16e', 'Apple', 14900000, 30, 'ai_ip16e.png', 'Phiên bản đặc biệt, mỏng nhẹ và đầy màu sắc.'),
('Samsung Galaxy S25 Ultra', 'Samsung', 29490000, 30, 'ai_s25_ultra.png', 'Đỉnh cao công nghệ màn hình và camera từ Samsung.'),
('Samsung Galaxy S24 Ultra', 'Samsung', 23900000, 12, 'ai_s24_ultra.png', 'Siêu phẩm Galaxy AI đầu tiên.'),
('Samsung Galaxy S23', 'Samsung', 12500000, 25, 'ai_s23.png', 'Nhỏ gọn nhưng đầy nội lực.'),
('Xiaomi 17 Ultra', 'Xiaomi', 24500000, 8, 'ai_mi17_ultra.png', 'Quái thú nhiếp ảnh với cảm biến 1 inch thế hệ mới.'),
('Xiaomi 15T Pro', 'Xiaomi', 14200000, 40, 'ai_mi15t.png', 'Sức mạnh flagship với mức giá tầm trung.'),
('Xiaomi Mix Flip', 'Xiaomi', 21500000, 10, 'ai_mi_flip.png', 'Điện thoại gập nhỏ gọn, màn hình ngoài cực đại.'),
('Oppo Find X10 Pro', 'Oppo', 22900000, 15, 'oppo_findx10.png', 'Thiết kế độc bản, camera tele tiềm vọng đỉnh cao.'),
('Oppo K300', 'Oppo', 8900000, 100, 'oppo_k300.png', 'Hiệu năng mạnh mẽ cho game thủ tầm trung.'),
('Oppo Mix Flip 5090', 'Oppo', 19500000, 10, 'oppo_mixflip5090.png', 'Siêu phẩm nắp gập thời thượng.'),
('Vivo X300 Pro', 'Vivo', 20500000, 18, 'ai_vivo_x300.png', 'Bậc thầy chụp đêm và quay phim chuyên nghiệp.'),
('Vivo X200 (Black Edition)', 'Vivo', 16200000, 22, 'ai_vivo_x200_black.png', 'Mỏng nhẹ tinh tế, camera Zeiss đỉnh cao.'),
('OnePlus 15', 'OnePlus', 18900000, 15, 'oneplus15.png', 'Sát thủ Flagship với sạc siêu nhanh 150W.'),
('OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus13.png', 'Mượt mà vượt trội với OxygenOS.'),
('OnePlus 15R', 'OnePlus', 12900000, 35, 'oneplus15r.png', 'Hiệu suất cao cho phân khúc cận cao cấp.'),
('Honor Magic 10', 'Honor', 17500000, 15, 'honor magic10.png', 'Công nghệ bảo vệ mắt và camera AI thông minh.'),
('Honor Magic 9', 'Honor', 14500000, 25, 'honor magic9.png', 'Thiết kế sang trọng với mặt lưng lấp lánh.'),
('Realme GT9 Pro', 'Realme', 14900000, 30, 'realme_gt9.png', 'Tốc độ tối thượng, sạc nhanh nhất thế giới.'),
('Realme GT8 Pro', 'Realme', 12500000, 40, 'realme_gt8pro.png', 'Cân mọi tác vụ nặng với Snapdragon 8 gen mới.'),
('Nubia RedMagic 15', 'Nubia', 21900000, 10, 'nubia_magic15.png', 'Chiến thần gaming với quạt tản nhiệt tích hợp.'),
('Nubia V1000', 'Nubia', 7500000, 50, 'nubia_v1000.png', 'Điện thoại tầm trung pin trâu 6000mAh.'),
('Nubia V90', 'Nubia', 5900000, 80, 'nubia_v90.png', 'Sự lựa chọn ổn định cho các tác vụ cơ bản.');
