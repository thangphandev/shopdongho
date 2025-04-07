<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $matkhau = $_POST['password'] ?? ''; // Changed to match database field
    
    $connect = new Connect();
    
    if ($connect->login($email, $matkhau)) {
        // Successful login
        header('Location: index.php');
        exit();
    } else {
        // Failed login
        header('Location: login.php?error=1&message=' . urlencode('Email hoặc mật khẩu không đúng'));
        exit();
    }
}