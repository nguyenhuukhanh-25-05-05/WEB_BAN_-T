<?php
function syncCartWithDatabase($pdo) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $sessionId = session_id();
    $userId = $_SESSION['user_id'] ?? null;
    
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
    } else {
        foreach ($_SESSION['cart'] as $pid => $item) {
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (session_id, user_id, product_id, quantity) 
                VALUES (?, ?, ?, ?)
                ON CONFLICT (session_id, product_id) 
                DO UPDATE SET quantity = EXCLUDED.quantity, user_id = EXCLUDED.user_id
            ");
            $stmt->execute([$sessionId, $userId, $pid, $item['qty']]);
        }
        
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

function removeFromCartDB($pdo, $pid) {
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([session_id(), $pid]);
}
