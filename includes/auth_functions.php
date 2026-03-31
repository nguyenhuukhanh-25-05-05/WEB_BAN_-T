<?php
/**
 * Các hàm hỗ trợ xác thực và bảo mật
 */

if (session_status() === PHP_SESSION_NONE) {
    // Cấu hình Session sống lâu hơn (7 ngày)
    ini_set('session.gc_maxlifetime', 604800);
    session_set_cookie_params(604800);
    session_start();
}

/**
 * Hàm rút gọn cho htmlspecialchars để ngăn chặn tấn công XSS
 * @param string|null $string Chuỗi cần escape
 * @return string Chuỗi đã được an toàn để hiển thị trong HTML
 */
function e($string) {
    if ($string === null) return '';
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Tạo và lấy CSRF Token để bảo vệ các biểu mẫu (Forms)
 * @return string CSRF Token
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Kiểm tra CSRF Token từ yêu cầu POST
 * @return bool Trả về true nếu token hợp lệ
 */
function verify_csrf_token() {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        return true;
    }
    return false;
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
