<?php
session_start();
require_once '../connect.php'; // Sửa đường dẫn

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

try {
    $result = $connect->saveChat(
        (int)$_POST['user_id'],
        trim($_POST['message']),
        1,
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