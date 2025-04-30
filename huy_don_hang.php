<?php
require_once 'connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['orderId'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $connect = new Connect();
    $result = $connect->cancelOrder($_POST['orderId'], $_SESSION['user_id']);
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Đơn hàng đã được hủy thành công' : 'Không thể hủy đơn hàng'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
}