# NHẬT KÝ SỬA LỖI & CẬP NHẬT HỆ THỐNG (NHK MOBILE)

Tài liệu này tổng hợp toàn bộ các lỗi đã gặp và các tính năng mới đã được triển khai trong phiên làm việc này.

---

## 1. CÁC LỖI CƠ SỞ DỮ LIỆU (DATABASE)

### ❌ Lỗi "Duplicate table: warranties"
- **Hiện tượng:** Không thể chạy file `init-db.php` do bảng đã tồn tại.
- **Khắc phục:** Thêm từ khóa `IF NOT EXISTS` vào tất cả các lệnh tạo bảng trong `init_db.sql`.
- **Bài học:** Luôn dùng `IF NOT EXISTS` để kịch bản cài đặt có thể chạy lại nhiều lần mà không gây lỗi.

### ❌ Lỗi thiếu cột "is_featured" và "status"
- **Hiện tượng:** Trang chủ và trang đăng nhập báo lỗi `column does not exist`.
- **Khắc phục:** 
  - Bổ sung lệnh `ALTER TABLE products ADD COLUMN IF NOT EXISTS is_featured...`
  - Bổ sung lệnh `ALTER TABLE users ADD COLUMN IF NOT EXISTS status...`
- **Bài học:** Khi cập nhật code có dùng cột mới, phải chạy lệnh `ALTER TABLE` cho Database cũ.

---

## 2. CÁC LỖI GIAO DIỆN (UI/UX)

### ❌ Lỗi "Không bấm được gì trên điện thoại"
- **Hiện tượng:** Màn hình di động bị "đơ", không thể click vào nút hay menu.
- **Khắc phục:** Thêm `pointer-events: none` cho các lớp trang trí (`.hero-bg-gradient`, `.hero-image-glow`). Các lớp này có z-index cao đã chặn mọi cú chạm của người dùng.
- **Bài học:** Các thành phần trang trí mờ ảo phải có `pointer-events: none` để không cản trở tương tác.

### ❌ Lỗi "Tràn lề ngang & Khoảng trắng thừa"
- **Hiện tượng:** Trang web bị trượt ngang khi xem trên điện thoại, lòi khoảng trắng bên phải.
- **Khắc phục:** 
  - Thêm `overflow-x: hidden !important` cho `html, body`.
  - Khử lề âm của Bootstrap Row trên mobile.
- **Bài học:** Luôn kiểm soát chặt chẽ chiều ngang màn hình di động.

### ❌ Lỗi "Ảnh sản phẩm không hiển thị"
- **Hiện tượng:** Sản phẩm Honor Magic hiện ảnh mặc định "Phone".
- **Khắc phục:** Đổi tên file ảnh từ `honor magic9.png` (có dấu cách) thành `honor_magic9.png` (gạch dưới) để khớp với Database.
- **Bài học:** Tên file ảnh không nên có dấu cách.

---

## 3. CÁC LỖI HỆ THỐNG & BẢO MẬT

### ❌ Lỗi "GitHub Push Protection"
- **Hiện tượng:** GitHub từ chối lệnh `push` vì phát hiện API Key của Grok nằm trong code.
- **Khắc phục:** 
  - Xóa API Key cứng khỏi `ai-chat.php`.
  - Chuyển sang dùng biến môi trường `getenv('XAI_API_KEY')`.
- **Bài học:** Không bao giờ để lộ mật khẩu hoặc API Key trong mã nguồn đẩy lên GitHub.

### ❌ Lỗi "Phiên đăng nhập bị thoát liên tục"
- **Hiện tượng:** Admin đang làm việc thì bị đá ra trang login.
- **Khắc phục:** Cấu hình lại `session.gc_maxlifetime` lên 7 ngày trong `auth_functions.php`.
- **Bài học:** Cần kéo dài thời gian Session cho các trang quản trị.

### ❌ Lỗi "Kẹt nút icon người dùng trên mobile"
- **Hiện tượng:** Icon người dùng (đăng nhập/đăng ký) rất khó bấm hoặc không phản hồi trên màn hình nhỏ.
- **Khắc phục:** 
  - Tăng diện tích chạm (hit area) cho icon bằng cách thêm padding và `min-width/height`.
  - Tăng `z-index` cho dropdown để đảm bảo nó luôn nằm trên các thành phần khác.
- **Bài học:** Các nút bấm trên mobile cần có diện tích tối thiểu 40x40px để ngón tay dễ dàng thao tác.

