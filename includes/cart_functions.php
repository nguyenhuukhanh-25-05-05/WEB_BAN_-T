<?php
function syncCartWithDatabase($pdo) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $sessionId = session_id();
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $stmt = $pdo->prepare("
            SELECT ci.*, p.name, p.price, p.image 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = ?
        ");
        $stmt->execute([$sessionId]);
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
                INSERT INTO cart_items (session_id, product_id, quantity) 
                VALUES (?, ?, ?)
                ON CONFLICT (session_id, product_id) 
                DO UPDATE SET quantity = EXCLUDED.quantity
            ");
            $stmt->execute([$sessionId, $pid, $item['qty']]);
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
