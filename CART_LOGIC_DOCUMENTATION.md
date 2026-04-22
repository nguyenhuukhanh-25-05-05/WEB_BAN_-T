# 🛒 LOGIC HỆ THỐNG MUA HÀNG - NHK MOBILE

## 📋 FLOW CHUẨN

```
┌─────────────────────────────────────────────────┐
│           NGƯỜI DÙNG VÀO WEBSITE                │
└──────────────────┬──────────────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  Browse Products     │ ✅ KHÔNG cần login
        │  (product.php)       │    Ai cũng xem được
        └──────────┬───────────┘
                   │
                   ▼ Click sản phẩm
        ┌──────────────────────┐
        │  Product Detail      │ ✅ KHÔNG cần login
        │  (product-detail.php)│    Xem chi tiết thoải mái
        └──────────┬───────────┘
                   │
                   ▼ Click "Thêm vào giỏ hàng"
        ┌──────────────────────┐
        │  CHECK LOGIN?        │ ❓
        └──────────┬───────────┘
                   │
          ┌────────┴────────┐
          │                 │
     ❌ CHƯA LOGIN     ✅ ĐÃ LOGIN
          │                 │
          ▼                 ▼
   ┌──────────────┐  ┌──────────────────────┐
   │ REDIRECT to  │  │ 1. Thêm vào SESSION  │
   │ login.php    │  │ 2. Sync vào DB       │
   │              │  │ 3. Redirect cart.php │
   │ Sau login →  │  └──────────────────────┘
   │ quay lại     │
   └──────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  View Cart           │ ✅ KHÔNG cần login
        │  (cart.php)          │    Xem được (từ session)
        │                      │    Nhưng nếu có trong DB
        │                      │    sẽ load lên sau login
        └──────────┬───────────┘
                   │
                   ▼ Click "Tiến hành đặt hàng"
        ┌──────────────────────┐
        │  CHECK LOGIN?        │ ❓
        └──────────┬───────────┘
                   │
          ┌────────┴────────┐
          │                 │
     ❌ CHƯA LOGIN     ✅ ĐÃ LOGIN
          │                 │
          ▼                 ▼
   ┌──────────────┐  ┌──────────────────────┐
   │ REDIRECT to  │  │  checkout.php        │
   │ login.php    │  │  - Load cart từ DB   │
   │              │  │  - Hiển thị form     │
   │ Sau login →  │  │  - Place order       │
   │ quay lại     │  └──────────────────────┘
   └──────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  Checkout Form       │ ✅ BẮT BUỘC login
        │  (checkout.php)      │    require_login()
        │  - Họ tên            │
        │  - SĐT               │
        │  - Địa chỉ           │
        │  - Thanh toán        │
        └──────────┬───────────┘
                   │
                   ▼ Submit
        ┌──────────────────────┐
        │  Create Order        │ 
        │  - Lưu vào DB        │
        │  - Clear cart        │
        │  - Success page      │
        └──────────────────────┘
```

---

## 🔐 CHI TIẾT TỪNG TRANG

### **1. product.php (Danh sách sản phẩm)**
- ✅ **KHÔNG cần login**
- Ai cũng xem được
- Không có action nào cần auth

### **2. product-detail.php (Chi tiết sản phẩm)**
- ✅ **KHÔNG cần login** để xem
- ❌ **CẦN login** khi click "Thêm vào giỏ hàng"
  - Link: `cart.php?add={id}`
  - Trong `cart.php` line 21: `require_login();`
  - Nếu chưa login → redirect `login.php?redirect=cart.php?add={id}`
  - Sau login → quay lại và thêm vào giỏ

### **3. cart.php (Giỏ hàng)**
- ✅ **KHÔNG cần login** để XEM giỏ hàng
  - Dùng session để lưu tạm
  - Guest users có thể xem cart của họ
- ❌ **CẦN login** khi:
  - Thêm sản phẩm: `?add={id}`
  - Logic ở line 21: `if (isset($_GET['add'])) { require_login(); }`

### **4. checkout.php (Thanh toán)**
- ❌ **BẮT BUỘC login**
- Line 19: `require_login();`
- Nếu chưa login → redirect `login.php?redirect=checkout.php`
- Sau login → quay lại checkout
- Load cart từ DB (vì đã login)
- Place order → lưu vào DB với `user_id`

---

## 💾 DATABASE STRUCTURE

### **cart_items Table**
```sql
CREATE TABLE cart_items (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    product_id INTEGER REFERENCES products(id),
    quantity INTEGER DEFAULT 1,
    session_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, product_id)
);
```

**Logic sync:**
1. **Chưa login:** Cart lưu trong `$_SESSION['cart']`
2. **Đã login:** 
   - Session cart → Sync xuống DB (user_id)
   - DB cart → Load lên session
   - Kết hợp cả session_id và user_id

### **orders Table**
```sql
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(50),
    customer_address TEXT,
    total_price DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Chờ duyệt',
    payment_method VARCHAR(50) DEFAULT 'COD',
    user_id INTEGER REFERENCES users(id),
    is_installment BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW()
);
```

**Chỉ tạo order khi:**
- ✅ Đã login (có user_id)
- ✅ Cart có items
- ✅ Form validated (name, phone)

---

## 🔄 SYNC LOGIC

### **syncCartWithDatabase() Function**

