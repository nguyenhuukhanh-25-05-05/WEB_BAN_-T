# NHK Mobile - Hệ thống Authentication & Bảo mật

## 📋 Tổng quan các cải tiến (Cập nhật 2026-04-15)

### 🔒 1. BẢO MẬT ĐƯỢC CẢI THIỆN

#### CSRF Protection (Cross-Site Request Forgery)
- ✅ Tất cả form đều có CSRF token
- ✅ Token tự động expire sau 1 giờ
- ✅ Token được validate trước mỗi request

#### Rate Limiting
- ✅ Login: Tối đa 5 lần thử / 5 phút
- ✅ Register: Tối đa 3 lần thử / 10 phút  
- ✅ Forgot Password: Tối đa 3 lần thử / 10 phút
- ✅ Tự động reset sau thời gian chờ

#### Password Security
- ✅ Password hashing với `PASSWORD_DEFAULT` (bcrypt)
- ✅ Password strength validation:
  - Tối thiểu 8 ký tự
  - Ít nhất 1 chữ hoa
  - Ít nhất 1 chữ thường
  - Ít nhất 1 số
  - Ít nhất 1 ký tự đặc biệt (!@#$%^&*...)
- ✅ Password strength indicator (Yếu/Trung bình/Mạnh)

#### Session Security
- ✅ Session regeneration sau mỗi lần đăng nhập
- ✅ Session timeout sau 30 phút không hoạt động
- ✅ Secure session configuration (HttpOnly, SameSite=Strict)
- ✅ Protection against session fixation attacks

#### Input Validation
- ✅ Tất cả input được sanitize (htmlspecialchars + strip_tags)
- ✅ Email validation với filter_var
- ✅ Autocomplete attributes cho trình duyệt

#### Logging & Audit
- ✅ Tất cả auth attempts được log
- ✅ Log file: `/logs/auth.log`
- ✅ Ghi nhận: IP, timestamp, action, success/failure

#### Database Security
- ✅ Email UNIQUE constraint (không thể đăng ký trùng email)
- ✅ Prepared statements (chống SQL injection)
- ✅ Password reset tokens có expiration

---

### 🎨 2. HIỆU ỨNG & UI/UX

#### Animations
- ✅ **Slide-up animation**: Form xuất hiện mượt mà khi load
- ✅ **Focus effects**: Input nổi lên với shadow xanh khi focus
- ✅ **Button hover**: Button nâng lên với shadow
- ✅ **Link hover**: Links phóng to nhẹ
- ✅ **Error shake**: Lỗi rung lên để thu hút sự chú ý
- ✅ **Success fade**: Thành công xuất hiện mượt mà
- ✅ **Icon scale**: Icons phóng to khi hover

#### Password Features
- ✅ **Show/Hide password**: Toggle hiển thị mật khẩu
- ✅ **Password strength meter**: Thanh bar màu (đỏ/vàng/xanh)
- ✅ **Loading states**: Spinner khi submit form
- ✅ **Form validation**: Client-side validation trước khi submit

---

### 📁 3. FILE MỚI ĐƯỢC TẠO

#### `forgot-password.php` (Root)
- **Step 1**: Nhập email → Generate reset token
- **Step 2**: Click link với token → Nhập mật khẩu mới
- **Security**:
  - Token expire sau 1 giờ
  - Token chỉ sử dụng 1 lần
  - Password strength validation
  - CSRF protection

#### `admin/password_resets.php`
- Xem tất cả yêu cầu đặt lại mật khẩu đang chờ
- Đặt lại mật khẩu thủ công cho user
- Xác minh danh tính người dùng
- Ghi chú audit trail
- Danh sách tất cả users với lịch sử reset

---

### 🗄️ 4. DATABASE MIGRATIONS

#### Bảng mới: `password_resets`
```sql
CREATE TABLE password_resets (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    reset_token VARCHAR(255) UNIQUE,
    expires_at TIMESTAMP,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Cột mới cho bảng `users`:
- `username` VARCHAR(50) - Tương thích legacy
- `last_password_reset` TIMESTAMP - Theo dõi password resets

#### Constraints:
- `users_email_unique` - Email phải là duy nhất
- `users_username_unique` - Username phải là duy nhất

---

### 🔄 5. WORKFLOW QUÊN MẬT KHẨU

#### Flow 1: User tự đặt lại (Online)
1. User vào `/forgot-password.php`
2. Nhập email → Hệ thống generate token
3. Token lưu vào DB với expiration (1 giờ)
4. User nhận link reset (trong production: gửi qua email)
5. User click link → Nhập mật khẩu mới
6. Token được đánh dấu `is_used = TRUE`
7. Password được update → Redirect to login

#### Flow 2: Admin hỗ trợ (Manual)
1. User liên hệ admin (phone/email/ở quầy)
2. Admin xác minh danh tính
3. Admin vào `/admin/password_resets.php`
4. Chọn user → Đặt mật khẩu mới
5. Hệ thống log action → Invalidated old tokens
6. Thông báo mật khẩu mới cho user

---

### 🛡️ 6. BEST PRACTICES ĐÃ IMPLEMENT

#### Security
- ✅ Không tiết lộ email có tồn tại hay không
- ✅ Tokens expire và one-time use
- ✅ Rate limiting để prevent brute force
- ✅ Input sanitization để prevent XSS
- ✅ Prepared statements để prevent SQL injection
- ✅ Password hashing với bcrypt
- ✅ Session management an toàn

#### User Experience
- ✅ Clear error messages (tiếng Việt)
- ✅ Password strength indicator
- ✅ Show/hide password toggle
- ✅ Loading states để user biết đang xử lý
- ✅ Smooth animations và transitions
- ✅ Mobile responsive

#### Admin Control
- ✅ Full visibility vào reset requests
- ✅ Manual override capability
- ✅ Audit logging cho compliance
- ✅ User identity verification flow

---

### 📝 7. HƯỚNG DẪN SỬ DỤNG

#### Cho Users:
1. **Đăng ký**: Vào `/register.php` → Điền form → Email phải duy nhất
2. **Đăng nhập**: Vào `/login.php` → Nhập email + password
3. **Quên mật khẩu**: 
   - Vào `/forgot-password.php`
   - Nhập email
   - Nhận link reset
   - Đặt mật khẩu mới

#### Cho Admin:
1. **Xem yêu cầu reset**: Vào `/admin/password_resets.php`
2. **Đặt lại mật khẩu cho user**:
   - Chọn user từ dropdown
   - Nhập mật khẩu mới (phải đủ mạnh)
   - Ghi chú xác minh
   - Submit
3. **Xem log**: Kiểm tra `/logs/auth.log`

---

### 🚀 8. PRODUCTION DEPLOYMENT NOTES

#### Email Configuration (Cần thêm)
Để gửi email reset thực sự, cần:
```php
// Sử dụng PHPMailer hoặc similar
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'noreply@nhkmobile.com';
$mail->Password = 'your_password';
$mail->setFrom('noreply@nhkmobile.com', 'NHK Mobile');
$mail->addAddress($userEmail);
$mail->Subject = 'Đặt lại mật khẩu NHK Mobile';
$mail->Body = "Click vào link: $resetUrl";
$mail->send();
```

#### Environment Variables
```env
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=noreply@nhkmobile.com
SMTP_PASSWORD=your_secure_password
```

#### Security Hardening (Recommended)
- [ ] Enable HTTPS/SSL
- [ ] Set `session.cookie_secure = 1`
- [ ] Implement 2FA cho admin
- [ ] Setup fail2ban cho rate limiting ở server level
- [ ] Regular security audits
- [ ] Backup database thường xuyên

---

### ✅ 9. CHECKLIST KIỂM THỬ

- [ ] Đăng ký tài khoản mới (email duy nhất)
- [ ] Thử đăng ký trùng email → Phải báo lỗi
- [ ] Đăng nhập đúng → Thành công
- [ ] Đăng nhập sai password → Báo lỗi
- [ ] Quên mật khẩu → Generate token
- [ ] Reset password với token → Thành công
- [ ] Token hết hạn → Không sử dụng được
- [ ] Password yếu → Không chấp nhận
- [ ] Rate limit exceeded → Phải chờ
- [ ] Admin reset password → Thành công
- [ ] Animations hoạt động mượt
- [ ] Mobile responsive OK

---

### 📞 10. TROUBLESHOOTING

**Lỗi: "Email đã được sử dụng"**
- Email phải là duy nhất trong hệ thống
- Kiểm tra bảng `users` trong database

**Lỗi: "Token không hợp lệ"**
- Token đã hết hạn (1 giờ)
- Token đã được sử dụng
- Yêu cầu token mới

**Lỗi: "Rate limit exceeded"**
- Đợi 5-10 phút rồi thử lại
- Hoặc clear session/cookies

**Animation không hoạt động:**
- Clear browser cache
- Kiểm tra console errors
- Đảm bảo JavaScript enabled

---

**Version**: 3.0  
**Last Updated**: 2026-04-15  
**Author**: NguyenHuuKhanh  
**Status**: Production Ready ✅
