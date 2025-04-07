<?php
// Disable error reporting for production
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    error_log("Order Data Received: " . print_r($data, true));
    $connect = new Connect();

    // Format order data
    $orderData = [
        'fullname' => $data['fullname'],
        'phone' => $data['phone'],
        'full_address' => $data['address'],
        'total_amount' => $data['total_amount'],
        'type' => $data['type']
    ];
    // Debug log
    error_log("Formatted Order Data: " . print_r($orderData, true));
    error_log("Products Data: " . print_r($products, true));

    // Get products based on order type
    $products = [];
    if ($data['type'] === 'direct') {
        if (isset($_GET['productId'])) {
            $productId = (int)$_GET['productId'];
            $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
        } else {
            list($productId, $quantity) = explode('_', $data['items']);
            $productId = (int)$productId;
            $quantity = (int)$quantity;
        }

        $product = $connect->getProductDetails($productId);
        if ($product) {
            $price = !empty($product['gia_giam']) ? 
                     ($product['giaban'] - $product['gia_giam']) : 
                     $product['giaban'];
            $products[] = [
                'idsanpham' => $productId,
                'quantity' => $quantity,
                'price' => $price
            ];
        }
    } else if ($data['type'] === 'cart' && !empty($data['items'])) {
        $items = explode(',', $data['items']);
        foreach ($items as $item) {
            list($productId, $quantity) = explode('_', $item);
            $product = $connect->getProductDetails((int)$productId);
            if ($product) {
                $price = !empty($product['gia_giam']) ? 
                         ($product['giaban'] - $product['gia_giam']) : 
                         $product['giaban'];
                $products[] = [
                    'idsanpham' => (int)$productId,
                    'quantity' => (int)$quantity,
                    'price' => $price
                ];
            }
        }
    }

    if (empty($products)) {
        throw new Exception('No valid products found');
    }

    // Create the order
    $result = $connect->createOrder(
        $_SESSION['user_id'], 
        $orderData, 
        $products, 
        $data['payment_method']
    );
    error_log("Order Creation Result: " . print_r($result, true));

    if ($result['success'] && $data['type'] === 'cart') {
        try {
            // Extract just the product IDs from the items
            $purchasedProductIds = array_map(function($item) {
                return explode('_', $item)[0];
            }, explode(',', $data['items']));
            
            $connect->removePurchasedItems($_SESSION['user_id'], $purchasedProductIds);
        } catch (Exception $e) {
            error_log("Error removing purchased items: " . $e->getMessage());
        }
    }

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}