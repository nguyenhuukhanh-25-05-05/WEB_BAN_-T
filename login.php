<?php
/**
 * NHK Mobile - Authentication Portal
 * 
 * Description: Unified login gateway for customers and administrators. 
 * Handles credential verification, hash validation, and session 
 * lifecycle management.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.1
 * Date: 2026-04-08
 */
require_once 'includes/auth_functions.php';
require_once 'includes/db.php';

// Initialization of authentication variables
$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_user = $_POST['email_or_user'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email_or_user) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tài khoản và mật khẩu.";
    } else {
        $stmt = $pdo->prepare("SELECT id, fullname, email, password, status FROM users WHERE email = ?");
        $stmt->execute([$email_or_user]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'banned') {
                $error = "Tài khoản của bạn đã bị khóa.";
            } else {
                // Xóa session admin nếu có (không để cả 2 vào cùng lúc)
                unset($_SESSION['admin_id'], $_SESSION['admin_user']);
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_fullname'] = $user['fullname'];
                $_SESSION['user_email']    = $user['email'];
                header("Location: " . $redirect);
                exit;
            }
        }

        $stmtAdmin = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmtAdmin->execute([$email_or_user]);
        $admin = $stmtAdmin->fetch();

        if ($admin) {
            if ($password === $admin['password'] || password_verify($password, $admin['password'])) {
                // Xóa session user nếu có (admin và user không được cùng session)
                unset($_SESSION['user_id'], $_SESSION['user_fullname'], $_SESSION['user_email']);
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_user'] = $admin['username'];
                header("Location: admin/dashboard.php");
                exit;
            }
        }

        $error = "Tài khoản hoặc mật khẩu không chính xác.";
    }
}

$pageTitle = "Đăng nhập | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<main class="min-vh-100 d-flex align-items-center justify-content-center bg-gray py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="bg-white p-5 rounded-4 shadow-lg border">
                    <div class="text-center mb-5">
                        <div class="nav-icon bg-primary-light text-primary mx-auto mb-4" style="width: 64px; height: 64px; font-size: 32px;">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <h2 class="fw-800 mb-2">Chào mừng trở lại</h2>
                        <p class="text-secondary small fw-500">Đăng nhập để tiếp tục trải nghiệm</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-3 small fw-600 mb-4"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php?redirect=<?php echo urlencode($redirect); ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Email / Username</label>
                            <input type="text" name="email_or_user" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Nhập tài khoản" required>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-800 text-muted text-uppercase letter-spacing mb-0">Mật khẩu</label>
                                <a href="#" class="text-primary small fw-700 text-decoration-none">Quên?</a>
                            </div>
                            <input type="password" name="password" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn-main btn-primary w-100 py-3 mb-4">Đăng nhập</button>
                        <div class="text-center">
                            <p class="text-secondary small fw-500 mb-0">Chưa có tài khoản? <a href="register.php" class="text-primary fw-800">Đăng ký ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
