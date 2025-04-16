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

// Lấy email từ request
$email = $_POST['email'] ?? '';
$response = ['exists' => false];

if (!empty($email)) {
    // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
    $sql = "SELECT * FROM nguoidung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email tồn tại
        $response['exists'] = true;
    }
}

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>