<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Sử dụng autoload từ Composer

function sendOrderConfirmation($orderData, $products, $userEmail) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phanminhthangcode@gmail.com';
        $mail->Password = 'jyga cpnz crxk oode';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('21004204@st.vlute.edu.vn', 'Watch Shop');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng - Watch Shop';

        // Build products table
        $productsHtml = '<table border="1" style="border-collapse: collapse; width: 100%;">
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>';
        
        foreach ($products as $product) {
            $productsHtml .= "<tr>
                <td>{$product['tensanpham']}</td>
                <td>{$product['quantity']}</td>
                <td>" . number_format($product['price'], 0, ',', '.') . "đ</td>
                <td>" . number_format($product['subtotal'], 0, ',', '.') . "đ</td>
            </tr>";
        }
        $productsHtml .= '</table>';

        // Email template
        $body = "
            <h2>Cảm ơn bạn đã đặt hàng!</h2>
            <p>Đơn hàng của bạn đã được xác nhận.</p>
            <h3>Chi tiết đơn hàng:</h3>
            <p>Người nhận: {$orderData['fullname']}</p>
            <p>Địa chỉ: {$orderData['full_address']}</p>
            <p>Số điện thoại: {$orderData['phone']}</p>
            <h3>Danh sách sản phẩm:</h3>
            {$productsHtml}
            <p>Phí vận chuyển: 30.000đ</p>
            <p>Tổng tiền: " . number_format($orderData['total_amount'], 0, ',', '.') . "đ</p>
            <p>Phương thức thanh toán: " . ($orderData['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : 'PayPal') . "</p>
            <p>Thời gian đặt hàng: {$orderData['order_date']}</p>
        ";

        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?>