# 🛒 HƯỚNG DẪN DEBUG LỖI MUA HÀNG

## 📋 Các bước kiểm tra:

### 1️⃣ **Mở trang test cart**
```
http://localhost:8000/test_cart.php
```

Trang này sẽ hiển thị:
- ✅ Session có hoạt động không
- ✅ Database có kết nối không  
- ✅ Có sản phẩm trong database không
- ✅ Giỏ hàng có item không
- ✅ User đã đăng nhập chưa

### 2️⃣ **Test thêm sản phẩm vào giỏ**

Trong trang `test_cart.php`:
1. Click nút **"➕ Add to Cart"**
2. Check xem mục "3. Cart Contents" có hiển thị item không
3. Nếu có → Click **"💳 Go to Checkout"**

### 3️⃣ **Kiểm tra Console Browser**

Mở bất kỳ trang nào (index.php, product-detail.php):
1. Nhấn **F12** để mở DevTools
2. Chuyển sang tab **Console**
3. Sẽ thấy các thông báo debug:
   ```
   🔍 Cart Debug Script Loaded
   🛒 Found X "Add to Cart" links
   🔘 Found X buttons
   ✅ Debug setup complete
   ```

4. **Click vào nút "Thêm vào giỏ hàng"**
   - Console sẽ hiện: `✅ Add to cart clicked: http://...`
   - Nếu KHÔNG thấy → Có element khác chặn click

### 4️⃣ **Các lỗi thường gặp**

#### ❌ **Lỗi 1: Không thêm được vào giỏ**
**Nguyên nhân:** 
- Session không hoạt động
- Database connection lỗi

**Kiểm tra:**
```
http://localhost:8000/test_cart.php
```
→ Xem mục 1 và 2 có ✅ không

#### ❌ **Lỗi 2: Click nút không phản hồi**
**Nguyên nhân:**
- Có modal/overlay che khuất (z-index cao)
- CSS `pointer-events: none`
- JavaScript error

**Kiểm tra:**
- Mở Console (F12)
- Tìm cảnh báo: `⚠️ Potentially blocking element`
- Tìm lỗi JavaScript (màu đỏ)

#### ❌ **Lỗi 3: Checkout báo lỗi**
**Nguyên nhân:**
- Chưa đăng nhập
- Form validation fail
- Database insert lỗi

**Kiểm tra:**
- Đã login chưa? (test_cart.php → mục 4)
- Điền đầy đủ họ tên và SĐT chưa?
- Mở Console xem có error không

### 5️⃣ **Test Flow đầy đủ**

```
1. Đăng nhập/Đăng ký
   ↓
2. Vào trang sản phẩm (product.php)
   ↓  
3. Click vào sản phẩm → product-detail.php
   ↓
4. Click "Thêm vào giỏ hàng"
   ↓
5. Kiểm tra giỏ hàng → cart.php
   ↓
6. Click "Tiến hành đặt hàng" → checkout.php
   ↓
7. Điền form và click "Xác nhận đặt hàng"
   ↓
8. Thành công! → checkout.php?order=success
```

### 6️⃣ **Nếu vẫn lỗi, cung cấp thông tin:**

1. **Screenshot** trang `test_cart.php`
2. **Console logs** (F12 → Console → Copy all)
3. **Mô tả bước** bạn làm khi lỗi xảy ra
4. **URL** trang bạn đang ở

---

## 🔧 Quick Fixes

### Fix 1: Clear Session & Cache
```
1. Logout
2. Xóa cache browser (Ctrl+Shift+Delete)
3. Login lại
4. Thử mua hàng
```

### Fix 2: Check Database
```sql
-- Kiểm tra products
SELECT id, name, price, stock FROM products LIMIT 5;

-- Kiểm tra cart_items table
SELECT * FROM cart_items LIMIT 5;

-- Kiểm tra orders table
SELECT * FROM orders ORDER BY created_at DESC LIMIT 5;
```

### Fix 3: Reset Cart
```php
// Tạo file clear_cart.php
<?php
session_start();
unset($_SESSION['cart']);
unset($_SESSION['is_installment']);
echo "Cart cleared!";
?>
```

---

## 📞 Hỗ trợ

Nếu vẫn không được, gửi:
- Screenshot test_cart.php
- Console logs
- Mô tả chi tiết lỗi
