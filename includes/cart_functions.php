<?php
/**
 * Tệp chứa các hàm quản lý giỏ hàng (Cart Functions)
 * Hỗ trợ đồng bộ giỏ hàng giữa Session (phía khách) và Database (phía chủ).
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
    
    $sessionId = session_id();
    $userId = $_SESSION['user_id'] ?? null;
    
    // TRƯỜNG HỢP 1: Giỏ hàng trong Session đang rỗng -> Tìm trong DB để nạp lên
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $sql = "SELECT ci.*, p.name, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.session_id = ?";
        $params = [$sessionId];
        if ($userId) {
            $sql = "SELECT ci.*, p.name, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.session_id = ? OR ci.user_id = ?";
            $params = [$sessionId, $userId];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();
        
        if ($items) {
            $_SESSION['cart'] = [];
            foreach ($items as $item) {
                $_SESSION['cart'][$item['product_id']] = [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'qty' => $item['quantity']
                ];
            }
        }
    } 
    // TRƯỜNG HỢP 2: Session đã có hàng -> Cập nhật xuống DB để lưu trữ bền vững
    else {
        foreach ($_SESSION['cart'] as $pid => $item) {
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (session_id, user_id, product_id, quantity) 
                VALUES (?, ?, ?, ?)
                ON CONFLICT (session_id, product_id) 
                DO UPDATE SET quantity = EXCLUDED.quantity, user_id = EXCLUDED.user_id
            ");
            $stmt->execute([$sessionId, $userId, $pid, $item['qty']]);
        }
        
        // Xóa những món trong DB mà Session không còn giữ (vì đã bị người dùng xóa)
        $productIds = array_keys($_SESSION['cart']);
        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = $pdo->prepare("
                DELETE FROM cart_items 
                WHERE session_id = ? AND product_id NOT IN ($placeholders)
            ");
            $params = array_merge([$sessionId], $productIds);
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
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([session_id(), $pid]);
}
?>
