<?php
/**
 * NHK Mobile - Cart Persistence Services
 * 
 * Description: Specialized logic for bidirectional synchronization 
 * between client-side PHP sessions and server-side database storage. 
 * Ensures cart contents persist across devices and sessions.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.2
 * Date: 2026-04-08
 */

/**
 * Đồng bộ giỏ hàng của người dùng với cơ sở dữ liệu.
 * - Nếu session rỗng: Tải dữ liệu giỏ hàng từ DB lên session.
 * - Nếu session có hàng: Lưu đè dữ liệu từ session xuống DB để đảm bảo tính đồng nhất.
 * 
 * @param PDO $pdo Đối tượng kết nối cơ sở dữ liệu
 */
function syncCartWithDatabase($pdo) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;

    // Chỉ đồng bộ khi đã đăng nhập
    if (!$userId) {
        // Chưa đăng nhập: xóa sạch giỏ hàng trong session (không load từ DB)
        $_SESSION['cart'] = [];
        return;
    }
    
    // TRƯỜNG HỢP 1: Session rỗng -> Nạp giỏ hàng từ DB theo user_id
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $sql = "SELECT ci.*, p.name, p.price, p.image FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();
        
        $_SESSION['cart'] = [];
        foreach ($items as $item) {
            $_SESSION['cart'][$item['product_id']] = [
                'name'  => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'qty'   => $item['quantity']
            ];
        }
    } 
    // TRƯỜNG HỢP 2: Session có hàng -> Lưu xuống DB theo user_id
    else {
        foreach ($_SESSION['cart'] as $pid => $item) {
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (user_id, product_id, quantity, session_id) 
                VALUES (?, ?, ?, '')
                ON CONFLICT (session_id, product_id) 
                DO UPDATE SET quantity = EXCLUDED.quantity, user_id = EXCLUDED.user_id
            ");
            $stmt->execute([$userId, $pid, $item['qty']]);
        }
        
        // Xóa những món trong DB mà session không giữ nữa
        $productIds = array_keys($_SESSION['cart']);
        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = $pdo->prepare("
                DELETE FROM cart_items 
                WHERE user_id = ? AND product_id NOT IN ($placeholders)
            ");
            $params = array_merge([$userId], $productIds);
            $stmt->execute($params);
        }
    }
}

/**
 * Xóa một sản phẩm cụ thể khỏi giỏ hàng trong cơ sở dữ liệu.
 * 
 * @param PDO $pdo Đối tượng kết nối cơ sở dữ liệu
 * @param int $pid ID của sản phẩm cần xóa
 */
function removeFromCartDB($pdo, $pid) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $pid]);
    }
}
?>
