# HƯỚNG DẪN CHI TIẾT: ĐẨY CODE LÊN GITHUB & TRIỂN KHAI LÊN RENDER

Tài liệu này hướng dẫn bạn từng bước một cách chi tiết nhất để vận hành hệ thống NHK Mobile.

---

## PHẦN 1: ĐẨY CODE LÊN GITHUB (MÁY CÁ NHÂN)

Mỗi khi bạn sửa code, thêm ảnh, hoặc thay đổi giao diện, bạn cần thực hiện các bước sau để lưu trữ code an toàn trên GitHub.

### Bước 1: Mở Terminal
- Trong Trae IDE hoặc VS Code, nhấn tổ hợp phím **`Ctrl + \``** (phím cạnh số 1) để mở Terminal.
- Đảm bảo Terminal đang ở đúng thư mục dự án `WEB_DienThoai`.

### Bước 2: Chuẩn bị đẩy code (Bộ 3 lệnh thần thánh)
Bạn hãy nhập lần lượt từng dòng lệnh sau và nhấn Enter:

1. **Kiểm tra các file đã sửa:**
   ```powershell
   git status
   ```
   *Lệnh này để bạn thấy danh sách các file màu đỏ (chưa lưu).*

2. **Lưu tất cả thay đổi vào "giỏ hàng":**
   ```powershell
   git add .
   ```
   *Dấu chấm có nghĩa là lấy tất cả. Nếu bạn chạy lại `git status`, các file sẽ chuyển sang màu xanh.*

3. **Đóng gói thay đổi kèm ghi chú:**
   ```powershell
   git commit -m "Sửa lỗi giao diện và cập nhật AI Chat"
   ```
   *Ghi chú trong ngoặc kép giúp bạn nhớ mình đã sửa cái gì.*

4. **Đẩy lên GitHub:**
   ```powershell
   git push origin main
   ```
   - **Nếu hiện thông báo lỗi:** Đọc kỹ thông báo. Nếu nó yêu cầu `git pull` trước, hãy gõ `git pull origin main`.
   - **Nếu hiện cửa sổ đăng nhập:** Hãy chọn **Sign in with your browser** và làm theo hướng dẫn trên web.

---

## PHẦN 2: CẤU HÌNH TRÊN RENDER (ĐỂ WEB CHẠY ĐƯỢC)

Code trên Render sẽ tự động cập nhật sau khi bạn `git push`. Tuy nhiên, bạn cần cấu hình "bí mật" để AI và CSDL hoạt động.

### Bước 1: Cấu hình API Key cho AI Chat
1. Đăng nhập [Dashboard Render](https://dashboard.render.com/).
2. Click vào tên Web Service của bạn (ví dụ: `nhkmobile`).
3. Ở cột menu bên trái, tìm mục **Environment**.
4. Tìm nút **Add Environment Variable** (Thêm biến môi trường).
5. Nhập chính xác thông tin sau:
   - **Key**: `XAI_API_KEY`
   - **Value**: `(Dán mã API Grok của bạn vào đây)`
   *Lưu ý: Bạn lấy mã API này từ trang quản trị xAI/Grok.*
6. Nhấn **Save Changes** (Lưu thay đổi). Render sẽ tự động khởi động lại (Deploying) để áp dụng.

### Bước 2: Cấu hình DATABASE_URL
*Thường thì bước này đã được Render tự động làm khi bạn liên kết Database với Web Service. Nếu chưa, hãy kiểm tra:*
1. Trong mục **Environment**, đảm bảo có biến `DATABASE_URL`.
2. Nếu chưa có, nhấn **Link Static Service** hoặc **Link Database** và chọn Database PostgreSQL của bạn.

---

## PHẦN 3: CẬP NHẬT CSDL SAU KHI DEPLOY (CỰC KỲ QUAN TRỌNG)

Mỗi khi tôi thêm cột mới vào Database (như cột `is_installment` cho trả góp, hoặc `status` cho login), Render sẽ không tự biết. Bạn phải ép nó cập nhật.

1. Chờ Render báo trạng thái **`Live`** (màu xanh lá).
2. Mở trình duyệt, gõ đường dẫn sau:
   👉 **`https://nhkmobile.onrender.com/init-db.php`**
3. Nếu trang web hiện: **"Khởi tạo Schema và Mock Data thành công!"** là bạn đã hoàn thành.
4. **Lưu ý:** Bạn chỉ cần chạy link này **1 lần duy nhất** sau mỗi lần cập nhật code quan trọng liên quan đến CSDL.

---

## CÁC LỖI THƯỜNG GẶP (TROUBLESHOOTING)

- **Lỗi 404:** Kiểm tra xem file đó đã được `git push` lên chưa.
- **Lỗi 500 (Internal Server Error):** Thường do sai cấu hình Database hoặc chưa chạy file `init-db.php`.
- **AI không trả lời:** Kiểm tra xem đã nhập đúng `XAI_API_KEY` trong mục Environment chưa.
