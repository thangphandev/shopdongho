<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tendangnhap = $_POST['firstname'] ?? ''; // Changed to match database field
    $email = $_POST['email'] ?? '';
    $matkhau = $_POST['password'] ?? ''; // Changed to match database field
    
    $connect = new Connect();
    $result = $connect->register($tendangnhap, $email, $matkhau);
    
    if ($result['success']) {
        header('Location: login.php?registered=1&message=' . urlencode('Đăng ký thành công'));
    } else {
        header('Location: login.php?error=register&message=' . urlencode($result['message']));
    }
    exit();
}