### ❌ Lỗi "Biến dạng nút Tài khoản (Person icon)"
- **Hiện tượng:** Nút tài khoản xuất hiện mũi tên xanh, gạch chân và một ô vuông trắng có mũi tên lên xuống (giống spinner của input number).
- **Khắc phục:** 
  - Sử dụng `appearance: none` và `-webkit-appearance: none` để xóa bỏ mọi định dạng mặc định của trình duyệt.
  - Thêm `display: inline-flex` và xóa bỏ `text-decoration` để icon sạch sẽ.
  - Dùng bộ chọn CSS ưu tiên cao để ẩn triệt để mũi tên (caret) của Bootstrap.
- **Bài học:** Các phần tử `dropdown-toggle` đôi khi bị trình duyệt hiểu nhầm hoặc bị CSS khác ghi đè, cần thiết lập thuộc tính hiển thị cơ bản một cách chặt chẽ.

### ❌ Lỗi "Navbar PC bị rời rạc (Logo trái, Icon phải)"
- **Hiện tượng:** Các thành phần trên Navbar (Logo, Menu, Icon) bị đẩy ra xa nhau, không đồng nhất và trông không thẩm mỹ.
- **Khắc phục:** 
  - Loại bỏ hoàn toàn kiểu bố trí "Logo trái - Menu giữa - Icon phải".
  - Chuyển sang dùng `navbar-centered-wrapper` với `display: flex`, `justify-content: center` và `gap: 30px`.
  - Phẳng hóa cấu trúc HTML (flatten HTML structure) để mọi phần tử (Logo, từng Link, từng Icon) đều là con trực tiếp của wrapper, giúp khoảng cách `gap` được chia đều tuyệt đối giữa tất cả chúng.
  - Áp dụng tương tự cho cả Mobile để tạo sự đồng bộ, thay vì dùng `space-between` như trước.
- **Bài học:** Thiết kế hiện đại (Modern/Apple Style) thường ưu tiên sự tập trung vào giữa màn hình để người dùng dễ quan sát trên các màn hình siêu rộng (Ultra-wide).

---

## 4. TÍNH NĂNG MỚI ĐÃ THÊM

1. **AI Chatbot (Grok xAI):** Trợ lý ảo tư vấn sản phẩm và trả góp dựa trên dữ liệu thực tế của kho hàng.
2. **Mua trả góp 0%:** Hệ thống tự động phân loại và đánh dấu đơn hàng trả góp.
3. **Quản trị Admin nâng cao:**
   - Cho phép chọn nhiều sản phẩm để xóa cùng lúc.
   - Hiển thị ảnh sản phẩm ngay trong danh sách đơn hàng để dễ duyệt.
   - Xuất file CSV báo cáo chuẩn Excel (có dấu, định dạng tiền tệ).
4. **Responsive Toàn diện:** Tối ưu hóa Navbar, Footer và các Section cho mọi loại màn hình (iPhone 8, Tablet, Desktop).

---

## 5. CẬP NHẬT NGÀY 2026-04-03

