<?php
/**
 * NHK Mobile - Authentication Portal
 *
 * Description: Unified login gateway for customers and administrators.
 * Handles credential verification, hash validation, and session
 * lifecycle management with enhanced security features.
 *
 * Author: NguyenHuuKhanh
 * Version: 3.0
 * Date: 2026-04-15
 */
require_once 'includes/auth_functions.php';
require_once 'includes/db.php';

// Initialization of authentication variables
$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

// Check session timeout
check_session_timeout();

// Generate CSRF token
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_user = sanitize_input($_POST['email_or_user'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token_post = $_POST['csrf_token'] ?? '';

    // Validate CSRF token
    if (!validate_csrf_token($csrf_token_post)) {
        $error = "Token bảo mật không hợp lệ. Vui lòng thử lại.";
        log_auth_attempt('login', $email_or_user, false, 'Invalid CSRF token');
    }
    // Check rate limiting
    elseif (!check_rate_limit('login', 5, 300)) {
        $remaining = get_rate_limit_remaining('login', 300);
        $error = "Quá nhiều lần thử. Vui lòng đợi " . ceil($remaining / 60) . " phút nữa.";
        log_auth_attempt('login', $email_or_user, false, 'Rate limited');
    }
    elseif (empty($email_or_user) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tài khoản và mật khẩu.";
    } else {
        $stmt = $pdo->prepare("SELECT id, fullname, email, password, status FROM users WHERE email = ?");
        $stmt->execute([$email_or_user]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'banned') {
                $error = "Tài khoản của bạn đã bị khóa.";
                log_auth_attempt('login', $email_or_user, false, 'Account banned');
            } else {
                // Clear rate limit on successful login
                clear_rate_limit('login');
                clear_csrf_token();
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Xóa session admin nếu có (không để cả 2 vào cùng lúc)
                unset($_SESSION['admin_id'], $_SESSION['admin_user']);
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_fullname'] = $user['fullname'];
                $_SESSION['user_email']    = $user['email'];
                $_SESSION['last_activity'] = time();
                
                log_auth_attempt('login', $email_or_user, true, 'User login successful');
                header("Location: " . $redirect);
                exit;
            }
        } else {
            $stmtAdmin = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $stmtAdmin->execute([$email_or_user]);
            $admin = $stmtAdmin->fetch();

            if ($admin) {
                if ($password === $admin['password'] || password_verify($password, $admin['password'])) {
                    // Clear rate limit on successful login
                    clear_rate_limit('login');
                    clear_csrf_token();
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Xóa session user nếu có (admin và user không được cùng session)
                    unset($_SESSION['user_id'], $_SESSION['user_fullname'], $_SESSION['user_email']);
                    $_SESSION['admin_id']   = $admin['id'];
                    $_SESSION['admin_user'] = $admin['username'];
                    $_SESSION['last_activity'] = time();
                    
                    // Redirect về admin/dashboard.php luôn — không phụ thuộc $redirect
                    $adminUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') === ''
                        ? '/admin/dashboard.php'
                        : 'admin/dashboard.php';
                    
                    log_auth_attempt('login', $email_or_user, true, 'Admin login successful');
                    
                    // SEED: Chèn đủ 30 sản phẩm nếu thiếu (tạm thời - xoá sau)
                    try {
                        $pdo->exec("
                            INSERT INTO products (name, category, price, stock, image, description, specs, is_featured) VALUES
                            ('iPhone 17 Pro Max', 'Apple', 32990000, 50, 'apple-iphone-17-pro-max.png', 'Siêu phẩm AI thế hệ mới với chip A19 Pro và camera đỉnh cao.', '256GB, 12GB RAM, A19 Pro, Camera 48MP', TRUE),
                            ('iPhone 16 Pro', 'Apple', 27990000, 40, 'apple-iphone-16-pro.png', 'iPhone 16 Pro với chip A18 Pro và màn hình ProMotion 120Hz.', '256GB, 8GB RAM, A18 Pro, Camera 48MP', TRUE),
                            ('iPhone 16e', 'Apple', 19990000, 35, 'apple-iphone-16e.png', 'iPhone nhỏ gọn thế hệ mới, hiệu năng mạnh mẽ.', '128GB, 8GB RAM, A16 Bionic', FALSE),
                            ('iPhone 15 Pro Max', 'Apple', 24990000, 25, 'apple-iphone-15-pro-max.png', 'Titan Design, Action Button, USB-C Pro.', '256GB, 8GB RAM, A17 Pro, Camera 48MP', FALSE),
                            ('Samsung Galaxy S25 Ultra', 'Samsung', 29490000, 30, 'samsung-galaxy-s25-ultra.png', 'Đỉnh cao màn hình vô cực, bút S Pen tích hợp AI.', '512GB, 16GB RAM, Snapdragon 8 Elite, S Pen', TRUE),
                            ('Samsung Galaxy S24 Ultra', 'Samsung', 22990000, 20, 'samsung-galaxy-s24-ultra.png', 'Galaxy AI đột phá, camera 200MP siêu nét.', '256GB, 12GB RAM, Snapdragon 8 Gen 3', TRUE),
                            ('Samsung Galaxy S23', 'Samsung', 14990000, 25, 'samsung-galaxy-s23.png', 'Hiệu năng ổn định, màn hình Dynamic AMOLED 120Hz.', '128GB, 8GB RAM, Snapdragon 8 Gen 2', FALSE),
                            ('Xiaomi 17 Ultra', 'Xiaomi', 24500000, 15, 'xiaomi-17-ultra.png', 'Camera Leica thế hệ 4, sạc nhanh HyperCharge 120W.', '512GB, 16GB RAM, Snapdragon 8 Elite, Leica Camera', TRUE),
                            ('Xiaomi 15T', 'Xiaomi', 15990000, 20, 'xiaomi-15t.png', 'Snapdragon 8s Gen 4, màn hình AMOLED 144Hz.', '256GB, 12GB RAM, Snapdragon 8s Gen 4', FALSE),
                            ('Xiaomi Mix Flip', 'Xiaomi', 21990000, 10, 'xiaomi-mix-flip.png', 'Điện thoại gập thời thượng, camera Leica, màn hình LTPO AMOLED.', '512GB, 12GB RAM, Snapdragon 8 Gen 3', FALSE),
                            ('OPPO Find X10', 'OPPO', 23990000, 12, 'oppo-find-x10.png', 'Camera Hasselblad thế hệ mới, sạc nhanh 100W SUPERVOOC.', '512GB, 16GB RAM, Dimensity 9400, Hasselblad', TRUE),
                            ('OPPO K300', 'OPPO', 11990000, 22, 'oppo-k300.png', 'Hiệu năng mạnh mẽ tầm trung, pin 6000mAh.', '256GB, 12GB RAM, Snapdragon 7s Gen 3', FALSE),
                            ('OPPO Mix Flip 5090', 'OPPO', 26990000, 8, 'oppo-mix-flip-5090.png', 'Điện thoại gập cao cấp với chip Snapdragon 8 Elite.', '512GB, 16GB RAM, Snapdragon 8 Elite, Gập đôi', FALSE),
                            ('OnePlus 13', 'OnePlus', 15500000, 20, 'oneplus-13.png', 'Sạc siêu nhanh 100W, Hasselblad Camera, Snapdragon 8 Gen 3.', '256GB, 12GB RAM, Snapdragon 8 Gen 3, Hasselblad', FALSE),
                            ('OnePlus 15', 'OnePlus', 19990000, 15, 'oneplus-15.png', 'OnePlus 15 với chip Snapdragon 8 Elite, màn hình ProXDR.', '256GB, 12GB RAM, Snapdragon 8 Elite', FALSE),
                            ('OnePlus 15R', 'OnePlus', 12990000, 18, 'oneplus-15r.png', 'Hiệu năng cao tầm trung, sạc nhanh 80W SuperVOOC.', '128GB, 8GB RAM, Snapdragon 7+ Gen 3', FALSE),
                            ('Realme GT 9', 'Realme', 13990000, 18, 'realme-gt9.png', 'Gaming phone mạnh mẽ, màn hình 144Hz, sạc 120W.', '256GB, 12GB RAM, Snapdragon 8s Gen 3', FALSE),
                            ('Realme GT 8 Pro', 'Realme', 17990000, 12, 'realme-gt8-pro.png', 'Camera 50MP Sony IMX906, chip Snapdragon 8 Gen 3.', '512GB, 16GB RAM, Snapdragon 8 Gen 3', FALSE),
                            ('Realme GT 8 Pro Blue', 'Realme', 17490000, 10, 'realme-gt8-pro-blue.png', 'Phiên bản màu xanh Ocean đặc biệt, camera Sony IMX906.', '256GB, 12GB RAM, Snapdragon 8 Gen 3', FALSE),
                            ('Realme GT 7', 'Realme', 11490000, 20, 'realme-gt7.png', 'Pin 6000mAh, sạc 120W, màn hình 144Hz sắc nét.', '256GB, 8GB RAM, Dimensity 9300+', FALSE),
                            ('Vivo X300 Pro', 'Vivo', 20990000, 10, 'vivo-x300.png', 'Camera periscope 200MP, chip Dimensity 9400, sạc 90W.', '512GB, 16GB RAM, Dimensity 9400, 200MP Periscope', FALSE),
                            ('Vivo X200', 'Vivo', 18490000, 14, 'vivo-x200-black.png', 'Camera Zeiss 50MP, chip Dimensity 9300, pin 5800mAh.', '256GB, 16GB RAM, Dimensity 9300, Zeiss Camera', FALSE),
                            ('Honor Magic 10', 'Honor', 19490000, 12, 'honor-magic-10.png', 'AI Camera thông minh, Snapdragon 8 Gen 3, màn hình OLED 120Hz.', '512GB, 16GB RAM, Snapdragon 8 Gen 3', FALSE),
                            ('Honor Magic 9', 'Honor', 16490000, 16, 'honor-magic-9.png', 'Snapdragon 8 Gen 2, camera 200MP, sạc nhanh 66W.', '256GB, 12GB RAM, Snapdragon 8 Gen 2, 200MP', FALSE),
                            ('Nubia Magic 15', 'Nubia', 17990000, 8, 'nubia-magic-15.png', 'Gaming phone chuyên dụng, tản nhiệt ICE 6.0, 165Hz UltraTouch.', '512GB, 16GB RAM, Snapdragon 8 Gen 3, Gaming', FALSE),
                            ('Nubia V1000', 'Nubia', 22990000, 6, 'nubia-v1000.png', 'Pin siêu khủng 10000mAh, sạc nhanh 100W, màn hình 120Hz.', '256GB, 12GB RAM, Snapdragon 7 Gen 3, 10000mAh', FALSE),
                            ('Nubia V90', 'Nubia', 9990000, 20, 'nubia-v90.png', 'Pin 6000mAh bền bỉ, màn hình 90Hz, giá tầm trung hợp lý.', '128GB, 8GB RAM, Snapdragon 4 Gen 2, 6000mAh', FALSE)
                            ON CONFLICT (name) DO NOTHING
                        ");
                    } catch (\PDOException $e) {}

                    header("Location: " . $adminUrl);
                    exit;
                }
            }
            
            $error = "Tài khoản hoặc mật khẩu không chính xác.";
            log_auth_attempt('login', $email_or_user, false, 'Invalid credentials');
        }
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
                <div class="auth-card bg-white p-5 rounded-4 shadow-lg border">
                    <div class="text-center mb-5">
                        <div class="nav-icon bg-primary-light text-primary mx-auto mb-4" style="width: 64px; height: 64px; font-size: 32px;">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <h2 class="fw-800 mb-2">Chào mừng trở lại</h2>
                        <p class="text-secondary small fw-500">Đăng nhập để tiếp tục trải nghiệm</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-3 small fw-600 mb-4 auth-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php?redirect=<?php echo urlencode($redirect); ?>" method="POST" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase letter-spacing">Email / Username</label>
                            <input type="text" name="email_or_user" class="form-control bg-light border-0 py-3 px-4 rounded-3 auth-input" placeholder="Nhập tài khoản" required autocomplete="email">
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-800 text-muted text-uppercase letter-spacing mb-0">Mật khẩu</label>
                                <a href="forgot-password.php" class="text-primary small fw-700 text-decoration-none">Quên?</a>
                            </div>
                            <input type="password" name="password" id="loginPassword" class="form-control bg-light border-0 py-3 px-4 rounded-3 auth-input" placeholder="••••••••" required autocomplete="current-password">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="showLoginPassword">
                                <label class="form-check-label small text-muted" for="showLoginPassword">
                                    Hiển thị mật khẩu
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn-main btn-primary w-100 py-3 mb-4 auth-btn">
                            <span class="btn-text">Đăng nhập</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Đang đăng nhập...
                            </span>
                        </button>
                        <div class="text-center">
                            <p class="text-secondary small fw-500 mb-0">Chưa có tài khoản? <a href="register.php" class="text-primary fw-800 auth-link">Đăng ký ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.auth-card {
    opacity: 0;
    transform: translateY(30px);
    animation: slideUp 0.6s ease-out forwards;
}

@keyframes slideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-input {
    transition: all 0.3s ease;
}

.auth-input:focus {
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
    transform: translateY(-2px);
}

.auth-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 122, 255, 0.3);
}

.auth-btn:active {
    transform: translateY(0);
}

.auth-link {
    position: relative;
    transition: all 0.3s ease;
}

.auth-link:hover {
    transform: scale(1.05);
}

.auth-error {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
    20%, 40%, 60%, 80% { transform: translateX(10px); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const showPasswordCheckbox = document.getElementById('showLoginPassword');
    const passwordInput = document.getElementById('loginPassword');
    const submitBtn = loginForm.querySelector('.auth-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Show/hide password toggle
    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }

    // Form submission with loading state
    loginForm.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }

        // Show loading state
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
    });

    // Add subtle focus animations
    const inputs = document.querySelectorAll('.auth-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
