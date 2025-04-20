<?php
// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php_errors.log'); // Thay bằng đường dẫn thực tế

header('Content-Type: application/json');
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Validate input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Debug: Log received data
    error_log('Received order data: ' . print_r($data, true));

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

    // Format order data
    $orderData = [
        'fullname' => strip_tags(trim($data['fullname'])),
        'phone' => trim($data['phone']),
        'full_address' => strip_tags(trim($data['address'])),
        'total_amount' => floatval($data['total_amount']),
        'type' => $data['type'],
        'payment_method' => $data['payment_method'],
        'order_date' => date('Y-m-d H:i:s'),
        'status' => $data['payment_method'] === 'paypal' ? 'Chuẩn bị đơn' : 'Chờ xác nhận',
        'user_id' => $_SESSION['user_id'],
        'payment_details' => $data['payment_details'] ?? null
    ];

    // Process products
    $products = [];
    if ($data['type'] === 'direct') {
        $productInfo = isset($data['items']) ? explode('_', $data['items']) : [];
        if (count($productInfo) !== 2) {
            throw new Exception('Invalid product info');
        }

        $productId = (int)$productInfo[0];
        $quantity = (int)$productInfo[1];

        $product = $connect->getProductDetails($productId);
        if (!$product || $product['soluong'] < $quantity) {
            throw new Exception(!$product ? 'Product not found' : 'Insufficient stock');
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
    } elseif ($data['type'] === 'cart' && !empty($data['items'])) {
        foreach (explode(',', $data['items']) as $item) {
            list($productId, $quantity) = explode('_', $item);
            $product = $connect->getProductDetails((int)$productId);

            if (!$product || $product['soluong'] < (int)$quantity) {
                throw new Exception(
                    !$product ?
                        "Product ID {$productId} not found" :
                        "Insufficient stock for product: {$product['tensanpham']}"
                );
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

    // Validate total amount (including shipping fee)
    $calculatedTotal = array_sum(array_column($products, 'subtotal')) + 30000;
    if (abs($calculatedTotal - $data['total_amount']) > 1) {
        throw new Exception('Total amount mismatch');
    }

    // Create order and process
    $result = $connect->createOrder($_SESSION['user_id'], $orderData, $products, $data['payment_method']);

    if ($result['success']) {
        // Update product stock
        foreach ($products as $product) {
            $connect->updateProductStock($product['idsanpham'], $product['quantity']);
        }

        // Clear cart items if order was from cart
        if ($data['type'] === 'cart') {
            $purchasedProductIds = array_map(function ($item) {
                return explode('_', $item)[0];
            }, explode(',', $data['items']));

            $connect->removePurchasedItems($_SESSION['user_id'], $purchasedProductIds);
        }

        // Send confirmation email
        try {
            $userInfo = $connect->getUserById($_SESSION['user_id']);
            require_once 'send_email.php';

            // $emailData = [
            //     'orderData' => $orderData,
            //     'products' => $products,
            //     'userEmail' => $userInfo['email']
            // ];

            sendOrderConfirmation($orderData, $products, $userInfo['email']);
        } catch (Exception $e) {
            error_log("Error sending confirmation email: " . $e->getMessage());
        }
    }

    http_response_code(200);
    echo json_encode($result);
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Throwable $t) {
    error_log("Unexpected error in tao_don_hang.php: " . $t->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error'
    ]);
}
