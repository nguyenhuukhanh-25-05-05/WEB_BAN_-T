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

### ❌ Lỗi "Tràn biểu tượng Navbar trên Mobile (< 350px)"
- **Hiện tượng:** Logo, tìm kiếm, giỏ hàng, tài khoản và nút menu bị ép sát, đè lên nhau trên các điện thoại nhỏ như iPhone 5/SE.
- **Khắc phục:** 
  - Ẩn biểu tượng Tài khoản (`d-xs-none`) trên màn hình dưới 350px.
  - Chuyển liên kết "Tài khoản / Đăng nhập" vào bên trong Menu Hamburger (`#navbarNav`) để không làm mất tính năng.
  - Giảm `gap` giữa các phần tử và kích thước logo trên thiết bị cực nhỏ.
- **Bài học:** Với các thanh điều hướng có nhiều icon, cần có chiến lược ẩn/hiện thông minh cho các màn hình siêu nhỏ.

### ❌ Lỗi "Navbar bị vỡ khung do chiều cao cố định"
- **Hiện tượng:** Khi nội dung bên trong Navbar (như badge giỏ hàng) làm dòng bị cao lên, nó sẽ lòi ra ngoài thanh điều hướng.
- **Khắc phục:** Chuyển `height: 54px` thành `min-height: 54px` trong CSS của `.navbar-premium`.
- **Bài học:** Hạn chế dùng `height` cố định cho các container chứa nội dung động; hãy dùng `min-height`.

### ❌ Lỗi "Z-index Sidebar Admin bị Header che khuất"
- **Hiện tượng:** Trên mobile, khi mở Sidebar admin, nó nằm phía dưới Header nên không thể tương tác hoàn toàn.
- **Khắc phục:** Tăng `z-index` cho `.sidebar.show` lên 1100 (cao hơn Header 1010).
- **Bài học:** Quản lý `z-index` theo lớp (Layering) là cực kỳ quan trọng trong giao diện Dashboard.

### 🛡️ Lỗ hổng bảo mật & Tối ưu UX (Phase 2)
- **XSS Search:** Đã sử dụng `htmlspecialchars()` để lọc từ khóa tìm kiếm trước khi hiện ra HTML trong `product.php`.
- **Open Redirect:** Đã kiểm tra tham số `redirect` trong `login.php` để ngăn chặn việc chuyển hướng đến trang web lạ.
- **AI Chat Context:** Giới hạn 30 sản phẩm mới nhất để tránh tràn bộ nhớ và tăng tốc độ phản hồi.
- **Search Typo:** Sửa lỗi chính tả "tìm m thấy" -> "tìm thấy".
- **Form Validation:** Thêm `type="tel"` và `minlength` cho form đăng ký.
- **Product UX:** Thêm nút "Quay lại" tại trang chi tiết giúp di chuyển nhanh hơn.

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

## 5. GIAI ĐOẠN HOÀN THIỆN CUỐI CÙNG (FINAL POLISH)

1. **Authentication Layout:**
   - Thêm nút chuyển đổi ẩn/hiện mật khẩu (Password Toggle) cho cả trang Đăng nhập và Đăng ký.
   - Thêm hiệu ứng focus và validation số điện thoại thời gian thực.
2. **AI Chatbot V2:**
   - Thêm hiệu ứng "Đang nhập..." (Typing indicator) để bot trông sinh động hơn.
   - Nâng cấp giao diện bóng bẩy, bo góc mềm mại và hiệu ứng Slide-in.
3. **Admin Dashboard Analytics:**
   - Thêm biểu đồ Sparkline mini cho doanh thu.
   - Thêm thẻ "Trạng thái hệ thống" với hiệu ứng nhịp tim (Pulse) thể hiện độ ổn định.
4. **Trải nghiệm người dùng toàn cầu:**
   - Tùy chỉnh thanh cuộn (Scrollbar) mỏng mịn kiểu Apple.
   - Thêm nút "Cuộn lên đầu trang" (Back to Top) tiện lợi.
   - Chuẩn hóa toàn bộ hiệu ứng chuyển cảnh (Transition) về 0.3s cubic-bezier.

---
*Ngày hoàn tất: 25/03/2026*
