<?php
require_once 'includes/db.php';

try {
    // 1. Tìm các sản phẩm trùng tên
    $sql = "SELECT name, COUNT(*) as count FROM products GROUP BY name HAVING COUNT(*) > 1";
    $stmt = $pdo->query($sql);
    $duplicates = $stmt->fetchAll();

    if (empty($duplicates)) {
        echo "Không tìm thấy sản phẩm nào bị trùng lặp.";
    } else {
        echo "<h2>Danh sách sản phẩm trùng lặp:</h2>";
        $pdo->beginTransaction();
        
        $totalDeleted = 0;
        foreach ($duplicates as $d) {
            $name = $d['name'];
            echo "- " . htmlspecialchars($name) . " (" . $d['count'] . " bản ghi)<br>";
            
            // Lấy tất cả ID của sản phẩm cùng tên này
            $stmtIds = $pdo->prepare("SELECT id FROM products WHERE name = ? ORDER BY id ASC");
            $stmtIds->execute([$name]);
            $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);
            
            $masterId = array_shift($ids); // Giữ lại ID đầu tiên làm "gốc"
            $duplicateIds = $ids; // Các ID còn lại sẽ bị xóa
            
            if (!empty($duplicateIds)) {
                $placeholders = implode(',', array_fill(0, count($duplicateIds), '?'));
                
                // 1. Chuyển các mục trong giỏ hàng sang ID gốc
                $pdo->prepare("UPDATE cart_items SET product_id = ? WHERE product_id IN ($placeholders)")
                    ->execute(array_merge([$masterId], $duplicateIds));
                
                // 2. Chuyển các mục trong đơn hàng sang ID gốc
                $pdo->prepare("UPDATE order_items SET product_id = ? WHERE product_id IN ($placeholders)")
                    ->execute(array_merge([$masterId], $duplicateIds));
                
                // 3. Chuyển các đánh giá sang ID gốc
                $pdo->prepare("UPDATE reviews SET product_id = ? WHERE product_id IN ($placeholders)")
                    ->execute(array_merge([$masterId], $duplicateIds));
                
                // 4. Chuyển các bản ghi bảo hành sang ID gốc
                $pdo->prepare("UPDATE warranties SET product_id = ? WHERE product_id IN ($placeholders)")
                    ->execute(array_merge([$masterId], $duplicateIds));
                
                // 5. Sau khi đã chuyển hết ràng buộc, tiến hành xóa sản phẩm trùng lặp
                $stmtDelete = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
                $stmtDelete->execute($duplicateIds);
                
                $totalDeleted += count($duplicateIds);
            }
        }
        
        $pdo->commit();
        echo "<br><strong>Đã xử lý xong! Đã xóa tổng cộng $totalDeleted bản ghi trùng lặp và đồng bộ hóa dữ liệu liên quan.</strong>";
    }
    
    echo "<br><br><a href='index.php'>Quay về trang chủ</a>";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Lỗi: " . $e->getMessage();
}
?>