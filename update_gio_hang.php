<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$connect = new Connect();
$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? 0;

switch($action) {
    case 'update':
        $quantity = $_POST['quantity'] ?? 1;
        $success = $connect->updatesoluongsanpham($_SESSION['user_id'], $productId, $quantity);
        echo json_encode(['success' => $success]);
        break;
        
    case 'remove':
        $success = $connect->removesanphamtronggiohang($_SESSION['user_id'], $productId);
        echo json_encode(['success' => $success]);
        break;

    case 'add':
        $quantity = $_POST['quantity'] ?? 1;
        $success = $connect->addToCart($_SESSION['user_id'], $productId, $quantity);
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}