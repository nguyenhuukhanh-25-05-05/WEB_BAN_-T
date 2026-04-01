<?php
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email này đã được sử dụng.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $hashedPassword])) {
                $success = "Đăng ký thành công! Đang chuyển hướng...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    }
}

$pageTitle = "Đăng ký | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<main class="min-vh-100 d-flex align-items-center justify-content-center bg-gray py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="bg-white p-5 rounded-4 shadow-lg border">
                    <div class="text-center mb-5">
                        <div class="nav-icon bg-primary-light text-primary mx-auto mb-4" style="width: 64px; height: 64px; font-size: 32px;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h2 class="fw-800 mb-2">Tạo tài khoản mới</h2>
                        <p class="text-secondary small fw-500">Trở thành thành viên của NHK Mobile</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-3 small fw-600 mb-4"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 rounded-3 small fw-600 mb-4"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="example@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Mật khẩu</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="••••••••" required>
                        </div>
                        <div class="mb-5">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Xác nhận mật khẩu</label>
                            <input type="password" name="confirm_password" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn-main btn-primary w-100 py-3 mb-4">Đăng ký tài khoản</button>
                        <div class="text-center">
                            <p class="text-secondary small fw-500 mb-0">Đã có tài khoản? <a href="login.php" class="text-primary fw-800">Đăng nhập</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
