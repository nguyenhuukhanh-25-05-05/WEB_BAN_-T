# 🔐 HƯỚNG DẪN SỬA LỖI ĐĂNG NHẬP & MUA HÀNG

## ✅ ĐÃ SỬA:

### 1. **Login Search Cả Email VÀ Username**
**Trước:** Chỉ tìm theo email
```php
WHERE email = ?
```

**Sau:** Tìm theo cả email HOẶC fullname
```php
WHERE email = ? OR fullname = ?
```

### 2. **Tạo Trang Test Login**
File: `test_login.php`

---

## 📋 CÁC BƯỚC TEST:

### **Bước 1: Tạo Test User**
```
http://localhost:8000/test_login.php
```
1. Mở trang trên
2. Click nút **"Create Test User"**
3. Sẽ tạo tài khoản:
   - Email: `test@nhkmobile.com`
   - Password: `Test@123456`
   - Fullname: `Test User`

### **Bước 2: Test Login**
**Cách 1 - Dùng form test:**
1. Ở trang `test_login.php`, scroll xuống mục 2
2. Điền email: `test@nhkmobile.com`
3. Điền password: `Test@123456`
4. Click **"Login via login.php"**

**Cách 2 - Login bình thường:**
1. Vào `http://localhost:8000/login.php`
2. Email: `test@nhkmobile.com`
3. Password: `Test@123456`
4. Click đăng nhập

### **Bước 3: Kiểm Tra Session**
Sau khi login, quay lại `test_login.php`:
- Mục 3 sẽ hiển thị: ✅ User Logged In
- Show User ID, Fullname, Email

### **Bước 4: Test Mua Hàng**
1. Vào `product.php`
2. Click vào sản phẩm
3. Click **"Thêm vào giỏ hàng"**
4. Vào `cart.php` → xem giỏ hàng
5. Click **"Tiến hành đặt hàng"**
6. Điền form checkout
7. Click **"Xác nhận đặt hàng"**

---

## 🔍 DEBUG NẾU VẪN LỖI:

### **Lỗi 1: Login không vào được**
**Kiểm tra:**
1. Mở `test_login.php`
2. Xem mục 5 (Debug Information)
3. Check session data
4. Mở Console (F12) xem có error không

**Fix:**
```sql
-- Check users table
SELECT id, fullname, email, status FROM users LIMIT 5;

-- Check if password is hashed
SELECT id, email, LEFT(password, 20) as pwd_start FROM users;
```

### **Lỗi 2: Login xong nhưng session mất**
**Nguyên nhân:**
- Session cookie không set đúng
- `session_regenerate_id()` có vấn đề
- Browser block third-party cookies

**Fix:**
1. Clear browser cache & cookies
2. Thử trình duyệt khác (Chrome, Firefox)
3. Check browser console cho warnings

### **Lỗi 3: Checkout vẫn báo lỗi**
**Check:**
1. Đã login chưa? → `test_login.php` mục 3
2. Cart có item không? → `test_cart.php`
3. Form checkout điền đầy đủ chưa?

---

## 🧪 TEST ACCOUNTS

### **Test User (Customer)**
```
Email: test@nhkmobile.com
Password: Test@123456
Role: Customer
Status: Active
```

### **Admin (Nếu cần)**
```
Username: admin
Password: (check trong database)
Role: Admin
```

---

## 📊 FLOW ĐẦY ĐỦ

```
1. Register/Login
   ↓
2. ✅ Check session active (test_login.php)
   ↓
3. Browse products (product.php)
   ↓
4. Add to cart (product-detail.php)
   ↓
5. ✅ Check cart has items (test_cart.php)
   ↓
6. View cart (cart.php)
   ↓
7. Go to checkout (checkout.php)
   ↓
8. Fill form (name, phone, address)
   ↓
9. Submit order
   ↓
10. ✅ Success! (checkout.php?order=success)
```

---

## 🔧 QUICK FIXES

### **Fix 1: Reset Everything**
```
1. Logout
2. Clear browser cache (Ctrl+Shift+Delete)
3. Go to test_login.php
4. Clear session
5. Login again with test account
```

### **Fix 2: Check Database Tables**
```sql
-- Users table
SELECT * FROM users WHERE email = 'test@nhkmobile.com';

-- Check cart_items
SELECT * FROM cart_items WHERE user_id = [your_user_id];

-- Check orders
SELECT * FROM orders ORDER BY created_at DESC LIMIT 5;
```

### **Fix 3: Manual Login Test**
```php
// Tạo file manual_login.php
<?php
session_start();
require_once 'includes/db.php';

$email = 'test@nhkmobile.com';
$password = 'Test@123456';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_fullname'] = $user['fullname'];
    $_SESSION['user_email'] = $user['email'];
    echo "✅ Login successful!";
    echo "<br>User ID: " . $user['id'];
    echo "<br>Session: " . session_id();
} else {
    echo "❌ Login failed!";
}
?>
```

---

## 📸 REPORT BUG

Nếu vẫn lỗi, gửi:
1. **Screenshot** `test_login.php` (toàn bộ trang)
2. **Screenshot** `test_cart.php` (toàn bộ trang)
3. **Console logs** (F12 → Console → Copy all)
4. **Mô tả**: Làm bước nào, lỗi gì, message gì?
