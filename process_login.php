<?php
require_once 'connect.php';
session_start(); // Make sure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $matkhau = $_POST['password'] ?? ''; // Changed to match database field
    
    $connect = new Connect();
    
    $loginResult = $connect->login($email, $matkhau);
    
    if ($loginResult['success']) {
        // Check if user is admin (role > 0)
        if (isset($_SESSION['role']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2)) {
            // Admin user - redirect to admin page
            $_SESSION['admin_id'] = $_SESSION['user_id'];
            $_SESSION['admin_email'] = $_SESSION['email'];
            $_SESSION['admin_name'] = $_SESSION['tendangnhap'];
            $_SESSION['admin_role'] = $_SESSION['role'];
            header('Location: admin/admin.php');
            exit();
        } else if (isset($_SESSION['role']) && $_SESSION['role'] == 0)  {
            // Regular user - redirect to homepage
            header('Location: index.php');
            exit();
        }
    } else {
        // Failed login - ensure proper redirection
        $_SESSION['login_error'] = $loginResult['message'];
        header('Location: login.php');
        exit();
    }
} else {
    // If someone tries to access this page directly without POST data
    header('Location: login.php');
    exit();
}
