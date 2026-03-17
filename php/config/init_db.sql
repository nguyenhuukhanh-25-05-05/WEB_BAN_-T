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
    is_featured BOOLEAN DEFAULT FALSE, -- Thêm cờ sản phẩm nổi bật
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
    status VARCHAR(20) DEFAULT 'active', -- Thêm trạng thái: active, banned
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
ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50);

-- Migration: Thêm cột is_featured vào bảng products nếu chưa có
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_featured BOOLEAN DEFAULT FALSE;
-- Tạo bảng news để lưu bài viết công nghệ
CREATE TABLE IF NOT EXISTS news (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content TEXT,
    image VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cập nhật cấu trúc Cart Items (Cho phép liên kết với user_id thay vì chỉ session_id)
ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS user_id INT REFERENCES users(id);

-- Thêm bảng Bảo hành (Warranties)
CREATE TABLE warranties (
    id SERIAL PRIMARY KEY,
    imei VARCHAR(50) UNIQUE NOT NULL,
    product_id INT REFERENCES products(id),
    order_id INT REFERENCES orders(id),
    status VARCHAR(50) DEFAULT 'Active', -- Active, Expired, Voided
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Thêm bảng Đăng ký nhận tin (Subscribers)
CREATE TABLE subscribers (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dữ liệu mẫu Admin (Mật khẩu sẽ được hash bởi init-db.php)
-- Tài khoản mặc định: nhk_admin / nhk@2026
INSERT INTO admins (username, password) VALUES ('nhk_admin', 'nhk@2026') ON CONFLICT (username) DO NOTHING;
-- Tài khoản phụ: admin / admin123
INSERT INTO admins (username, password) VALUES ('admin', 'admin123') ON CONFLICT (username) DO NOTHING;

-- Dữ liệu mẫu Subscribers
INSERT INTO subscribers (email) VALUES ('khachhang1@gmail.com') ON CONFLICT (email) DO NOTHING;
INSERT INTO subscribers (email) VALUES ('nguoihammocongnghe@yahoo.com') ON CONFLICT (email) DO NOTHING;

-- Dữ liệu mẫu Sản phẩm (Sửa tên tệp ảnh có dấu cách)
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
('Honor Magic 10', 'Honor', 17500000, 15, 'honor_magic10.png', 'Công nghệ bảo vệ mắt và camera AI thông minh.'),
('Honor Magic 9', 'Honor', 14500000, 25, 'honor_magic9.png', 'Thiết kế sang trọng với mặt lưng lấp lánh.'),
('Realme GT9 Pro', 'Realme', 14900000, 30, 'realme_gt9.png', 'Tốc độ tối thượng, sạc nhanh nhất thế giới.'),
('Realme GT8 Pro', 'Realme', 12500000, 40, 'realme_gt8pro.png', 'Cân mọi tác vụ nặng với Snapdragon 8 gen mới.'),
('Nubia RedMagic 15', 'Nubia', 21900000, 10, 'nubia_magic15.png', 'Chiến thần gaming với quạt tản nhiệt tích hợp.'),
('Nubia V1000', 'Nubia', 7500000, 50, 'nubia_v1000.png', 'Điện thoại tầm trung pin trâu 6000mAh.'),
('Nubia V90', 'Nubia', 5900000, 80, 'nubia_v90.png', 'Sự lựa chọn ổn định cho các tác vụ cơ bản.');

-- Dữ liệu mẫu Tin tức (Tech News)
INSERT INTO news (title, excerpt, content, image, category) VALUES 
('iPhone 17 Pro: Bước nhảy vọt về AI', 'Apple dự kiến sẽ ra mắt dòng iPhone 17 với chip A19 Pro tập trung hoàn toàn vào xử lý trí tuệ nhân tạo.', 'Apple đang chuẩn bị cho một cuộc cách mạng tiếp theo với iPhone 17 Pro. Nguồn tin cho biết chip A19 Pro sẽ có Neural Engine thế hệ mới mạnh gấp 2 lần hiện tại, cho phép xử lý các tác vụ AI phức tạp ngay trên thiết bị mà không cần internet.', 'ai_ip17_pm.png', 'Apple'),
('Samsung S25 Ultra và màn hình Dynamic AMOLED 3X', 'Màn hình mới của Samsung hứa hẹn độ sáng vượt ngưỡng 3000 nits và tiết kiệm pin hơn 20%.', 'Hãng điện tử Hàn Quốc vừa hé lộ công nghệ màn hình Dynamic AMOLED 3X trên S25 Ultra. Điểm nhấn là công nghệ chống chói mới giúp hiển thị rõ nét ngay dưới ánh nắng gắt, đồng thời giảm lượng điện tiêu thụ đáng kể.', 'ai_s25_ultra.png', 'Samsung'),
('Xiaomi Mix Flip: Định nghĩa lại dòng Flip', 'Xiaomi Mix Flip sở hữu màn hình ngoài lớn nhất thị trường kèm hiệu năng cực đỉnh.', 'Không chỉ là một món đồ thời trang, Mix Flip của Xiaomi mang đến sức mạnh của Snapdragon 8 Gen 4. Màn hình ngoài 4 inch cho phép người dùng sử dụng hầu hết các ứng dụng mà không cần mở máy.', 'ai_mi_flip.png', 'Xiaomi'),
('OnePlus 15: Sạc nhanh 150W trở lại', 'OnePlus tiếp tục dẫn đầu cuộc đua tốc độ với công nghệ sạc SuperVOOC thế hệ mới.', 'OnePlus 15 không chỉ mượt ở phần mềm mà còn thần tốc ở phần cứng. Viên pin 5500mAh chỉ mất 15 phút để sạc đầy 100%, một con số kỷ lục trong làng smartphone cao cấp hiện nay.', 'oneplus15.png', 'OnePlus'),
('Kỷ nguyên AI trên di động năm 2026', 'Mọi hãng điện thoại lớn đều đang dồn toàn lực vào AI để thay đổi cách chúng ta sử dụng smartphone.', 'Năm 2026 được coi là bản lề của AI di động. Các trợ lý ảo không còn chỉ trả lời câu hỏi mà đã có thể hiểu ngữ cảnh, tự động sắp xếp lịch trình và chỉnh sửa hình ảnh chuyên nghiệp theo ý muốn của người dùng.', 'ai_ip16e.png', 'Technology'),
('Top 5 smartphone đáng mua nhất quý 1/2026', 'Cùng NHK Mobile điểm danh những mẫu flagship hot nhất đầu năm nay.', 'Thị trường đầu năm 2026 vô cùng sôi động với sự góp mặt của iPhone 17 series và S25 series. Nếu bạn cần camera đỉnh cao, S25 Ultra là lựa chọn số 1, còn nếu yêu thích sự ổn định và AI mượt mà, iPhone 17 Pro Max là cái tên không thể bỏ qua.', 'ai_ip16_pro.png', 'Review');
