<?php
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ các thông tin bắt buộc (*).";
    } else {
        // Kiểm tra email tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email này đã được đăng ký. Vui lòng sử dụng email khác.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $email, $hashedPassword, $phone, $address]);
                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } catch (PDOException $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = "Đăng ký tài khoản | NHK Mobile";
$basePath = "";
include 'includes/header.php';
?>

<main class="min-vh-100 d-flex align-items-center justify-content-center bg-dark-deep py-huge">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 animate-reveal">
                <div class="card-glass-product p-5 p-lg-10 shadow-2xl">
                    <div class="text-center mb-5">
                        <h2 class="display-6 fw-bold text-white mb-2">Tạo tài khoản mới.</h2>
                        <p class="text-secondary">Trở thành thành viên để nhận nhiều ưu đãi hơn.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger glass-badge border-danger text-danger mb-4"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success glass-badge border-success text-success mb-4">
                            <?php echo $success; ?>
                            <br><a href="login.php" class="fw-bold text-white text-decoration-underline">Đăng nhập ngay</a>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label text-white small fw-bold">Họ và tên *</label>
                            <input type="text" name="fullname" class="form-control btn-premium-glass py-3 px-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff !important;" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-white small fw-bold">Email *</label>
                            <input type="email" name="email" class="form-control btn-premium-glass py-3 px-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff !important;" placeholder="email@vi-du.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-white small fw-bold">Mật khẩu *</label>
                            <div class="position-relative">
                                <input type="password" id="password" name="password" class="form-control btn-premium-glass py-3 px-4 pe-5" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff !important; letter-spacing: 2px;" placeholder="••••••••" minlength="6" required>
                                <button type="button" id="togglePassword" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary me-2 p-0 border-0 shadow-none">
                                    <i class="bi bi-eye fs-5" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-white small fw-bold">Số điện thoại</label>
                            <input type="tel" name="phone" id="phone" class="form-control btn-premium-glass py-3 px-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff !important;" placeholder="09xx xxx xxx">
                        </div>
                        <div class="mb-5">
                            <label class="form-label text-white small fw-bold">Địa chỉ giao hàng</label>
                            <textarea name="address" class="form-control btn-premium-glass py-3 px-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff !important;" rows="2" placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-premium-dark w-100 py-3 fw-bold shadow-lg mb-4">Đăng ký ngay</button>
                        <div class="text-center">
                            <span class="text-secondary small">Đã có tài khoản? </span>
                            <a href="login.php" class="text-white small fw-bold text-decoration-none border-bottom">Đăng nhập</a>
                        </div>
                    </form>

                    <script>
                        const togglePassword = document.querySelector('#togglePassword');
                        const password = document.querySelector('#password');
                        const toggleIcon = document.querySelector('#toggleIcon');

                        togglePassword.addEventListener('click', function (e) {
                            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                            password.setAttribute('type', type);
                            toggleIcon.classList.toggle('bi-eye');
                            toggleIcon.classList.toggle('bi-eye-slash');
                            
                            if (type === 'text') {
                                password.style.letterSpacing = 'normal';
                            } else {
                                password.style.letterSpacing = '2px';
                            }
                        });

                        // Real-time phone validation (Basic)
                        const phoneInput = document.querySelector('#phone');
                        phoneInput.addEventListener('input', function(e) {
                            this.value = this.value.replace(/[^0-9]/g, '');
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
