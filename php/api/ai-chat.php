<?php
// Tắt báo cáo lỗi trực tiếp để tránh lộ thông tin nhạy cảm
error_reporting(0);
header('Content-Type: application/json');

// 1. Nhúng cấu hình CSDL để lấy dữ liệu sản phẩm
require_once '../../includes/db.php';

// 2. CẤU HÌNH API GROK (XAI) - Lấy từ biến môi trường để bảo mật
$apiKey = getenv('XAI_API_KEY');
if (!$apiKey) $apiKey = $_ENV['XAI_API_KEY'] ?? $_SERVER['XAI_API_KEY'] ?? null;

define('XAI_API_KEY', $apiKey);
define('XAI_API_URL', 'https://api.x.ai/v1/chat/completions');

if (!XAI_API_KEY) {
    echo json_encode(['error' => 'API Key is not configured']);
    exit;
}

// 3. LẤY DỮ LIỆU SẢN PHẨM LÀM NGỮ CẢNH (Context)
try {
    $stmt = $pdo->query("SELECT name, price, category, stock FROM products WHERE stock > 0");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $productContext = "Danh sách sản phẩm hiện có tại cửa hàng NHK Mobile:\n";
    foreach ($products as $p) {
        $productContext .= "- " . $p['name'] . " (" . $p['category'] . "): " . number_format($p['price'], 0, ',', '.') . " VNĐ. Tình trạng: Còn hàng.\n";
    }
} catch (Exception $e) {
    $productContext = "Không thể lấy danh sách sản phẩm hiện tại.";
}

// 4. XỬ LÝ DỮ LIỆU TỪ FRONTEND
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['error' => 'Message is empty']);
    exit;
}

// 5. CHUẨN BỊ DỮ LIỆU GỬI ĐẾN XAI
$data = [
    'model' => 'grok-beta',
    'messages' => [
        [
            'role' => 'system',
            'content' => "Bạn là nhân viên tư vấn ảo của cửa hàng điện thoại NHK Mobile. 
                          Hãy trả lời khách hàng một cách lịch sự, thân thiện và chuyên nghiệp.
                          Dưới đây là thông tin sản phẩm của cửa hàng để bạn tư vấn:
                          $productContext
                          Nếu khách hỏi về sản phẩm không có trong danh sách, hãy khéo léo nói rằng hiện tại cửa hàng chưa có mã đó nhưng có những sản phẩm tương tự.
                          Luôn khuyến khích khách hàng mua trả góp 0% vì cửa hàng đang có chương trình này."
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ],
    'temperature' => 0.7
];

// 6. GỌI API BẰNG CURL
$ch = curl_init(XAI_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . XAI_API_KEY
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);
    $reply = $result['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi gặp chút trục trặc. Bạn thử lại nhé!';
    echo json_encode(['reply' => $reply]);
} else {
    echo json_encode(['error' => 'API Error', 'code' => $httpCode, 'details' => $response]);
}
?>