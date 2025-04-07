<?php
require_once 'connect.php';
session_start();

// Set JSON header first
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$connect = new Connect();
$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? 0;

try {
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
        
        case 'remove_purchased_items':
            $items = $_POST['items'] ?? '';
            if (!empty($items)) {
                // Debug log to check received items
                error_log("Received items for removal: " . $items);
                
                // Split items only if it contains commas
                $itemsArray = strpos($items, ',') !== false ? explode(',', $items) : [$items];
                
                $purchasedProductIds = array_map(function($item) {
                    $parts = explode('_', $item);
                    return (int)$parts[0]; // Ensure we get integer ID
                }, $itemsArray);
                
                // Debug log to check product IDs to be removed
                error_log("Product IDs to remove: " . implode(', ', $purchasedProductIds));
                
                $success = $connect->removePurchasedItems($_SESSION['user_id'], $purchasedProductIds);
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Đã xóa các sản phẩm đã mua' : 'Không thể xóa sản phẩm'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không có sản phẩm để xóa'
                ]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xử lý: ' . $e->getMessage()
    ]);
}