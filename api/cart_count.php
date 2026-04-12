<?php
/**
 * NHK Mobile - Cart Count API
 * Trả về số lượng sản phẩm trong giỏ hàng của user đang đăng nhập
 */
require_once '../includes/auth_functions.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Chưa đăng nhập -> giỏ hàng rỗng
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'logged_in' => false]);
    exit;
}

$userId = $_SESSION['user_id'];

// Cộng tổng số lượng từ DB theo user
$stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart_items WHERE user_id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch();

// Cũng lấy từ session nếu có (ưu tiên số thực tế hơn)
$sessionCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $sessionCount += isset($item['qty']) ? (int)$item['qty'] : 1;
    }
}

$count = max((int)$row['total'], $sessionCount);

echo json_encode(['count' => $count, 'logged_in' => true]);
