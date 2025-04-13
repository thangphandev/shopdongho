<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new Connect();
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $data = [
                'tendanhmuc' => $_POST['tendanhmuc'],
                'trangthai' => isset($_POST['trangthai']) ? 1 : 0
            ];
            
            if ($connect->addCategory($data)) {
                $_SESSION['success_message'] = "Thêm danh mục thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể thêm danh mục!";
            }
            break;

        case 'edit':
            $data = [
                'iddanhmuc' => $_POST['iddanhmuc'],
                'tendanhmuc' => $_POST['tendanhmuc'],
                'trangthai' => isset($_POST['trangthai']) ? 1 : 0
            ];
            
            if ($connect->updateCategory($data)) {
                $_SESSION['success_message'] = "Cập nhật danh mục thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể cập nhật danh mục!";
            }
            break;

        case 'delete':
            $id = $_POST['iddanhmuc'];
            if ($connect->deleteCategory($id)) {
                $_SESSION['success_message'] = "Xóa danh mục thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể xóa danh mục!";
            }
            break;
    }
}

header('Location: admin.php?page=danhmuc');
exit;