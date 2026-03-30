<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

try {
    $results = [];

    // Lấy sản phẩm
    $stmt = $pdo->prepare("SELECT id, name, price, image, category FROM products WHERE name ILIKE ? OR category ILIKE ? LIMIT 4");
    $stmt->execute(["%$q%", "%$q%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $item) {
        $item['formatted_price'] = number_format($item['price'], 0, ',', '.') . '₫';
        $item['url'] = "product-detail.php?id=" . $item['id'];
        $item['type'] = 'product';
        $results[] = $item;
    }

    // Lấy tin tức
    $stmt2 = $pdo->prepare("SELECT id, title as name, excerpt as formatted_price, image, category FROM news WHERE title ILIKE ? OR tags ILIKE ? LIMIT 4");
    $stmt2->execute(["%$q%", "%$q%"]);
    $news = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($news as $item) {
        $item['category'] = 'Tin tức \u2022 ' . $item['category'];
        $item['url'] = "news-detail.php?id=" . $item['id'];
        $item['type'] = 'news';
        if (mb_strlen($item['formatted_price']) > 50) {
            $item['formatted_price'] = mb_substr($item['formatted_price'], 0, 50) . '...';
        }
        $results[] = $item;
    }

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
