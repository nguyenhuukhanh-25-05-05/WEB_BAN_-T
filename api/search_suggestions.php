<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

try {
    // Tìm kiếm sản phẩm theo tên hoặc hãng
    $stmt = $pdo->prepare("SELECT id, name, price, image, category FROM products WHERE name ILIKE ? OR category ILIKE ? LIMIT 6");
    $stmt->execute(["%$q%", "%$q%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Định dạng lại giá tiền
    foreach ($results as &$item) {
        $item['formatted_price'] = number_format($item['price'], 0, ',', '.') . '₫';
    }

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
