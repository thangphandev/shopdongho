<?php
session_start();
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['message']) || empty(trim($_POST['message']))) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

$connect = new Connect();
$currentTime = date('Y-m-d H:i:s');
$message = trim($_POST['message']);
$type = isset($_POST['type']) ? $_POST['type'] : 'text';

// Nếu là tin nhắn sản phẩm, giữ nguyên HTML
if ($type === 'product') {
    // Không mã hóa HTML để giữ cấu trúc product-list
    $message = $message;
} else {
    // Mã hóa HTML cho tin nhắn thường để tránh XSS
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}

try {
    $result = $connect->saveChat(
        (int)$_POST['user_id'],
        $message,
        1, // role = 1 for admin
        $currentTime
    );
    
    echo json_encode([
        'success' => true,
        'time' => $currentTime
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lưu tin nhắn'
    ]);
}