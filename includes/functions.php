<?php
/**
 * NHK Mobile - Core Utility Functions
 */

/**
 * Định dạng tiền tệ Việt Nam (VNĐ)
 */
function format_price($price) {
    return number_format($price, 0, ',', '.') . '₫';
}

/**
 * Rút gọn văn bản (dùng cho mô tả sản phẩm/tin tức)
 */
function excerpt($text, $limit = 100) {
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '...';
}

/**
 * Hiển thị Badge trạng thái đơn hàng với CSS class tương ứng
 */
function get_order_status_badge($status) {
    $class = 'bg-warning text-dark';
    $s = mb_strtolower($status, 'UTF-8');
    
    if (str_contains($s, 'đã duyệt')) $class = 'bg-info text-white';
    elseif (str_contains($s, 'đang giao')) $class = 'bg-primary text-white';
    elseif (str_contains($s, 'hoàn thành')) $class = 'bg-success text-white';
    elseif (str_contains($s, 'hủy')) $class = 'bg-danger text-white';
    
    return "<span class=\"badge $class border-0 px-3 py-1 rounded-pill small\">$status</span>";
}
?>