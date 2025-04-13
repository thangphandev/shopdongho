<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new Connect();
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $data = [
                'tennhacungcap' => $_POST['tennhacungcap'],
                'diachi' => $_POST['diachi'],
                'sdt' => $_POST['sdt']
            ];
            
            if ($connect->addSupplier($data)) {
                $_SESSION['success_message'] = "Thêm nhà cung cấp thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể thêm nhà cung cấp!";
            }
            break;

        case 'edit':
            $data = [
                'idnhacungcap' => $_POST['idnhacungcap'],
                'tennhacungcap' => $_POST['tennhacungcap'],
                'diachi' => $_POST['diachi'],
                'sdt' => $_POST['sdt']
            ];
            
            if ($connect->updateSupplier($data)) {
                $_SESSION['success_message'] = "Cập nhật nhà cung cấp thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể cập nhật nhà cung cấp!";
            }
            break;

        case 'delete':
            $id = $_POST['idnhacungcap'];
            if ($connect->deleteSupplier($id)) {
                $_SESSION['success_message'] = "Xóa nhà cung cấp thành công!";
            } else {
                $_SESSION['error_message'] = "Không thể xóa nhà cung cấp!";
            }
            break;
    }
}

header('Location: admin.php?page=nhacungcap');
exit;