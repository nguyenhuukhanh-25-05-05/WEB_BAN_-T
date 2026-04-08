<?php
/**
 * Tệp chứa các hàm hỗ trợ xác thực (Authentication)
 * Quản lý trạng thái đăng nhập, phân quyền người dùng và admin.
 */

// Đảm bảo session luôn được khởi tạo với cấu hình bảo mật
if (session_status() === PHP_SESSION_NONE) {
    // Cấu hình Session sống lâu hơn (7 ngày) để tránh mất giỏ hàng/đăng nhập đột ngột
    ini_set('session.gc_maxlifetime', 604800);
    session_set_cookie_params(604800);
    session_start();
}

/**
 * Kiểm tra trạng thái đăng nhập tổng quát
 * 
 * @return bool Trả về true nếu là Thành viên hoặc Admin đã đăng nhập
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

/**
 * Kiểm tra xem người đang truy cập có quyền Admin hay không
 * 
 * @return bool Trả về true nếu là Admin
 */
function is_admin() {
    return isset($_SESSION['admin_id']);
}

/**
 * Lấy ID của người dùng (Thành viên) đang đăng nhập
 * 
 * @return int|null Trả về ID người dùng (số nguyên) hoặc null nếu chưa đăng nhập
 */
function get_logged_in_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy tên hiển thị của người đang đăng nhập
 * Ưu tiên hiển thị tên Admin nếu là Admin, ngược lại hiện tên Thành viên.
 * 
 * @return string Tên người dùng hoặc "Khách" nếu chưa đăng nhập
 */
function get_logged_in_name() {
    if (isset($_SESSION['admin_id'])) {
        return "Admin: " . ($_SESSION['admin_user'] ?? 'Quản trị viên');
    }
    return $_SESSION['user_fullname'] ?? 'Khách';
}

/**
 * Hàm bảo vệ trang cá nhân: Bắt buộc đăng nhập Thành viên hoặc Admin
 * Nếu chưa đăng nhập, tự động chuyển hướng sang trang login và lưu vị trí cũ.
 */
function require_login() {
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Hàm bảo vệ trang Admin: Chỉ cho phép tài khoản Admin truy cập
 * Nếu không có quyền Admin, chuyển hướng về login kèm thông báo lỗi.
 */
function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php?error=no_admin");
        exit;
    }
}
?>
