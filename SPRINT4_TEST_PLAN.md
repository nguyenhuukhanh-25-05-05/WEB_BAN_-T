# KẾ HOẠCH KIỂM THỬ – SPRINT 4
**Dự án:** NHK Mobile – Website Bán Điện Thoại  
**Sprint:** 4  
**Phiên bản:** 1.0  
**Ngày lập:** 21/04/2026  
**Nhóm thực hiện:** 3 thành viên  

---

## 1. TỔNG QUAN

### 1.1 Tính năng kiểm thử trong Sprint 4

| STT | Tính năng | Mô tả |
|-----|-----------|-------|
| 1 | 📄 Phân trang sản phẩm | Điều hướng trang, lọc theo thương hiệu + phân trang |
| 2 | 🤖 Chat AI hỗ trợ | Tích hợp xAI Grok API vào Live Chat widget |

### 1.2 Môi trường kiểm thử

| Thành phần | Thông tin |
|---|---|
| Hệ điều hành | Windows 10/11 |
| Web server | PHP 8.x |
| Database | PostgreSQL (Supabase) |
| Trình duyệt | Chrome, Firefox, Edge |
| AI API | xAI Grok (`grok-3-mini`) |

### 1.3 Tiêu chí hoàn thành
- Test case mức **Cao** PASS 100%
- Test case mức **Trung bình** PASS ≥ 90%
- Không còn bug **Critical/High** chưa xử lý

---

## 2. PHÂN CÔNG THÀNH VIÊN

| Thành viên | Phụ trách |
|---|---|
| **Thành viên 1** | Phân trang – Hiển thị & Điều hướng |
| **Thành viên 2** | Phân trang – Lọc theo thương hiệu & Kết hợp |
| **Thành viên 3** | Chat AI – Toàn bộ module |

---

## 3. TEST CASE CHI TIẾT

---

### 👤 THÀNH VIÊN 1 – Phân trang: Hiển thị & Điều hướng

**Phụ trách trang:** `product.php`  
**Tổng số TC:** 10

| TC ID | Tên Test Case | Bước thực hiện | Kết quả mong đợi | Mức độ | Kết quả |
|-------|-------------|----------------|------------------|--------|---------|
| TC1.01 | Hiển thị danh sách sản phẩm | Vào `product.php` | Danh sách sản phẩm hiển thị, không bị lỗi trắng trang | Cao | ⬜ |
| TC1.02 | Số sản phẩm trên mỗi trang | Đếm sản phẩm hiển thị trang đầu | Đúng số lượng quy định (VD: 9 hoặc 12 sản phẩm/trang) | Cao | ⬜ |
| TC1.03 | Nút "Trang tiếp theo" | Click nút Next / `?page=2` | Chuyển sang trang 2, nội dung thay đổi | Cao | ⬜ |
| TC1.04 | Nút "Trang trước" | Đang ở trang 2 → click Prev | Quay về trang 1 đúng nội dung | Cao | ⬜ |
| TC1.05 | Click số trang cụ thể | Click vào số trang bất kỳ (VD: 3) | Nhảy đúng đến trang đó | Cao | ⬜ |
| TC1.06 | Trang đầu – ẩn nút Prev | Đang ở trang 1 | Nút "Prev" bị ẩn hoặc disabled | Trung bình | ⬜ |
| TC1.07 | Trang cuối – ẩn nút Next | Đang ở trang cuối | Nút "Next" bị ẩn hoặc disabled | Trung bình | ⬜ |
| TC1.08 | URL phản ánh số trang | Chuyển sang trang 3 | URL có `?page=3` hoặc tương đương | Trung bình | ⬜ |
| TC1.09 | Trang không tồn tại | Truy cập `?page=9999` | Về trang 1 hoặc báo "Không có sản phẩm" | Cao | ⬜ |
| TC1.10 | Responsive phân trang | Xem trên mobile (≤ 768px) | Phân trang hiển thị gọn, không tràn màn hình | Trung bình | ⬜ |

---

### 👤 THÀNH VIÊN 2 – Phân trang: Lọc theo thương hiệu & Kết hợp

**Phụ trách trang:** `product.php?category=...`  
**Tổng số TC:** 10

| TC ID | Tên Test Case | Bước thực hiện | Kết quả mong đợi | Mức độ | Kết quả |
|-------|-------------|----------------|------------------|--------|---------|
| TC2.01 | Lọc theo Apple | Click danh mục Apple | Chỉ hiển thị sản phẩm thương hiệu Apple | Cao | ⬜ |
| TC2.02 | Lọc theo Samsung | Click danh mục Samsung | Chỉ hiển thị sản phẩm Samsung | Cao | ⬜ |
| TC2.03 | Lọc theo Xiaomi | Click danh mục Xiaomi | Chỉ hiển thị sản phẩm Xiaomi | Cao | ⬜ |
| TC2.04 | Lọc theo OPPO | Click danh mục OPPO | Chỉ hiển thị sản phẩm OPPO | Cao | ⬜ |
| TC2.05 | Lọc không có kết quả | Lọc thương hiệu chưa có sản phẩm | Thông báo "Không có sản phẩm" thân thiện | Trung bình | ⬜ |
| TC2.06 | Phân trang GIỮ bộ lọc | Lọc Apple → sang trang 2 | Trang 2 vẫn chỉ hiện sản phẩm Apple | Cao | ⬜ |
| TC2.07 | Tổng số trang đúng theo lọc | Lọc Samsung (ít sản phẩm hơn) | Số trang ít hơn khi xem tất cả | Cao | ⬜ |
| TC2.08 | Xóa bộ lọc / Xem tất cả | Click "Tất cả" hoặc xóa filter | Hiện lại toàn bộ sản phẩm | Cao | ⬜ |
| TC2.09 | URL khi lọc + phân trang | Lọc Apple, trang 2 | URL có cả `category=Apple&page=2` | Trung bình | ⬜ |
| TC2.10 | Reload giữ bộ lọc | Lọc Xiaomi trang 2 → F5 | Vẫn ở trang 2 danh mục Xiaomi | Trung bình | ⬜ |

