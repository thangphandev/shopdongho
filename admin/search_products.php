<?php
session_start();
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['keyword']) || empty(trim($_POST['keyword']))) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập từ khóa']);
    exit();
}

$connect = new Connect();
$keyword = trim($_POST['keyword']);
$products = $connect->searchProducts($keyword, [], [], [], [], [], 0, PHP_INT_MAX);

echo json_encode([
    'success' => true,
    'products' => $products
]);