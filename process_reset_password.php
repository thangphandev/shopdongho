<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopdongho2";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu form đã được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $token = $_POST["token"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Kiểm tra mật khẩu và xác nhận mật khẩu có khớp nhau không
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Mật khẩu không khớp. Vui lòng thử lại.'); window.history.back();</script>";
        exit();
    }
    
    // Kiểm tra token có hợp lệ không
    $sql = "SELECT * FROM nguoidung WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
                // Token hợp lệ, cập nhật mật khẩu
                $user = $result->fetch_assoc();
        
                // Mã hóa mật khẩu mới bằng MD5 (không khuyến nghị cho bảo mật)
                $hashed_password = md5($new_password);
                
                // Cập nhật mật khẩu và xóa token
                $sql = "UPDATE nguoidung SET matkhau = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $hashed_password, $token);
        
        if ($stmt->execute()) {
            // Mật khẩu đã được cập nhật thành công
            echo "<script>alert('Mật khẩu đã được đặt lại thành công!'); window.location.href='login.php';</script>";
        } else {
            // Lỗi khi cập nhật mật khẩu
            echo "<script>alert('Có lỗi xảy ra khi cập nhật mật khẩu: " . $stmt->error . "'); window.history.back();</script>";
        }
    } else {
        // Token không hợp lệ
        echo "<script>alert('Liên kết đặt lại mật khẩu không hợp lệ!'); window.location.href='forgot_password.php';</script>";
    }
}

$conn->close();
?>