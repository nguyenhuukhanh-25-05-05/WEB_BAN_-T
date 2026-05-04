<?php
/**
 * NHK Mobile - Rule-based Chat API Endpoint
 * 
 * Trả lời tin nhắn khách hàng dựa trên kịch bản lưu trong CSDL.
 * Chạy 24/7 ổn định trên mọi hosting. Không cần API Key.
 * 
 * Author: NguyenHuuKhanh
 * Version: 4.0 (Quay lại Rule-based nâng cao)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Đọc input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message không được để trống']);
    exit;
}

// Giới hạn độ dài tin nhắn
if (mb_strlen($userMessage) > 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'Tin nhắn quá dài (tối đa 1000 ký tự)']);
    exit;
}

// Kết nối CSDL
require_once dirname(__DIR__) . '/includes/db.php';

if (!isset($pdo)) {
    echo json_encode(['reply' => 'Hệ thống đang bảo trì, vui lòng gọi hotline 0375 352 347 để được hỗ trợ ạ!']);
    exit;
}

try {
    // Chuyển tin nhắn về chữ thường để dễ tìm kiếm
    $lowerMessage = mb_strtolower($userMessage, 'UTF-8');
    
    // Lấy tất cả rule từ CSDL
    $stmt = $pdo->query("SELECT keyword, response FROM chatbot_rules ORDER BY LENGTH(keyword) DESC");
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $reply = null;
    
    // Tìm kiếm keyword trong tin nhắn
    foreach ($rules as $rule) {
        $keyword = mb_strtolower($rule['keyword'], 'UTF-8');
        // Nếu tin nhắn có chứa keyword
        if (mb_strpos($lowerMessage, $keyword) !== false) {
            $reply = $rule['response'];
            break; // Dừng lại ở keyword đầu tiên khớp (ưu tiên keyword dài hơn)
        }
    }
    
    // Nếu không khớp rule nào
    if (!$reply) {
        $reply = "Dạ, em chưa hiểu rõ ý của anh/chị. Anh/chị có thể hỏi cụ thể hơn về (giá, bảo hành, ship, trả góp, imei...) hoặc liên hệ hotline 0375 352 347 để được nhân viên tư vấn chi tiết hơn nhé!";
    }
    
    // Tạm dừng 1 giây để giả lập thời gian bot đang gõ chữ
    sleep(1);
    
    echo json_encode(['reply' => $reply], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("[NHK Chat] DB Error: " . $e->getMessage());
    echo json_encode(['reply' => 'Hệ thống đang bận, vui lòng thử lại sau ạ! 🙏']);
}
