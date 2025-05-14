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
                    'tennhacungcap' => trim($_POST['tennhacungcap']),
                    'diachi' => trim($_POST['diachi']),
                    'sdt' => trim($_POST['sdt'])
                ];
                
                if ($connect->addSupplier($data)) {
                    $_SESSION['success_message'] = "Thêm nhà cung cấp thành công!";
                }
                break;

            case 'edit':
                $data = [
                    'idnhacungcap' => $_POST['idnhacungcap'],
                    'tennhacungcap' => trim($_POST['tennhacungcap']),
                    'diachi' => trim($_POST['diachi']),
                    'sdt' => trim($_POST['sdt'])
                ];
                
                if ($connect->updateSupplier($data)) {
                    $_SESSION['success_message'] = "Cập nhật nhà cung cấp thành công!";
                }
                break;

            case 'delete':
                $id = $_POST['idnhacungcap'];
                // Hàm deleteSupplier sẽ ném ngoại lệ nếu nhà cung cấp có sản phẩm
                if ($connect->deleteSupplier($id)) {
                    $_SESSION['success_message'] = "Xóa nhà cung cấp thành công!";
                }
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header('Location: admin.php?page=nhacungcap');
exit;