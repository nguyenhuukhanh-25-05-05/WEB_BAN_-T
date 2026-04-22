<?php
/**
 * Test Login & Registration
 */
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_functions.php';

// Handle test actions
$action = $_GET['action'] ?? '';
$message = '';

if ($action === 'create_test_user') {
    try {
        $email = 'test@nhkmobile.com';
        $fullname = 'Test User';
        $password = 'Test@123456';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $message = "✅ Test user already exists!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$fullname, $email, $hashedPassword]);
            $message = "✅ Test user created successfully!";
        }
        
        echo "<div style='background: #d4edda; padding: 15px; margin: 20px; border-radius: 8px;'>";
        echo "<strong>📝 Test Account:</strong><br>";
        echo "Email: <code>{$email}</code><br>";
        echo "Password: <code>{$password}</code><br>";
        echo "Fullname: <code>{$fullname}</code>";
        echo "</div>";
        
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

if ($action === 'clear_session') {
    session_destroy();
    session_start();
    $message = "✅ Session cleared!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Test Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f7; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 30px; margin: 20px 0; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        h1 { color: #1d1d1f; margin-bottom: 10px; }
        h2 { color: #333; margin-bottom: 15px; font-size: 1.3rem; }
        p { color: #666; line-height: 1.6; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 15px 0; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #007AFF; color: white; text-decoration: none; border-radius: 8px; margin: 5px; border: none; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: #0056CC; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .test-account { background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin: 15px 0; }
        .steps { counter-reset: step; list-style: none; padding: 0; }
        .steps li { counter-increment: step; padding: 10px 0 10px 40px; position: relative; }
        .steps li::before { content: counter(step); position: absolute; left: 0; top: 10px; width: 28px; height: 28px; background: #007AFF; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Test Login System</h1>
        <p>Use this page to test and debug login functionality</p>

        <?php if ($message): ?>
            <div class="<?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Step 1: Create Test User -->
        <div class="card">
            <h2>1️⃣ Create Test User</h2>
            <p>First, create a test account in the database:</p>
            <a href="?action=create_test_user" class="btn btn-success">Create Test User</a>
            
            <div class="test-account">
                <strong>📝 Test Account Credentials:</strong><br><br>
                <p><strong>Email:</strong> <code>test@nhkmobile.com</code></p>
                <p><strong>Password:</strong> <code>Test@123456</code></p>
                <p><strong>Fullname:</strong> <code>Test User</code></p>
            </div>
        </div>

        <!-- Step 2: Test Login -->
        <div class="card">
            <h2>2️⃣ Test Login</h2>
            <p>Try logging in with the test account:</p>
            
            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div style="margin: 15px 0;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email or Username:</label>
                    <input type="text" name="email_or_user" value="test@nhkmobile.com" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                </div>
                <div style="margin: 15px 0;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Password:</label>
                    <input type="password" name="password" value="Test@123456" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                </div>
                <button type="submit" class="btn">Login via login.php</button>
            </form>
            
            <p style="margin-top: 15px;">Or test programmatically:</p>
            <a href="test_cart.php" class="btn btn-warning">Check Session & Cart</a>
        </div>

        <!-- Step 3: Current Session Status -->
        <div class="card">
            <h2>3️⃣ Current Session Status</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="success">
                    <strong>✅ User Logged In</strong><br><br>
                    <p><strong>User ID:</strong> <?= $_SESSION['user_id'] ?></p>
                    <p><strong>Fullname:</strong> <?= $_SESSION['user_fullname'] ?? 'N/A' ?></p>
                    <p><strong>Email:</strong> <?= $_SESSION['user_email'] ?? 'N/A' ?></p>
                    <p><strong>Last Activity:</strong> <?= date('Y-m-d H:i:s', $_SESSION['last_activity'] ?? time()) ?></p>
                </div>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            <?php elseif (isset($_SESSION['admin_id'])): ?>
                <div class="info">
                    <strong>ℹ️ Admin Logged In</strong><br><br>
                    <p><strong>Admin ID:</strong> <?= $_SESSION['admin_id'] ?></p>
                    <p><strong>Username:</strong> <?= $_SESSION['admin_user'] ?? 'N/A' ?></p>
                    <p><strong>Note:</strong> Admin cannot checkout. Please logout and login as user.</p>
                </div>
                <a href="admin/logout.php" class="btn btn-danger">Admin Logout</a>
            <?php else: ?>
                <div class="error">
                    <strong>❌ Not Logged In</strong><br><br>
                    <p>Please login first to test checkout functionality.</p>
                </div>
                <a href="login.php" class="btn">Go to Login Page</a>
                <a href="register.php" class="btn btn-success">Register New Account</a>
            <?php endif; ?>
            
            <p style="margin-top: 15px;">
                <a href="?action=clear_session" class="btn btn-warning">Clear Session</a>
                <a href="test_login.php" class="btn">Refresh Page</a>
            </p>
        </div>

        <!-- Step 4: Manual Test -->
        <div class="card">
            <h2>4️⃣ Manual Testing Steps</h2>
            <ol class="steps">
                <li>Click "Create Test User" button above</li>
                <li>Clear session if needed</li>
                <li>Login with test credentials (test@nhkmobile.com / Test@123456)</li>
                <li>Check session status in section 3</li>
                <li>Try adding product to cart from product-detail.php</li>
                <li>Go to cart.php and checkout</li>
            </ol>
        </div>

        <!-- Step 5: Debug Info -->
        <div class="card">
            <h2>5️⃣ Debug Information</h2>
            <p><strong>Session ID:</strong> <?= session_id() ?></p>
            <p><strong>Session Status:</strong> <?= session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive' ?></p>
            <p><strong>Session Name:</strong> <?= session_name() ?></p>
            
            <p style="margin-top: 15px;"><strong>Session Data:</strong></p>
            <pre><?= print_r($_SESSION, true) ?></pre>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <h2>🔗 Quick Links</h2>
            <a href="index.php" class="btn">🏠 Home</a>
            <a href="product.php" class="btn">📱 Products</a>
            <a href="cart.php" class="btn">🛒 Cart</a>
            <a href="checkout.php" class="btn">💳 Checkout</a>
            <a href="test_cart.php" class="btn btn-success">🧪 Test Cart</a>
            <a href="admin/dashboard.php" class="btn">👨‍💼 Admin</a>
        </div>
    </div>
</body>
</html>
