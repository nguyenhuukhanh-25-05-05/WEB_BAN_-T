<?php
/**
 * Test Cart & Checkout Functionality
 */
session_start();
require_once 'includes/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Test Cart</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .test-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f9f9f9; padding: 10px; border-radius: 4px; overflow-x: auto; }
        a { display: inline-block; margin: 10px 10px 10px 0; padding: 10px 20px; background: #007AFF; color: white; text-decoration: none; border-radius: 6px; }
        a:hover { background: #0056CC; }
    </style>
</head>
<body>
    <h1>🧪 Test Cart & Checkout System</h1>";

// Test 1: Session
echo "<div class='test-box'>";
echo "<h2>1. Session Test</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✅ Session is ACTIVE</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} else {
    echo "<p class='error'>❌ Session is NOT active</p>";
}
echo "</div>";

// Test 2: Database Connection
echo "<div class='test-box'>";
echo "<h2>2. Database Connection</h2>";
try {
    $pdo->query("SELECT 1");
    echo "<p class='success'>✅ Database connected</p>";
    
    // Check products table
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $productCount = $stmt->fetchColumn();
    echo "<p class='info'>📦 Total products: $productCount</p>";
    
    // Get first product
    $stmt = $pdo->query("SELECT id, name, price, stock FROM products LIMIT 1");
    $firstProduct = $stmt->fetch();
    if ($firstProduct) {
        echo "<p>First product: <strong>{$firstProduct['name']}</strong></p>";
        echo "<p>Price: " . number_format($firstProduct['price'], 0, ',', '.') . "₫</p>";
        echo "<p>Stock: {$firstProduct['stock']}</p>";
        echo "<p><a href='cart.php?add={$firstProduct['id']}'>➕ Add to Cart (ID: {$firstProduct['id']})</a></p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 3: Cart
echo "<div class='test-box'>";
echo "<h2>3. Cart Contents</h2>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<p class='success'>✅ Cart has items</p>";
    echo "<pre>" . print_r($_SESSION['cart'], true) . "</pre>";
    
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $subtotal = $item['price'] * $item['qty'];
        $total += $subtotal;
        echo "<p>📱 {$item['name']} x {$item['qty']} = " . number_format($subtotal, 0, ',', '.') . "₫</p>";
    }
    echo "<p><strong>Total: " . number_format($total, 0, ',', '.') . "₫</strong></p>";
    echo "<p><a href='checkout.php'>💳 Go to Checkout</a></p>";
    echo "<p><a href='cart.php?remove=" . array_key_first($_SESSION['cart']) . "'>🗑️ Remove first item</a></p>";
    echo "<p><a href='cart.php'>🛒 View Cart Page</a></p>";
} else {
    echo "<p class='error'>❌ Cart is EMPTY</p>";
    echo "<p>Try clicking 'Add to Cart' button above</p>";
}
echo "</div>";

// Test 4: User Login
echo "<div class='test-box'>";
echo "<h2>4. User Login Status</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p class='success'>✅ User logged in (ID: {$_SESSION['user_id']})</p>";
    echo "<p><a href='logout.php'>🚪 Logout</a></p>";
} elseif (isset($_SESSION['admin_id'])) {
    echo "<p class='info'>ℹ️ Admin logged in (ID: {$_SESSION['admin_id']})</p>";
    echo "<p class='info'>Admin cannot checkout - please logout and login as user</p>";
    echo "<p><a href='admin/logout.php'>🚪 Admin Logout</a></p>";
} else {
    echo "<p class='error'>❌ Not logged in</p>";
    echo "<p>Checkout requires login</p>";
    echo "<p><a href='login.php'>🔐 Login</a></p>";
    echo "<p><a href='register.php'>📝 Register</a></p>";
}
echo "</div>";

// Test 5: Cart Functions
echo "<div class='test-box'>";
echo "<h2>5. Cart Functions Test</h2>";
require_once 'includes/cart_functions.php';

echo "<p>Testing syncCartWithDatabase()...</p>";
try {
    syncCartWithDatabase($pdo);
    echo "<p class='success'>✅ Cart sync successful</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Cart sync error: " . $e->getMessage() . "</p>";
}

echo "<p>Session cart after sync:</p>";
echo "<pre>" . print_r($_SESSION['cart'] ?? 'Not set', true) . "</pre>";
echo "</div>";

// Test 6: Quick Actions
echo "<div class='test-box'>";
echo "<h2>6. Quick Actions</h2>";
echo "<a href='index.php'>🏠 Home</a>";
echo "<a href='product.php'>📱 Products</a>";
echo "<a href='cart.php'>🛒 Cart</a>";
echo "<a href='checkout.php'>💳 Checkout</a>";
echo "<a href='admin/dashboard.php'>👨‍💼 Admin</a>";
echo "<p style='margin-top: 20px;'><a href='test_cart.php' style='background: #28a745;'>🔄 Refresh Test</a></p>";
echo "</div>";

echo "</body></html>";
?>