---

### 👤 THÀNH VIÊN 3 – Chat AI (xAI Grok)

**Phụ trách:** `api/chat.php` + widget trong `includes/footer.php`  
**Tổng số TC:** 15

| TC ID | Tên Test Case | Bước thực hiện | Kết quả mong đợi | Mức độ | Kết quả |
|-------|-------------|----------------|------------------|--------|---------|
| TC3.01 | Mở cửa sổ chat | Click icon chat góc phải màn hình | Cửa sổ chat mở ra, ô nhập được focus tự động | Cao | ⬜ |
| TC3.02 | Đóng cửa sổ chat | Click nút X trên header chat | Cửa sổ đóng lại | Cao | ⬜ |
| TC3.03 | Đóng bằng phím Escape | Nhấn Escape khi chat đang mở | Cửa sổ đóng | Trung bình | ⬜ |
| TC3.04 | Gửi tin bằng nút Send | Nhập nội dung → click nút gửi | Tin nhắn hiện lên, AI phản hồi | Cao | ⬜ |
| TC3.05 | Gửi tin bằng phím Enter | Nhập nội dung → nhấn Enter | Tin nhắn được gửi đi | Cao | ⬜ |
| TC3.06 | Hiển thị typing indicator | Gửi tin → quan sát ngay sau | 3 chấm nhấp nháy hiện trong khi AI xử lý | Trung bình | ⬜ |
| TC3.07 | AI phản hồi thực tế | Hỏi: *"iPhone 17 Pro Max có những tính năng gì?"* | AI trả lời đúng nội dung về NHK Mobile, không phải câu ngẫu nhiên | Cao | ⬜ |
| TC3.08 | AI biết thông tin cửa hàng | Hỏi: *"Bảo hành bao lâu?"* | AI trả lời đúng: 12 tháng, 1 đổi 1 trong 30 ngày | Cao | ⬜ |
| TC3.09 | AI nhớ ngữ cảnh hội thoại | Hỏi: *"iPhone có màu gì?"* → *"Cái đó giá bao nhiêu?"* | AI hiểu "cái đó" là iPhone vừa hỏi | Cao | ⬜ |
| TC3.10 | Chống spam gửi liên tục | Click gửi nhanh nhiều lần | Chỉ gửi 1 tin, nút + ô bị disable khi đang chờ | Cao | ⬜ |
| TC3.11 | Gửi tin rỗng | Click gửi khi ô trống | Không gửi gì, không có lỗi hiển thị | Cao | ⬜ |
| TC3.12 | Chống XSS | Nhập `<script>alert(1)</script>` → gửi | Hiển thị dạng text thuần, không chạy script | Cao | ⬜ |
| TC3.13 | API key không lộ frontend | Mở DevTools → tab Network → xem request gửi đi | Không thấy `xai-...` key ở bất kỳ request nào từ browser | Cao | ⬜ |
| TC3.14 | Xử lý lỗi mạng | Tắt mạng → send tin nhắn | Hiển thị thông báo "Mất kết nối, vui lòng thử lại" | Trung bình | ⬜ |
| TC3.15 | Scroll tự động | Gửi đủ tin để tràn khung chat | Khung tự động cuộn xuống tin nhắn mới nhất | Trung bình | ⬜ |

---

## 4. TỔNG HỢP KẾT QUẢ

*(Điền sau khi kiểm thử xong)*

| Thành viên | Tổng TC | PASS | FAIL | SKIP |
|---|---|---|---|---|
| Thành viên 1 (Phân trang – Điều hướng) | 10 | | | |
| Thành viên 2 (Phân trang – Lọc & Kết hợp) | 10 | | | |
| Thành viên 3 (Chat AI) | 15 | | | |
| **TỔNG CỘNG** | **35** | | | |

---

## 5. DANH SÁCH BUG

| Bug ID | Mô tả | Module | Người phát hiện | Mức độ | Trạng thái |
|--------|-------|--------|----------------|--------|-----------|
| — | *(Cập nhật khi phát sinh)* | | | | |

---

## 6. PHÊ DUYỆT

| Vai trò | Họ tên | Ngày |
|---------|--------|------|
| Thành viên 1 | | 21/04/2026 |
| Thành viên 2 | | 21/04/2026 |
| Thành viên 3 | | 21/04/2026 |
| Giảng viên hướng dẫn | | |

---
*📌 Ký hiệu: ✅ PASS · ❌ FAIL · ⏭️ SKIP · ⬜ Chưa thực hiện*
 
## Test Run Log - 2026-04-26 
- Cart flow tested on all screen sizes 
- Checkout process validated 
- Authentication tested (login, logout, forgot password) 
- Admin dashboard reviewed 
- Warranty tracking verified 
