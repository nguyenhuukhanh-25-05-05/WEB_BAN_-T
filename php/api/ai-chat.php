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

// 3. LẤY DỮ LIỆU SẢN PHẨM LÀM NGỮ CẢNH (Context) - Giới hạn 30 sản phẩm mới nhất
try {
    $stmt = $pdo->query("SELECT name, price, category, stock FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 30");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $productContext = "Sản phẩm tại cửa hàng NHK Mobile:\n";
    foreach ($products as $p) {
        $productContext .= "- " . $p['name'] . " (" . $p['category'] . "): " . number_format($p['price'], 0, ',', '.') . " VNĐ. Còn hàng.\n";
    }
} catch (Exception $e) {
    $productContext = "Không thể lấy danh sách sản phẩm.";
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
            'content' => "Bạn là trợ lý ảo chính thức của NHK Mobile.
                          Phong cách trả lời: Ngắn gọn, sang trọng, tập trung vào giá trị sản phẩm.
                          Ngữ cảnh sản phẩm hiện có:
                          $productContext
                          Nhiệm vụ:
                          - Tư vấn các dòng iPhone, Samsung, Xiaomi mới nhất.
                          - Nhắc về chương trình trả góp 0% ưu đãi.
                          - Nếu khách hỏi sản phẩm không có, hãy gợi ý dòng tương đương.
                          - Trả lời bằng tiếng Việt, thân thiện nhưng chuyên nghiệp."
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ],
    'temperature' => 0.6
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
    // Không lộ response thô ra ngoài cho user
    echo json_encode(['error' => 'Máy chủ AI đang bận, vui lòng thử lại sau.']);
}
?>