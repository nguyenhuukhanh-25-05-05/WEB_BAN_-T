# NHK Mobile - Web Bán Điện Thoại

Website bán điện thoại PHP thuần sử dụng PostgreSQL với Admin Panel Desktop App.

**Last Updated:** April 24, 2026
**Current Version:** 2.4

### ✨ Recent Refinements
- **UI/UX:** Added interactive hover animations and smooth transitions to landing page buttons.
- **Maintenance:** Updated core dependencies and metadata for version 2.4.
- **Deployment:** Optimized Nixpacks and Render configuration for faster deployment.

## 📱 Tính năng chính

### Website khách hàng
- Trang chủ với carousel, sản phẩm nổi bật, flash sale
- Danh mục sản phẩm (Apple, Samsung, Xiaomi, OPPO, Vivo, Realme)
- Giỏ hàng và thanh toán
- Theo dõi đơn hàng
- Bảo hành điện tử
- Tin tức công nghệ
- Yêu thích sản phẩm
- Quản lý tài khoản

### Admin Panel
- Dashboard tổng quan
- Quản lý sản phẩm
- Quản lý đơn hàng
- Quản lý khách hàng
- Quản lý bảo hành
- Quản lý tin tức
- Báo cáo doanh thu
- Xuất thống kê

### Desktop App (Mới!)
- Admin Panel dưới dạng ứng dụng desktop
- Hỗ trợ Windows, macOS, Linux
- Menu điều hướng nhanh
- Tự động khởi động PHP server
- Build thành file cài đặt độc lập

### System Check (Mới!)
- Trang kiểm tra toàn bộ hệ thống
- Kiểm tra kết nối database
- Kiểm tra PHP extensions
- Kiểm tra quyền thư mục
- Kiểm tra dung lượng đĩa
- Thông tin hệ thống chi tiết

## 🚀 Cài đặt

### Yêu cầu
- PHP 8.2+
- PostgreSQL 14+
- Node.js 16+ (cho desktop app)

### Chạy website
```bash
# Sử dụng PHP built-in server
php -S 127.0.0.1:8080 -t .

# Hoặc sử dụng XAMPP/Laragon
# Trỏ Document Root đến thư mục này
```

### Chạy Desktop App
```bash
cd desktop-app
npm install
npm start
```

Hoặc sử dụng scripts:
```bash
# Windows
desktop-app\setup.bat    # Cài đặt
desktop-app\run.bat      # Chạy
desktop-app\build.bat    # Build
```

## 📁 Cấu trúc thư mục

```
├── admin/               # Admin Panel
│   ├── dashboard.php   # Dashboard
│   ├── products.php    # Quản lý sản phẩm
│   ├── orders.php      # Quản lý đơn hàng
│   ├── users.php       # Quản lý users
│   ├── warranties.php  # Quản lý bảo hành
│   ├── news.php        # Quản lý tin tức
│   ├── revenue.php     # Báo cáo doanh thu
│   └── includes/       # Admin header/footer
├── api/                # REST API endpoints
├── assets/             # CSS, JS, images
├── includes/           # Shared PHP files
├── desktop-app/        # Electron Desktop App
│   ├── main.js         # Electron main process
│   ├── preload.js      # Preload script
│   ├── package.json    # NPM config
│   └── *.bat           # Windows scripts
├── index.php           # Trang chủ
├── product.php         # Danh sách sản phẩm
├── cart.php            # Giỏ hàng
├── checkout.php        # Thanh toán
├── check.php           # Kiểm tra hệ thống (Mới!)
├── login.php           # Đăng nhập
├── register.php        # Đăng ký
└── ...
```

## 📋 Database

Sử dụng PostgreSQL với các bảng chính:
- `users` - Người dùng
- `products` - Sản phẩm
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng
- `carts` - Giỏ hàng
- `wishlists` - Yêu thích
- `warranties` - Bảo hành
- `news` - Tin tức
- `invoices` - Hóa đơn

## 🛠️ Admin Desktop App

### Cài đặt
```bash
cd desktop-app
npm install
```

### Chạy
```bash
npm start        # Chế độ bình thường
npm run dev      # Chế độ developer (có DevTools)
```

### Build
```bash
npm run build:win    # Windows
npm run build:mac    # macOS
npm run build:linux  # Linux
```

### Tính năng
- Menu điều hướng nhanh đến các module
- Tự động khởi động PHP server
- Hỗ trợ phím tắt (Ctrl+R, Ctrl+Q, F12)
- Build thành file cài đặt độc lập

## 🔍 System Check

Truy cập `/check.php` để:
- Kiểm tra kết nối database
- Kiểm tra PHP extensions
- Kiểm tra quyền thư mục
- Xem thông tin hệ thống
- Truy cập nhanh đến admin panel

## 🌐 Deploy

### Render.com
1. Push code lên GitHub
2. Kết nối repository với Render
3. Cấu hình environment variables:
   - `DATABASE_URL`
   - `XAI_API_KEY` (cho AI chat)
4. Deploy tự động

### VPS/Server riêng
1. Cài đặt PHP 8.2+, PostgreSQL, Nginx/Apache
2. Clone repository
3. Cấu hình database trong `includes/db.php`
4. Chạy `init-db.php` để khởi tạo CSDL

## 📖 Tài liệu

- [Hướng dẫn deploy](HUONG_DAN_DEPLOY.md)
- [Hướng dẫn CSDL](HUONG_DAN_CSDL.md)
- [Nhật ký sửa lỗi](NHAT_KY_SUA_LOI.md)
- [Tài liệu Auth](AUTH_DOCUMENTATION.md)
- [Desktop App Setup](desktop-app/INSTALL.md)

## 📝 License

MIT License

## 👤 Author

NguyenHuuKhanh - NHK Mobile
