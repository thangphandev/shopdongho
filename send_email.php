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
        $mail->Username = 'WatchShopLuxury68@gmail.com';
        $mail->Password = 'yxex jtba vswj ltbe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('21004204@st.vlute.edu.vn', 'Watch Shop Luxury');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng - Watch Shop Luxury';

        // Build products table
        $productsHtml = '<table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
            <tr style="background-color: #f8f8f8;">
                <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Sản phẩm</th>
                <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Số lượng</th>
                <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Đơn giá</th>
                <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Thành tiền</th>
            </tr>';
        
        foreach ($products as $product) {
            $productsHtml .= "<tr>
                <td style='border: 1px solid #ddd; padding: 12px;'>{$product['tensanpham']}</td>
                <td style='border: 1px solid #ddd; padding: 12px; text-align: center;'>{$product['quantity']}</td>
                <td style='border: 1px solid #ddd; padding: 12px; text-align: right;'>" . number_format($product['price'], 0, ',', '.') . "đ</td>
                <td style='border: 1px solid #ddd; padding: 12px; text-align: right;'>" . number_format($product['subtotal'], 0, ',', '.') . "đ</td>
            </tr>";
        }
        $productsHtml .= '</table>';

        // Email template with improved styling
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h1 style='color: #333; margin-bottom: 5px;'>Watch Shop Luxury</h1>
                <p style='color: #777; font-size: 14px;'>Đồng hồ chính hãng cao cấp</p>
            </div>
            
            <div style='background-color: #f8f9fa; border-radius: 4px; padding: 15px; margin-bottom: 20px;'>
                <h2 style='color: #28a745; margin-top: 0;'>Cảm ơn bạn đã đặt hàng!</h2>
                <p style='color: #555; line-height: 1.5;'>Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
            </div>
            
            <div style='margin-bottom: 20px;'>
                <h3 style='color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px;'>Chi tiết đơn hàng</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Người nhận:</td>
                        <td style='padding: 8px 0; color: #333; font-weight: bold;'>{$orderData['fullname']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Địa chỉ:</td>
                        <td style='padding: 8px 0; color: #333;'>{$orderData['full_address']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Số điện thoại:</td>
                        <td style='padding: 8px 0; color: #333;'>{$orderData['phone']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Thời gian đặt hàng:</td>
                        <td style='padding: 8px 0; color: #333;'>{$orderData['order_date']}</td>
                    </tr>
                </table>
            </div>
            
            <div style='margin-bottom: 20px;'>
                <h3 style='color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px;'>Danh sách sản phẩm</h3>
                {$productsHtml}
            </div>
            
            <div style='background-color: #f8f9fa; border-radius: 4px; padding: 15px; margin-bottom: 20px;'>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Phí vận chuyển:</td>
                        <td style='padding: 8px 0; color: #333; text-align: right;'>" . number_format($orderData['ship'], 0, ',', '.') . "đ</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #555; font-weight: bold;'>Tổng tiền:</td>
                        <td style='padding: 8px 0; color: #e74c3c; font-size: 18px; font-weight: bold; text-align: right;'>" . number_format($orderData['total_amount'], 0, ',', '.') . "đ</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #555;'>Phương thức thanh toán:</td>
                        <td style='padding: 8px 0; color: #333; text-align: right;'>" . ($orderData['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : 'PayPal') . "</td>
                    </tr>
                </table>
            </div>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #777; font-size: 14px;'>
                <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline.</p>
                <p>© " . date('Y') . " Watch Shop Luxury. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
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