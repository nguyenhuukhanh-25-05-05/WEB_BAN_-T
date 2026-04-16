# HƯỚNG DẪN QUẢN TRỊ DỮ LIỆU BẰNG TABLEPLUS

Tài liệu này hướng dẫn cách kết nối trực tiếp vào Database trên Cloud của Render để xem, sửa và quản lý sản phẩm/đơn hàng.

---

## PHẦN 1: CÀI ĐẶT TABLEPLUS

1. Truy cập: [https://tableplus.com/](https://tableplus.com/)
2. Nhấn nút **Download for Windows**.
3. Sau khi tải về, mở file cài đặt. Quá trình cài đặt diễn ra tự động trong vài giây.
4. Mở ứng dụng TablePlus sau khi cài xong.

---

## PHẦN 2: LẤY THÔNG TIN KẾT NỐI TỪ SUPABASE

Database của bạn hiện đã được chuyển sang Supabase để sử dụng miễn phí lâu dài:

1. Đăng nhập [Supabase Dashboard](https://supabase.com/dashboard).
2. Chọn Project của bạn (`qfaslglevzkujkmylxfx`).
3. Vào mục **Project Settings** (biểu tượng bánh răng) -> **Database**.
4. Tìm phần **Connection string**, chọn tab **URI**.
5. Copy chuỗi có dạng: `postgresql://postgres:[YOUR-PASSWORD]@db.qfaslglevzkujkmylxfx.supabase.co:5432/postgres`
   *Lưu ý: Thay `[YOUR-PASSWORD]` bằng mật khẩu bạn đã đặt.*

---

## PHẦN 3: KẾT NỐI VÀO DATABASE

1. Mở TablePlus.
2. Nhấn vào dòng chữ **"Create a new connection..."** (hoặc nhấn chuột phải vào vùng trống).
3. Nhấn vào nút **"Import from URL"** (thường nằm ở góc dưới bên trái).
4. Dán chuỗi `External Connection String` bạn vừa copy ở Bước 2 vào ô trống.
5. Nhấn **Import**. TablePlus sẽ tự động điền các ô Host, Port, User, Password...
6. Nhấn nút **Test** để kiểm tra:
   - Nếu các dòng hiện màu **xanh lá**: Kết nối tốt.
   - Nếu hiện màu **đỏ**: Kiểm tra lại chuỗi copy hoặc mạng internet.
7. Nhấn **Connect** để bắt đầu quản trị.

---

## PHẦN 4: CÁCH SỬ DỤNG CĂN BẢN

### 1. Xem danh sách các bảng
Ở cột bên trái (Items), bạn sẽ thấy danh sách các bảng:
- `products`: Danh sách điện thoại, giá cả, ảnh.
- `orders`: Danh sách các đơn hàng khách đã đặt.
- `users`: Danh sách khách hàng đã đăng ký tài khoản.
- `order_items`: Chi tiết từng máy trong mỗi đơn hàng.

### 2. Xem và sửa dữ liệu
- **Xem:** Nhấn đúp chuột vào tên bảng (ví dụ: bảng `products`). Dữ liệu sẽ hiện ra như một trang Excel.
- **Sửa:** Click trực tiếp vào ô cần sửa (ví dụ sửa giá tiền).
- **LƯU THAY ĐỔI:** Sau khi sửa xong, bạn **PHẢI** nhấn nút **`Commit`** (biểu tượng dấu tích ở thanh công cụ phía trên) hoặc nhấn `Ctrl + S`. Nếu không nhấn nút này, dữ liệu sẽ không được lưu lên web.

### 3. Thêm hoặc Xóa dòng
- **Thêm:** Nhấn chuột phải vào vùng dữ liệu chọn **New -> Row** (hoặc nhấn phím `+` ở dưới cùng).
- **Xóa:** Chọn dòng cần xóa, nhấn chuột phải chọn **Delete** (hoặc nhấn phím `-` ở dưới cùng).
- **Lưu ý:** Luôn nhớ nhấn **Commit** sau khi thực hiện.

---

## LỜI KHUYÊN BẢO MẬT
- Chuỗi kết nối (Connection String) chứa mật khẩu Database của bạn. Tuyệt đối không gửi nó cho người lạ.
- Nếu bạn lỡ tay sửa sai dữ liệu và chưa nhấn **Commit**, hãy nhấn nút **Discard** (biểu tượng mũi tên quay lại) để khôi phục dữ liệu cũ.