- **Tối ưu hóa SEO:** Bổ sung các thẻ meta description và keyword trong `header.php` để tăng khả năng hiển thị trên công cụ tìm kiếm.
- **Cập nhật giao diện Trang chủ:** Thay đổi nội dung Hero Section để làm mới thông điệp truyền thông ("Thế hệ AI 2026").
- **Cải thiện hiển thị sản phẩm:** Thêm nhãn (badge) "Hot Deal" cho các sản phẩm nổi bật để thu hút người dùng.
- **Bảo trì hệ thống:** Kiểm tra và dọn dẹp các tệp tin rác, kiểm tra tính toàn vẹn của cơ sở dữ liệu.
102: 
103: ---
104: 
105: ## 6. CẬP NHẬT NGÀY 2026-04-08
106: 
107: ### ❌ Lỗi "Undefined column: is_installment"
108: - **Hiện tượng:** Khách hàng không thể thanh toán, báo lỗi thiếu cột trong bảng `orders`.
109: - **Khắc phục:** 
110:   - Tạo file `patch_db_installment.php` để bổ sung cột vào database hiện tại.
111:   - Cập nhật `init_db.sql` để đồng bộ schema cho các bản cài đặt mới.
112: - **Bài học:** Khi thêm tính năng mới (như trả góp) cần đảm bảo Migration script chạy thành công trên mọi môi trường.
113: 
114: ### ❌ Lỗi "Foreign key violation: warranties_order_id_fkey"
115: - **Hiện tượng:** Admin báo lỗi SQL khó hiểu khi nhập sai Mã đơn hàng trong trang Bảo hành.
116: - **Khắc phục:** 
117:   - Thêm bộ lọc mã lỗi PDO (`23503`, `23505`) trong `admin/warranties.php`.
118:   - Hiển thị thông báo tiếng Việt dễ hiểu: "Mã đơn hàng không tồn tại".
119: - **Bài học:** Luôn "bọc" các lỗi SQL bằng thông báo thân thiện với người dùng cuối.
120: 
121: ### ❌ Lỗi "Giao diện Mobile bị chật chội (Navbar phèn)"
122: - **Hiện tượng:** Trên màn hình điện thoại, menu và các nút hành động dẫm đạp lên nhau, logo bị méo.
123: - **Khắc phục:** 
124:   - Triển khai **Hamburger Menu** và **Offcanvas Drawer** cho mobile.
125:   - Ẩn các liên kết ít dùng trên mobile và đưa vào menu ngăn kéo.
126:   - Refine lại Footer: Căn giữa nội dung, thu gọn danh sách liên kết để tránh cuộn trang quá dài.
127:   - Áp dụng hiệu ứng **Glassmorphism** cho menu mobile để tăng độ sang trọng.
128: - **Bài học:** Thiết kế mobile phải ưu tiên sự thông thoáng và diện tích chạm (tap target).
129: 
130: ### ❌ Lỗi "Các nút Dashboard không hoạt động (Báo cáo & Thêm mới)"
131: - **Hiện tượng:** Click vào nút Báo cáo và Thêm mới ở đầu trang Dashboard không có phản hồi.
132: - **Khắc phục:** 
133:   - Kết nối nút **Báo cáo** với `export_stats.php` để tải file CSV thống kê.
134:   - Chuyển nút **Thêm mới** thành dạng **Dropdown** với các lựa chọn: Thêm sản phẩm, Thêm tin tức, Kích hoạt bảo hành.
135: - **Bài học:** Không để lại các thành phần giao diện "chết" (static placeholders) trong môi trường production.
136: 
137: ### 🔒 Bảo mật & Tối ưu: "Tách biệt logic xử lý (JS Separation)"
138: - **Vấn đề:** Mã xử lý đánh giá (AJAX fetch) nằm trực tiếp trong file HTML, gây rối mắt và làm người dùng lầm tưởng bị lộ toàn bộ logic.
139: - **Khắc phục:** 
140:   - Tách toàn bộ JS trong `product-detail.php` ra file `assets/js/product-reviews.js`.
141:   - Link file JS bên ngoài vào HTML.
142: - **Kết quả:** Code HTML sạch sẽ, logic được gom nhóm chuyên nghiệp, khó bị soi trực tiếp qua tab Elements.
143: 
144: ### 🛠️ Lỗi & UI: "Lịch sử đơn hàng & Trang cảm ơn"
145: - **Lỗi:** Cảnh báo "user_fullname" chưa được định nghĩa trong `order_history.php` do session không đồng bộ.
146: - **Khắc phục:** 
147:   - Sử dụng hàm `get_logged_in_name()` để lấy tên an toàn, tránh lỗi cảnh báo PHP.
148:   - Thiết kế lại trang **Đặt hàng thành công** thành dạng Card sang trọng, bổ sung icon hoạt họa và thông tin hướng dẫn rõ ràng.
149: - **Kết quả:** Giao diện đặt hàng chuyên nghiệp, không còn lỗi giao diện hiển thị tên người dùng.
150: 
151: ### 📱 Giao diện: "Tối ưu Footer Mobile (Accordion Style)"
152: - **Vấn đề:** Footer trên di động hiển thị dàn trải hoặc bị ẩn bớt nội dung, thiếu tính cao cấp.
153: - **Khắc phục:** 
154:   - Thiết kế lại theo phong cách tiêu chuẩn của Apple với tính năng **Accordion (Thu gọn/Mở rộng)**.
155:   - Khôi phục tất cả các cột nội dung bị ẩn trước đó.
156:   - Bo góc và làm mới các biểu tượng mạng xã hội (Social Icons) cho sang trọng hơn.
157: - **Kết quả:** Footer tinh gọn, dễ tương tác và mang lại cảm giác cao cấp trên mọi kích thước màn hình.

### 📦 Chức năng: "Tra cứu đơn hàng không cần đăng nhập"
- **Vấn đề:** Khách hàng sau khi đặt (đặc biệt là khách vãng lai) không có chỗ để kiểm tra tình trạng đơn hàng.
- **Giải pháp:** 
  - Tạo trang `track_order.php` cho phép tìm kiếm đơn hàng bằng **Mã đơn** + **Số điện thoại**.
  - Tích hợp link tra cứu vào Chân trang (Footer).
  - Thêm nút **"Theo dõi đơn hàng"** ngay tại trang Hoàn tất thanh toán để khách lưu lại mã.
- **Kết quả:** Tăng sự tin tưởng và trải nghiệm người dùng, giảm thiểu các câu hỏi về tình trạng đơn hàng.
