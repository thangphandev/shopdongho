<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new Connect();
    $action = $_POST['action'];

    switch ($action) {
        case 'update_status':
            $data = [
                'iddonhang' => $_POST['iddonhang'],
                'trangthai' => $_POST['trangthai']
            ];
            
            if ($connect->updateOrderStatusAdmin($data)) {
                $_SESSION['success_message'] = "Cập nhật trạng thái đơn hàng thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể cập nhật trạng thái đơn hàng!";
            }
            break;
        case 'delete':
            $orderId = $_POST['iddonhang'];
            if ($connect->deleteOrder($orderId)) {
                $_SESSION['success_message'] = "Đã xóa đơn hàng thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể xóa đơn hàng. Vui lòng thử lại!";
            }
            break;
    }
}

header('Location: admin.php?page=donhang');
exit;