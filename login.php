<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/3_2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Watch</title>

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
    .password-toggle {
        position: absolute;
        right: 10px; /* Điều chỉnh sát mép phải */
        top: 50%;
        transform: translateY(-50%);
        color: #fff;
        cursor: pointer;
        z-index: 100;
    }
    
    /* Điều chỉnh padding cho input mật khẩu để tránh chữ bị che bởi biểu tượng */
    .password-field {
        padding-right: 40px !important; /* Điều chỉnh padding phù hợp */
    }
    .input-box {
        position: relative;
    }
    .nav-logo p{
        color: white;
        font-size: 25px;
        font-weight: 600;
    }
    .nav-menu ul{
        display: flex;
    }
    .nav-menu ul li{
        list-style-type: none;
    }
    .show-password {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 5px;
        margin-top: -15px;
        margin-bottom: 15px;
        color: #fff;
        font-size: 14px;
    }
    
    
    .show-password input[type="checkbox"] {
        cursor: pointer;
    }
    
    .show-password label {
        cursor: pointer;
    }
    .nav-menu ul li .link{
        text-decoration: none;
        font-weight: 500;
        color: #fff;
        padding-bottom: 15px;
        margin: 0 25px;
    }
    .link:hover, .active{
        border-bottom: 2px solid #fff;
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
    #registerBtn{
        margin-left: 15px;
    }
    .btn.white-btn{
        background: rgba(255, 255, 255, 0.7);
    }
    .btn.btn.white-btn:hover{
        background: rgba(255, 255, 255, 0.5);
    }
    .nav-menu-btn{
        display: none;
    }
    .form-box{
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 512px;
        height: 450px;
        overflow: hidden;
        z-index: 2;
    }
    .login-container{
        position: absolute;
        left: 4px;
        width: 500px;
        display: flex;
        flex-direction: column;
        transition: .5s ease-in-out;
    }
    .register-container{
        position: absolute;
        right: -520px;
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
    .two-forms{
        display: flex;
        gap: 10px;
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
    .two-col{
        display: flex;
        justify-content: space-between;
        color: #fff;
        font-size: small;
        margin-top: 10px;
    }
    .two-col .one{
        display: flex;
        gap: 5px;
    }

    .two label a{
        text-decoration: none;
        color: #fff;
    }
    .two label a:hover{
        text-decoration: underline;
    }
    @media only screen and (max-width: 786px){
        .nav-button{
            display: none;
        }
        .nav-menu.responsive{
            top: 100px;
        }
        .nav-menu{
            position: absolute;
            top: -800px;
            display: flex;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            width: 100%;
            height: 90vh;
            backdrop-filter: blur(20px);
            transition: .3s;
        }
        .nav-menu ul{
            flex-direction: column;
            text-align: center;
        }
        .nav-menu-btn{
            display: block;
        }
        .nav-menu-btn i{
            font-size: 25px;
            color: #fff;
            padding: 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            cursor: pointer;
            transition: .3s;
        }
        .nav-menu-btn i:hover{
            background: rgba(255, 255, 255, 0.15);
        }
    }
    @media only screen and (max-width: 540px) {
        .wrapper{
            min-height: 100vh;
        }
        .form-box{
            width: 100%;
            height: 500px;
        }
        .register-container, .login-container{
            width: 100%;
            padding: 0 20px;
        }
        .register-container .two-forms{
            flex-direction: column;
            gap: 0;
        }
    }
</style>
<body>
 <div class="wrapper">
    <nav class="nav">
        <div class="nav-logo">
            <img src="images/2222.png" alt="hinh anh" style="width: 100px; height: auto;">
        </div>       
        <div class="nav-button">
            <button class="btn white-btn" id="loginBtn" onclick="login()">Đăng Nhập</button>
            <button class="btn" id="registerBtn" onclick="register()">Đăng Ký</button>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="myMenuFunction()"></i>
        </div>
    </nav>

<!----------------------------- Form box ----------------------------------->    
    <div class="form-box">
        
        <!------------------- login form -------------------------->

        <div class="login-container" id="login">
            <div class="top">
                <span>Bạn chưa có tài khoản? <a href="#" onclick="register()">Đăng Ký</a></span>
                <header>Đăng Nhập</header>
            </div>
            <form action="process_login.php" method="POST">
                <div class="input-box">
                    <input type="email" name="email" class="input-field" placeholder="Email" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" class="input-field" placeholder="Mật Khẩu" required id="loginPassword" minlength="6" title="Mật khẩu phải có ít nhất 6 ký tự">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="show-password">
                    <input type="checkbox" id="showLoginPassword">
                    <label for="showLoginPassword">Hiển thị mật khẩu</label>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Đăng Nhập">
                </div>
                <div class="two-col">
                    <div class="one">
                        <input type="checkbox" id="login-check" name="remember">
                        <label for="login-check"> Nhớ Tài Khoản</label>
                    </div>
                    <div class="two">
                        <label><a href="forgot_password.php">Quên Mật Khẩu?</a></label>
                    </div>
                </div>
            </form>
        </div>

        <!------------------- registration form -------------------------->
        <div class="register-container" id="register">
    <div class="top">
        <span>Bạn chưa có tài khoản? <a href="#" onclick="login()">Đăng Nhập</a></span>
        <header>Đăng Ký</header>
    </div>
    <form action="proces_dangky.php" method="POST">
        <div class="input-box">
            <input type="text" name="firstname" class="input-field" placeholder="Tên đăng nhập" required>
            <i class="bx bx-user"></i>
        </div>
        <div class="input-box">
            <input type="email" name="email" class="input-field" placeholder="Email" required>
            <i class="bx bx-envelope"></i>
        </div>
        <div class="input-box">
            <input type="password" name="password" class="input-field" placeholder="Mật khẩu" required id="registerPassword" minlength="6" title="Mật khẩu phải có ít nhất 6 ký tự">
            <i class="bx bx-lock-alt"></i>
        </div>
        <div class="show-password">
            <input type="checkbox" id="showRegisterPassword">
            <label for="showRegisterPassword">Hiển thị mật khẩu</label>
        </div>
        <div class="input-box">
            <input type="submit" class="submit" value="Đăng ký">
        </div>
        <div class="two-col">
            <div class="one">
                <input type="checkbox" id="register-check" required>
                <label for="register-check"> Tôi đồng ý với các điều khoản</label>
            </div>
        </div>
    </form>
</div>
    </div>
</div>   


<script>
   
   function myMenuFunction() {
    var i = document.getElementById("navMenu");

    if(i.className === "nav-menu") {
        i.className += " responsive";
    } else {
        i.className = "nav-menu";
    }
   }
 
</script>

<script>

    var a = document.getElementById("loginBtn");
    var b = document.getElementById("registerBtn");
    var x = document.getElementById("login");
    var y = document.getElementById("register");

    function login() {
        x.style.left = "4px";
        y.style.right = "-520px";
        a.className += " white-btn";
        b.className = "btn";
        x.style.opacity = 1;
        y.style.opacity = 0;
    }

    function register() {
        x.style.left = "-510px";
        y.style.right = "5px";
        a.className = "btn";
        b.className += " white-btn";
        x.style.opacity = 0;
        y.style.opacity = 1;
    }

</script>
<script>
    // Xử lý hiển thị/ẩn mật khẩu
    document.getElementById('showLoginPassword').addEventListener('change', function() {
        const passwordInput = document.getElementById('loginPassword');
        
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
    
    document.getElementById('showRegisterPassword').addEventListener('change', function() {
        const passwordInput = document.getElementById('registerPassword');
        
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
    
    // Kiểm tra form đăng nhập
    document.querySelector('.login-container form').addEventListener('submit', function(e) {
        const password = document.getElementById('loginPassword').value;
        
        if (password.length < 6) {
            alert('Mật khẩu phải có ít nhất 6 ký tự');
            e.preventDefault();
        }
    });
    
    // Kiểm tra form đăng ký
    document.querySelector('.register-container form').addEventListener('submit', function(e) {
        const password = document.getElementById('registerPassword').value;
        
        if (password.length < 6) {
            alert('Mật khẩu phải có ít nhất 6 ký tự');
            e.preventDefault();
        }
    });
</script>
</body>
</html>