<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopdongho";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Sử dụng PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Tải autoload từ Composer
require 'vendor/autoload.php';

// Kiểm tra nếu form đã được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy email từ form
    $email = $_POST["email"];
    
    // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
    $sql = "SELECT * FROM nguoidung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email tồn tại, tạo mã token để đặt lại mật khẩu
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Lưu token vào cơ sở dữ liệu
        $sql = "UPDATE nguoidung SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        
        if ($stmt->execute()) {
            // Token đã được lưu, tạo liên kết đặt lại mật khẩu
            $reset_link = "http://localhost:8080/shopdongho/reset_password.php?token=" . $token;
             // Thêm dòng debug này
            // echo "Debug: Token = " . $token; exit;
            // Tạo đối tượng PHPMailer
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
                $mail->setFrom('WatchShopLuxury68@gmail.com', 'Watch Shop Luxury');
                $mail->addAddress($email);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Đặt lại mật khẩu - Watch Shop Luxury';
                
                // Email template
                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.6;
                            }
                            .container {
                                max-width: 600px;
                                margin: 0 auto;
                                padding: 20px;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                            }
                            .button {
                                display: inline-block;
                                padding: 10px 20px;
                                background-color: #4CAF50;
                                color: white;
                                text-decoration: none;
                                border-radius: 5px;
                            }
                            .link {
                                word-break: break-all;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Đặt lại mật khẩu</h2>
                            <p>Xin chào,</p>
                            <p>Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu của bạn:</p>
                            <p><a href='{$reset_link}' class='button'>Đặt lại mật khẩu</a></p>
                            <p>Liên kết này sẽ hết hạn sau 1 giờ.</p>
                            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                            <p>Trân trọng,<br>Watch Shop Luxury</p>
                        </div>
                    </body>
                    </html>
                ";
                
                $mail->send();
                
                // Email đã được gửi thành công
                header("Location: forgot_password.php?status=success");
                exit();
            } catch (Exception $e) {
                // Không thể gửi email, nhưng token đã được tạo
                // Hiển thị thông báo lỗi
                header("Location: forgot_password.php?status=error");
                exit();
            }
        } else {
            // Lỗi khi lưu token
            header("Location: forgot_password.php?status=error");
            exit();
        }
    } else {
        // Email không tồn tại
        header("Location: forgot_password.php?status=error");
        exit();
    }
}

$conn->close();
?>