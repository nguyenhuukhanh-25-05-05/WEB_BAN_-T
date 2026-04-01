<?php
/**
 * Tệp chứa các hàm hỗ trợ xác thực (Authentication)
 */

if (session_status() === PHP_SESSION_NONE) {
    // Cấu hình Session sống lâu hơn (7 ngày)
    ini_set('session.gc_maxlifetime', 604800);
    session_set_cookie_params(604800);
    session_start();
}

/**
 * Kiểm tra xem có ai đang đăng nhập không (User hoặc Admin)
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

/**
 * Kiểm tra xem người đang đăng nhập có phải là Admin không
 */
function is_admin() {
    return isset($_SESSION['admin_id']);
}

/**
 * Lấy ID của người dùng đang đăng nhập
 */
function get_logged_in_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy tên của người đang đăng nhập
 */
function get_logged_in_name() {
    if (isset($_SESSION['admin_id'])) return "Admin: " . $_SESSION['admin_user'];
    return $_SESSION['user_fullname'] ?? 'Khách';
}

/**
 * Hàm bảo vệ trang: Yêu cầu đăng nhập User để tiếp tục
 */
function require_login() {
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Hàm bảo vệ trang: Chỉ dành cho Admin
 */
function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php?error=no_admin");
        exit;
    }
}
?>
