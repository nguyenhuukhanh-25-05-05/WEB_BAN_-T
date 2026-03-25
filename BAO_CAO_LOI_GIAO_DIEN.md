# BÁO CÁO LỖI GIAO DIỆN & RESPONSIVE (BUG REPORT)

Tài liệu này liệt kê các vấn đề thường gặp và các lỗi tiềm ẩn khi thao tác với thanh điều hướng (Navbar) và giao diện di động của dự án NHK Mobile.

---

## 1. CÁC LỖI NAVBAR KHI THU NHỎ (RESPONSIVE ISSUES)

### ❌ Lỗi 1: Tràn biểu tượng trên màn hình siêu nhỏ (Icon Crowding)
- **Mô tả:** Khi xem trên các thiết bị có chiều ngang hẹp (như iPhone SE hoặc khi thu nhỏ trình duyệt xuống dưới 350px), các biểu tượng (Logo, Tìm kiếm, Giỏ hàng, Tài khoản, Menu Hamburger) bị ép sát vào nhau.
- **Nguyên nhân:** Có quá nhiều thành phần (5 phần tử) cùng nằm trên một hàng ngang với `justify-content: space-between`.
- **Hệ quả:** Logo bị méo hoặc các icon đè lên nhau, gây mất thẩm mỹ và khó bấm.

### ❌ Lỗi 2: Vỡ khung Navbar do chiều cao cố định (Fixed Height Overflow)
- **Mô tả:** Navbar được thiết lập `height: 54px`. Nếu một biểu tượng có thêm thông báo (số lượng giỏ hàng) hoặc văn bản bị xuống hàng, nó sẽ nhảy ra ngoài vùng màu trắng của Navbar.
- **Nguyên nhân:** Thuộc tính `height` cố định không cho phép Navbar giãn nở theo nội dung bên trong.
- **Hệ quả:** Giao diện trông "vỡ", nội dung bị lòi ra ngoài viền của thanh điều hướng.

### ❌ Lỗi 3: Nút Hamburger bị lệch hoặc khó tương tác
- **Mô tả:** Trên một số phiên bản, nút Menu (Hamburger) không nằm sát lề phải mà bị đẩy vào giữa hoặc bị các icon khác che khuất một phần.
- **Nguyên nhân:** Cách sắp xếp `flexbox` trong `navbar-centered-wrapper` chưa ưu tiên vị trí cố định cho nút toggle trên mobile.

---

## 2. CÁC LỖI THƯỜNG GẶP KHI SỬA RESPONSIVE

### ❌ Lỗi 4: Tràn lề ngang (Horizontal Scroll)
- **Mô tả:** Xuất hiện thanh cuộn ngang ở chân trang, người dùng có thể gạt màn hình sang trái/phải thấy khoảng trắng.
- **Nguyên nhân:** Do các thẻ `div` hoặc `section` có chiều rộng vượt quá `100%` hoặc do lề âm (`negative margin`) của Bootstrap Row không được khử trên mobile.

### ❌ Lỗi 5: Lớp phủ trang trí chặn tương tác (Pointer Events)
- **Mô tả:** Các nút bấm ở phần Hero (đầu trang) không thể click được dù nhìn thấy rõ.
- **Nguyên nhân:** Các lớp phủ màu sắc hoặc hiệu ứng gradient (`.hero-bg-gradient`) nằm đè lên trên nhưng không có thuộc tính `pointer-events: none`.

---

## 3. ĐỀ XUẤT KHẮC PHỤC (RECOMMENDATIONS)

1. **Cho Navbar:** Sử dụng `min-height: 54px` thay vì `height: 54px` để linh hoạt hơn.
2. **Cho Icon Mobile:** Ẩn bớt các icon không quá quan trọng (như icon Tài khoản) vào bên trong Menu Hamburger trên màn hình cực nhỏ (< 320px).
3. **Kiểm tra lề:** Luôn sử dụng thuộc tính `overflow-x: hidden` cho các container lớn để đảm bảo không bị trượt ngang.

---
*Người báo cáo: AI Assistant*
*Ngày: 24/03/2026*
