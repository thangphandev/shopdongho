<?php
require_once 'connect.php';
$connect = new Connect();
if ($connect->logout()) {
    // Successful login
    header('Location: login.php');
    exit();
    // } else {
    //     // Failed login
    //     header('Location: login.php?error=1&message=' . urlencode('Email hoặc mật khẩu không đúng'));
    //     exit();
    // }
}