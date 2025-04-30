<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new Connect();
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'add':
                $data = [
                    'tendanhmuc' => $_POST['tendanhmuc'],
                    'trangthai' => isset($_POST['trangthai']) ? 1 : 0
                ];
                
                if ($connect->addCategory($data)) {
                    $_SESSION['success_message'] = "Thêm danh mục thành công!";
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
                }
                break;

            case 'delete':
                $id = $_POST['id_loai_may'];
                if ($connect->deleteWatchType($id)) {
                    $_SESSION['success_message'] = "Xóa loại máy thành công!";
                } else {
                    $_SESSION['error_message'] = "Không thể xóa loại máy!";
                }
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header('Location: admin.php?page=danhmuc');
exit;