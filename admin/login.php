<?php
// Bắt đầu phiên làm việc
session_start();

/**
 * TRANG ĐĂNG NHẬP ADMIN (PHP THUẦN)
 * Xử lý kiểm tra tài khoản và lưu vào Session
 */

if (isset($_POST['login'])) {
    // Lấy dữ liệu từ Form gửi lên
    $user = $_POST['username'];
    $pass = $_POST['password'];

    /**
     * TÀI KHOẢN MẶC ĐỊNH CHO ĐỒ ÁN
     * User: admin | Pass: admin123
     * Ghi chú: Có thể nâng cấp lấy từ bảng 'users' trong CSDL
     */
    if ($user == 'admin' && $pass == 'admin123') {
        // Nếu đúng, lưu biến đánh dấu vào Session
        $_SESSION['admin_logged_in'] = true;
        
        // Chuyển hướng sang trang Dashboard chính
        header("Location: dashboard.php");
        exit;
    } else {
        // Nếu sai, báo lỗi
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Hệ thống Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    
    <div class="login-container">
        <div class="card shadow-lg border-0 rounded-4 p-5" style="width: 420px;">
            <div class="text-center mb-5">
                 <img src="../assets/images/logo-k.svg" height="35" class="mb-4 d-block mx-auto">
                 <h4 class="fw-bold text-dark">Quản Trị Hệ Thống</h4>
                 <p class="text-secondary small">Vui lòng đăng nhập để tiếp tục.</p>
            </div>
            
            <!-- Hiển thị lỗi nếu đăng nhập sai -->
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 small py-3 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Form PHP gửi phương thức POST về chính nó -->
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-person text-secondary"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-0 rounded-end-3 py-2" placeholder="admin" required>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="form-label small fw-bold text-secondary">Mật khẩu bảo mật</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-secondary"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-0 rounded-end-3 py-2" placeholder="••••••••" required>
                    </div>
                </div>
                <!-- Nút Submit của Form PHP -->
                <button type="submit" name="login" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                    Đăng nhập ngay <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-5">
                <a href="../index.php" class="text-secondary small text-decoration-none border-bottom">
                    <i class="bi bi-box-arrow-left"></i> Quay lại cửa hàng
                </a>
            </div>
        </div>
        <p class="text-center text-secondary small mt-4 opacity-50">© 2026 NHK Mobile Administration</p>
    </div>

</body>
</html>
