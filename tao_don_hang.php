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
    // Validate input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = ['fullname', 'phone', 'address', 'payment_method', 'total_amount', 'type'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Validate phone number format
    if (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
        throw new Exception('Invalid phone number format');
    }

    $connect = new Connect();

    // Format order data with additional fields
    $orderData = [
        'fullname' => strip_tags(trim($data['fullname'])),
        'phone' => trim($data['phone']),
        'full_address' => strip_tags(trim($data['address'])),
        'total_amount' => floatval($data['total_amount']),
        'type' => $data['type'],
        'payment_method' => $data['payment_method'],
        'order_date' => date('Y-m-d H:i:s'),
        'status' => 'pending',
        'user_id' => $_SESSION['user_id']
    ];

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

        // Validate product existence and stock
        $product = $connect->getProductDetails($productId);
        if (!$product) {
            throw new Exception('Product not found');
        }
        if ($product['soluong'] < $quantity) {
            throw new Exception('Insufficient stock');
        }

        $price = !empty($product['gia_giam']) ? 
                 ($product['giaban'] - $product['gia_giam']) : 
                 $product['giaban'];
        $products[] = [
            'tensanpham' => $product['tensanpham'],
            'idsanpham' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $price * $quantity
        ];
    } else if ($data['type'] === 'cart' && !empty($data['items'])) {
        $items = explode(',', $data['items']);
        foreach ($items as $item) {
            list($productId, $quantity) = explode('_', $item);
            $product = $connect->getProductDetails((int)$productId);
            
            // Validate each cart item
            if (!$product) {
                throw new Exception("Product ID {$productId} not found");
            }
            if ($product['soluong'] < (int)$quantity) {
                throw new Exception("Insufficient stock for product: {$product['tensanpham']}");
            }

            $price = !empty($product['gia_giam']) ? 
                     ($product['giaban'] - $product['gia_giam']) : 
                     $product['giaban'];
            $products[] = [
                'tensanpham' => $product['tensanpham'],
                'idsanpham' => (int)$productId,
                'quantity' => (int)$quantity,
                'price' => $price,
                'subtotal' => $price * (int)$quantity
            ];
        }
    }

    if (empty($products)) {
        throw new Exception('No valid products found');
    }

    // Validate total amount
    $calculatedTotal = array_sum(array_column($products, 'subtotal')) + 30000; // Including shipping fee
    if (abs($calculatedTotal - $data['total_amount']) > 1) { // Allow 1đ difference for floating-point calculations
        throw new Exception('Total amount mismatch');
    }

    // Create the order
    $result = $connect->createOrder(
        $_SESSION['user_id'], 
        $orderData, 
        $products, 
        $data['payment_method']
    );

    if ($result['success']) {
        // Update product stock
        foreach ($products as $product) {
            $connect->updateProductStock($product['idsanpham'], $product['quantity']);
        }

        // Clear cart if order was from cart
        if ($data['type'] === 'cart') {
            try {
                $purchasedProductIds = array_map(function($item) {
                    return explode('_', $item)[0];
                }, explode(',', $data['items']));
                
                $connect->removePurchasedItems($_SESSION['user_id'], $purchasedProductIds);
            } catch (Exception $e) {
                error_log("Error removing purchased items: " . $e->getMessage());
            }
        }

        // Send order confirmation email
        try {
            $userInfo = $connect->getUserById($_SESSION['user_id']);
            require_once 'send_email.php';
            sendOrderConfirmation($orderData, $products, $userInfo['email']);
        } catch (Exception $e) {
            error_log("Error sending confirmation email: " . $e->getMessage());
        }
    }

    echo json_encode($result);

} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}