<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new Connect();
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $data = [
                'ten_loai_may' => $_POST['ten_loai_may'],
                'mo_ta_loai_may' => $_POST['mo_ta_loai_may'],
                'trangthai' => isset($_POST['trangthai']) && $_POST['trangthai'] === 'on' ? 1 : 0, // Fixed checkbox handling
            ];
            
            if ($connect->addWatchType($data)) {
                $_SESSION['success_message'] = "Thêm loại máy thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể thêm loại máy!";
            }
            break;

        case 'edit':
            $data = [
                'id_loai_may' => $_POST['id_loai_may'],
                'ten_loai_may' => $_POST['ten_loai_may'],
                'mo_ta_loai_may' => $_POST['mo_ta_loai_may'],
                'trangthai' => isset($_POST['trangthai']) && $_POST['trangthai'] === 'on' ? 1 : 0,
            ];
            
            if ($connect->updateWatchType($data)) {
                $_SESSION['success_message'] = "Cập nhật loại máy thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể cập nhật loại máy!";
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
}

header('Location: admin.php?page=loaimay');
exit;