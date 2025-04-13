<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $matkhau = $_POST['password'] ?? ''; // Changed to match database field
    
    $connect = new Connect();
    
    if ($connect->login($email, $matkhau)) {
        // Check if user is admin (role > 0)
        if (isset($_SESSION['role']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2)) {
            // Admin user - redirect to admin page
            $_SESSION['admin_id'] = $_SESSION['user_id'];
            $_SESSION['admin_email'] = $_SESSION['email'];
            $_SESSION['admin_name'] = $_SESSION['tendangnhap'];
            $_SESSION['admin_role'] = $_SESSION['role'];
            header('Location: admin/admin.php');
            exit();
        } else {
            // Regular user - redirect to homepage
            header('Location: index.php');
            exit();
        }
    } else {
        // Failed login
        header('Location: login.php?error=1&message=' . urlencode('Email hoặc mật khẩu không đúng'));
        exit();
    }
}