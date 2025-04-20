<?php
// Kiểm tra xem token có được cung cấp không
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];

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

// Kiểm tra token có hợp lệ không
$sql = "SELECT * FROM nguoidung WHERE reset_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Token không tồn tại
    echo "<script>alert('Token không tồn tại trong cơ sở dữ liệu!'); window.location.href='forgot_password.php';</script>";
    exit();
} else {
    // Kiểm tra thời hạn token
    $user = $result->fetch_assoc();
    if (isset($user['reset_token_expiry']) && $user['reset_token_expiry'] < date('Y-m-d H:i:s')) {
        echo "<script>alert('Token đã hết hạn!'); window.location.href='forgot_password.php';</script>";
        exit();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/3_2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Đặt Lại Mật Khẩu</title>
</head>
<style>
    /* POPPINS FONT */
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

*{  
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
body{
    background: url("images/anh-nen-pp.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    overflow: hidden;
}
.wrapper{
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 110vh;
    background: rgba(39, 39, 39, 0.4);
}
.nav{
    position: fixed;
    top: 0;
    display: flex;
    justify-content: space-around;
    width: 100%;
    height: 100px;
    line-height: 100px;
    background: linear-gradient(rgba(39,39,39, 0.6), transparent);
    z-index: 100;
}
.nav-logo p{
    color: white;
    font-size: 25px;
    font-weight: 600;
}
.nav-button .btn{
    width: 130px;
    height: 40px;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.4);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: .3s ease;
}
.btn:hover{
    background: rgba(255, 255, 255, 0.3);
}
#loginBtn{
    margin-left: 15px;
}
.btn.white-btn{
    background: rgba(255, 255, 255, 0.7);
}
.btn.btn.white-btn:hover{
    background: rgba(255, 255, 255, 0.5);
}
.form-box{
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 512px;
    height: 420px;
    overflow: hidden;
    z-index: 2;
}
.reset-container{
    position: absolute;
    width: 500px;
    display: flex;
    flex-direction: column;
    transition: .5s ease-in-out;
}
.top span{
    color: #fff;
    font-size: small;
    padding: 10px 0;
    display: flex;
    justify-content: center;
}
.top span a{
    font-weight: 500;
    color: #fff;
    margin-left: 5px;
}
header{
    color: #fff;
    font-size: 30px;
    text-align: center;
    padding: 10px 0 30px 0;
}
.input-field{
    font-size: 15px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    height: 50px;
    width: 100%;
    padding: 0 10px 0 45px;
    border: none;
    border-radius: 30px;
    outline: none;
    transition: .2s ease;
}
.input-field:hover, .input-field:focus{
    background: rgba(255, 255, 255, 0.25);
}
::-webkit-input-placeholder{
    color: #fff;
}
.input-box i{
    position: relative;
    top: -35px;
    left: 17px;
    color: #fff;
}
.submit{
    font-size: 15px;
    font-weight: 500;
    color: black;
    height: 45px;
    width: 100%;
    border: none;
    border-radius: 30px;
    outline: none;
    background: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: .3s ease-in-out;
}
.submit:hover{
    background: rgba(255, 255, 255, 0.5);
    box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
}
.message-box {
    margin-top: 20px;
    padding: 10px;
    border-radius: 5px;
    color: #fff;
    text-align: center;
    font-size: 14px;
}
</style>
<body>
 <div class="wrapper">
    <nav class="nav">
        <div class="nav-logo">
            <img src="images/2222.png" alt="hinh anh" style="width: 100px; height: auto;">
        </div>       
        <div class="nav-button">
            <button class="btn" id="loginBtn" onclick="window.location.href='login.php'">Đăng Nhập</button>
        </div>
    </nav>

<!----------------------------- Form box ----------------------------------->    
    <div class="form-box">
        
        <!------------------- reset password form -------------------------->

        <div class="reset-container" id="reset">
            <div class="top">
                <header>Đặt Lại Mật Khẩu</header>
            </div>
            <form action="process_reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="input-box">
                    <input type="password" name="new_password" class="input-field" placeholder="Mật khẩu mới" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" class="input-field" placeholder="Xác nhận mật khẩu mới" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Đặt Lại Mật Khẩu">
                </div>
                <div class="message-box">
                    <p>Vui lòng nhập mật khẩu mới của bạn.</p>
                </div>
            </form>
        </div>
    </div>
</div>   
</body>
</html>