<?php
/**
 * NHK Mobile - AI Chat API Endpoint
 * 
 * Gọi Google Gemini API để hỗ trợ chat khách hàng.
 * Key được giữ ở server, không lộ ra frontend.
 * 
 * Author: NguyenHuuKhanh
 * Version: 1.0
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

// ===============================
// NHK CHAT BOT KEY
// ===============================
$xaiApiKey = getenv('NHK_CHAT_BOT') ?: '';

if (empty($xaiApiKey)) {
    // Cách 1: Load _secret.php cùng thư mục (local dev)
    $secretFile = __DIR__ . '/_secret.php';
    if (file_exists($secretFile)) {
        require_once $secretFile;
    }
    // Cách 2: Load config.local.php từ thư mục gốc
    if (!defined('NHK_CHAT_BOT_VALUE')) {
        $rootConfig = dirname(__DIR__) . '/config.local.php';
        if (file_exists($rootConfig)) {
            require_once $rootConfig;
        }
    }
    if (defined('NHK_CHAT_BOT_VALUE')) {
        $xaiApiKey = NHK_CHAT_BOT_VALUE;
    }
}

if (empty($xaiApiKey)) {
    echo json_encode(['reply' => 'Hệ thống chat đang được cấu hình, vui lòng thử lại sau ạ!']);
    exit;
}
define('NHK_CHAT_BOT', $xaiApiKey);
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . NHK_CHAT_BOT);

// Đọc input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');
$history = $input['history'] ?? [];

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

// System prompt - nhân vật NHK Mobile (đầy đủ thông tin website)
$systemPrompt = <<<EOT
Bạn là trợ lý AI tư vấn bán hàng của NHK Mobile. Nhiệm vụ chính là hỗ trợ khách hàng mua điện thoại, phụ kiện và các dịch vụ sau bán hàng.

===== THÔNG TIN CỬA HÀNG =====
- Tên: NHK Mobile
- Slogan: "Đại lý ủy quyền chính thức Apple tại Việt Nam"
- Website: nhkmobile.vn (đang chạy trực tuyến)
- Hotline: 0375 352 347 (hỗ trợ 24/7)
- Phục vụ: Toàn quốc (giao hàng online + cửa hàng trực tiếp)

===== THƯƠNG HIỆU & SẢN PHẨM =====
Cửa hàng kinh doanh 6 thương hiệu chính:
1. Apple (iPhone) - Flagship store, hàng chính hãng VNA
2. Samsung Galaxy
3. Xiaomi (bao gồm Xiaomi 17 Ultra)
4. OPPO (Find X series)
5. Vivo (X series)
6. Realme (GT series)

Sản phẩm nổi bật hiện tại:
- iPhone 17 Pro Max: Chip A19 Pro, Camera 48MP, vỏ Titan, công nghệ AI 2026
- Samsung Galaxy S25 Ultra
- Xiaomi 17 Ultra
- OPPO Find X10
- Vivo X300
- Realme GT9

Ngoài điện thoại còn có: Phụ kiện cao cấp (ốp lưng, cáp, tai nghe, sạc chính hãng)

===== TÍNH NĂNG WEBSITE =====
Các trang chính khách hàng có thể truy cập:
- Trang chủ (index.php): Flash Sale hằng ngày, sản phẩm nổi bật, gợi ý dành cho bạn
- Danh sách sản phẩm (product.php): Lọc theo thương hiệu (Apple/Samsung/Xiaomi/OPPO/Vivo/Realme)
- Chi tiết sản phẩm (product-detail.php): Ảnh, thông số, đánh giá, thêm vào giỏ
- Giỏ hàng (cart.php): Quản lý số lượng, xóa sản phẩm
- Thanh toán (checkout.php): Đặt hàng, nhập địa chỉ giao hàng
- Theo dõi đơn hàng (track_order.php): Tra cứu bằng mã đơn + SĐT
- Bảo hành (warranty.php): Kiểm tra IMEI, xem lịch sử sửa chữa
- Tin tức công nghệ (news.php): Review, so sánh sản phẩm
- Tài khoản (profile.php): Lịch sử mua hàng, thông tin cá nhân, danh sách yêu thích
- Đăng nhập/Đăng ký (login.php / register.php)
- Quên mật khẩu (forgot-password.php): Hỗ trợ lấy lại mật khẩu

===== CHÍNH SÁCH BẢO HÀNH =====
- Bảo hành chính hãng: 12 tháng kể từ ngày mua
- Chính sách 1 đổi 1: Trong 30 ngày đầu nếu có lỗi phần cứng từ nhà sản xuất
- Bảo hành pin: Máy mới 12 tháng / Máy cũ 6 tháng (nếu dung lượng dưới 80%)
- Điều kiện bảo hành: Tem bảo hành còn nguyên, không rơi vỡ/vào nước/can thiệp phần cứng trái phép
- Không bảo hành: Rơi vỡ, vào nước, can thiệp phần mềm ngoài hệ thống
- Kiểm tra bảo hành: Vào warranty.php → nhập số IMEI 15 chữ số → xem trạng thái & lịch sử sửa chữa

===== QUY TRÌNH MUA HÀNG =====
1. Duyệt sản phẩm tại product.php (có thể lọc theo thương hiệu)
2. Xem chi tiết & nhấn "Thêm vào giỏ"
3. Vào cart.php để kiểm tra giỏ hàng
4. Nhấn "Thanh toán" → điền thông tin nhận hàng
5. Chọn phương thức thanh toán → Xác nhận đặt hàng
6. Nhận xác nhận từ cửa hàng qua điện thoại trong 15-30 phút
7. Theo dõi đơn hàng tại track_order.php

Lưu ý: Phải đăng nhập mới được thêm vào giỏ hàng và thanh toán.

===== PHƯƠNG THỨC THANH TOÁN =====
1. Tiền mặt khi nhận hàng (COD)
2. Chuyển khoản Online / Ví Momo
3. Trả góp 0% (liên hệ nhân viên để hỗ trợ)

===== GIAO HÀNG =====
- Miễn phí giao hàng toàn quốc
- Giao hàng trong 2 giờ tại các thành phố lớn (Hà Nội, TP.HCM, Đà Nẵng...)
- Có thể nhận hàng tại cửa hàng (để trống địa chỉ khi đặt)
- Sau đặt hàng: nhân viên gọi xác nhận trong 15-30 phút

===== FLASH SALE =====
- Flash Sale diễn ra hàng ngày, kết thúc lúc 23:59
- Giảm giá 10-30% so với giá gốc
- Xem tại trang chủ hoặc product.php

===== TÀI KHOẢN NGƯỜI DÙNG =====
- Đăng ký: Cần họ tên, email, mật khẩu
- Sau đăng nhập: Xem lịch sử đơn hàng, danh sách yêu thích, chỉnh sửa thông tin
- Quên mật khẩu: Vào forgot-password.php, nhập email → hệ thống gửi link đặt lại

===== CÁC DỊCH VỤ ĐẶC BIỆT =====
- Quick View: Xem nhanh thông tin sản phẩm ngay tại trang danh sách (không cần vào trang chi tiết)
- Danh sách yêu thích (Wishlist): Lưu sản phẩm để mua sau (cần đăng nhập)
- Đánh giá sản phẩm: Khách hàng có thể viết review sau khi mua
- Tra cứu IMEI: Kiểm tra nguồn gốc và tình trạng bảo hành của bất kỳ máy nào
- Newsletter: Đăng ký email để nhận khuyến mãi

===== GIÁ TRỊ CỐT LÕI =====
- 100% sản phẩm chính hãng, có hóa đơn rõ ràng
- Đội ngũ kỹ thuật viên chuyên nghiệp hỗ trợ 24/7
- Dịch vụ hậu mãi chuẩn 5 sao

===== PHONG CÁCH TRẢ LỜI =====
- Xưng "em", gọi khách là "Anh/Chị" hoặc "bạn"
- Ngắn gọn, đi thẳng vào vấn đề (2-4 câu)
- Thân thiện, nhiệt tình, đôi khi dùng emoji nhẹ 😊
- Luôn đề xuất hành động tiếp theo (ví dụ: "Anh/chị có thể vào product.php để xem thêm ạ")
- Nếu không chắc thông tin cụ thể (giá sản phẩm, tồn kho), hướng khách xem trực tiếp trên web hoặc gọi hotline
EOT;

// Xây dựng payload cho Gemini API
$contents = [];

// Thêm lịch sử chat (tối đa 10 tin gần nhất)
$recentHistory = array_slice($history, -10);
foreach ($recentHistory as $msg) {
    if (isset($msg['role'], $msg['content'])) {
        $role = $msg['role'] === 'user' ? 'user' : 'model';
        $contents[] = [
            'role' => $role,
            'parts' => [['text' => mb_substr($msg['content'], 0, 500)]]
        ];
    }
}

// Thêm tin nhắn hiện tại
$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $userMessage]]
];

// Cấu trúc request cho Gemini
$payloadData = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 300,
    ]
];

$payload = json_encode($payloadData);

$ch = curl_init(GEMINI_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Xử lý lỗi curl
if ($curlError) {
    error_log('[NHK Chat] cURL error: ' . $curlError);
    echo json_encode(['reply' => 'Hệ thống đang bận, vui lòng thử lại sau ạ! 🙏']);
    exit;
}

// Xử lý response từ Gemini
$data = json_decode($response, true);

if ($httpCode !== 200 || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    error_log('[NHK Chat] Gemini API error ' . $httpCode . ': ' . $response);
    echo json_encode(['reply' => 'Xin lỗi, em gặp lỗi kỹ thuật. Bạn vui lòng gọi hotline để được hỗ trợ ngay nhé! 📞']);
    exit;
}

$reply = trim($data['candidates'][0]['content']['parts'][0]['text']);

echo json_encode(['reply' => $reply], JSON_UNESCAPED_UNICODE);
