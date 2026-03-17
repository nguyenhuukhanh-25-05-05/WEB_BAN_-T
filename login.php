<?php
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_user = $_POST['email_or_user'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email_or_user) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tài khoản và mật khẩu.";
    } else {
        // 1. Thử đăng nhập User (Email)
        $stmt = $pdo->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
        $stmt->execute([$email_or_user]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_fullname'] = $user['fullname'];
            header("Location: " . $redirect);
            exit;
        }

        // 2. Thử đăng nhập Admin (Username)
        $stmtAdmin = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmtAdmin->execute([$email_or_user]);
        $admin = $stmtAdmin->fetch();

        if ($admin) {
            // Kiểm tra password (bản demo chấp nhận cả text thường 'admin123' cho admin)
            if ($password === $admin['password'] || password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
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

<main class="min-vh-100 d-flex align-items-center justify-content-center bg-dark-deep py-huge">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 animate-reveal">
                <div class="card-glass-product p-5 p-lg-10 shadow-2xl">
                    <div class="text-center mb-5">
                        <h2 class="display-6 fw-bold text-white mb-2">Chào mừng trở lại.</h2>
                        <p class="text-secondary">Đăng nhập để điều khiển thế giới công nghệ của bạn.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger glass-badge border-danger text-danger mb-4"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'no_admin'): ?>
                        <div class="alert alert-warning glass-badge border-warning text-warning mb-4">Bạn cần quyền quản trị để truy cập trang này.</div>
                    <?php endif; ?>

                    <form action="login.php?redirect=<?php echo urlencode($redirect); ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label text-white small fw-bold">Email hoặc Username *</label>
                            <input type="text" name="email_or_user" class="form-control btn-premium-glass py-3 px-4" placeholder="Nhập email hoăc admin" required>
                        </div>
                        <div class="mb-5">
                            <label class="form-label text-white small fw-bold d-flex justify-content-between">
                                Mật khẩu *
                                <a href="#" class="text-secondary x-small">Quên mật khẩu?</a>
                            </label>
                            <input type="password" name="password" class="form-control btn-premium-glass py-3 px-4" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-premium-dark w-100 py-3 fw-bold shadow-lg mb-4">Đăng nhập</button>
                        <div class="text-center">
                            <span class="text-secondary small">Chưa có tài khoản? </span>
                            <a href="register.php" class="text-white small fw-bold text-decoration-none border-bottom">Tạo tài khoản</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