```php
function syncCartWithDatabase($pdo) {
    // 1. Nếu CHƯA login:
    //    - Chỉ dùng session
    //    - KHÔNG load từ DB
    //    - KHÔNG lưu xuống DB
    if (!$userId && !$isAdmin) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return;
    }
    
    // 2. Nếu ĐÃ login:
    //    A. Session rỗng → Load từ DB lên session
    //    B. Session có hàng → Lưu xuống DB
    
    // Trường hợp A: Load từ DB
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $sql = "SELECT ci.*, p.name, p.price, p.image 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.user_id = ?";
        // ... load vào $_SESSION['cart']
    }
    
    // Trường hợp B: Lưu xuống DB
    else {
        // Xóa items trong DB mà session không có
        // INSERT/UPDATE từng item từ session vào DB
    }
}
```

---

## 🎯 USER JOURNEY

### **Scenario 1: Guest User (Chưa login)**
```
1. Vào product.php → Xem sản phẩm ✅
2. Click sản phẩm → Xem chi tiết ✅
3. Click "Thêm vào giỏ" → 
   ↓
   Redirect đến login.php ✅
   ?redirect=cart.php?add=5
   ↓
4. Login thành công →
   ↓
   Redirect về cart.php?add=5 ✅
   ↓
5. Sản phẩm được thêm vào cart ✅
6. Cart sync xuống DB ✅
```

### **Scenario 2: Logged-in User**
```
1. Login trước ✅
2. Vào product.php → Xem sản phẩm ✅
3. Click "Thêm vào giỏ" →
   ↓
   Thêm vào $_SESSION['cart'] ✅
   ↓
   Sync xuống DB (user_id) ✅
   ↓
4. Vào cart.php → Xem giỏ hàng ✅
   (Load từ DB nếu có)
   ↓
5. Click "Tiến hành đặt hàng" →
   ↓
   checkout.php (đã login rồi) ✅
   ↓
6. Điền form → Submit ✅
   ↓
7. Order created với user_id ✅
8. Cart cleared ✅
```

### **Scenario 3: Guest → Login → Checkout**
```
1. Guest thêm sản phẩm vào cart (session only)
2. Vào checkout.php →
   ↓
   require_login() → redirect login.php ✅
   ?redirect=checkout.php
   ↓
3. Login thành công →
   ↓
   Redirect về checkout.php ✅
   ↓
4. syncCartWithDatabase() chạy:
   - Session cart (có từ trước) → Sync xuống DB ✅
   - Load lại cart từ DB ✅
   ↓
5. Checkout bình thường ✅
```

---

## ✅ CHECKLIST HOẠT ĐỘNG

### **Phải có:**
- [x] Chưa login → Click "Thêm vào giỏ" → Redirect login
- [x] Sau login → Quay lại action trước
- [x] Đã login → Thêm vào giỏ → Sync xuống DB
- [x] Đã login → Cart load từ DB
- [x] Checkout → Bắt buộc login
- [x] Order lưu với user_id
- [x] Cart cleared sau order

### **Database:**
- [x] cart_items table có user_id
- [x] orders table có user_id
- [x] Sync session ↔ DB hoạt động
- [x] ON CONFLICT update quantity

---

## 🐛 TROUBLESHOOTING

### **Lỗi: Không redirect đến login**
**Check:**
```php
// Trong cart.php line 21
if (isset($_GET['add'])) {
    require_login(); // Phải có dòng này
}
```

### **Lỗi: Cart không sync xuống DB**
**Check:**
```php
// Sau khi thêm cart
syncCartWithDatabase($pdo); // Phải gọi hàm này
```

### **Lỗi: Checkout không có cart items**
**Check:**
1. Đã login chưa?
2. Cart có items không?
3. Sync DB chạy chưa?

### **Lỗi: Login xong mất cart**
**Check:**
```php
// Trong cart_functions.php
// Phải load cart từ DB lên session sau login
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Load from DB...
}
```

---

## 📝 TEST CASES

### **Test 1: Guest Add to Cart**
```
1. Logout
2. Vào product-detail.php?id=1
3. Click "Thêm vào giỏ hàng"
✅ Phải redirect đến login.php
✅ URL có ?redirect=cart.php?add=1
```

### **Test 2: Login & Add to Cart**
```
1. Login
2. Vào product-detail.php?id=1
3. Click "Thêm vào giỏ hàng"
✅ Redirect về cart.php
✅ Sản phẩm có trong cart
✅ Check DB: cart_items có record mới
```

### **Test 3: Checkout Flow**
```
1. Login
2. Add product to cart
3. Vào cart.php → xem cart
4. Click "Tiến hành đặt hàng"
✅ Vào được checkout.php
5. Fill form → Submit
✅ Order created trong DB
✅ Cart cleared
✅ Redirect success
```

### **Test 4: Cart Persistence**
```
1. Login
2. Add products to cart
3. Logout
4. Login lại
✅ Cart vẫn còn (load từ DB)
```

---

## 🎓 SUMMARY

**Logic chuẩn:**
1. ✅ Browse products → Không cần login
2. ✅ Add to cart → **PHẢI login** (redirect nếu chưa)
3. ✅ View cart → Không cần login (session only)
4. ✅ Checkout → **BẮT BUỘC login**
5. ✅ Cart sync → Session ↔ Database
6. ✅ Order → Lưu với user_id

**Files quan trọng:**
- `cart.php` - Xử lý thêm/xóa cart
- `checkout.php` - Xử lý đặt hàng
- `cart_functions.php` - Sync logic
- `auth_functions.php` - require_login()
- `login.php` - Xử lý đăng nhập
