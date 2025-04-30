<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$connect = new Connect();
$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
                $data = [
                    'ten_loai_day' => trim($_POST['ten_loai_day']),
                    'mo_ta_loai_day' => trim($_POST['mo_ta_loai_day']),
                    'trangthai' => isset($_POST['trangthai']) ? 1 : 0
                ];
                
                if (empty($data['ten_loai_day'])) {
                    throw new Exception('Tên loại dây không được để trống');
                }
                
                if ($connect->addStrapType($data)) {
                    $_SESSION['success_message'] = 'Thêm loại dây thành công!';
                }
                break;

            case 'edit':
                $data = [
                    'id_loai_day' => (int)$_POST['id_loai_day'],
                    'ten_loai_day' => trim($_POST['ten_loai_day']),
                    'mo_ta_loai_day' => trim($_POST['mo_ta_loai_day']),
                    'trangthai' => isset($_POST['trangthai']) ? 1 : 0
                ];
                
                if (empty($data['ten_loai_day'])) {
                    throw new Exception('Tên loại dây không được để trống');
                }
                
                if ($connect->updateStrapType($data)) {
                    $_SESSION['success_message'] = 'Cập nhật loại dây thành công!';
                }
                break;

            case 'delete':
                $id = (int)$_POST['id_loai_day'];
                $result = $connect->deleteStrapType($id);
                
                if ($result === 'in_use') {
                    $_SESSION['error_message'] = 'Không thể xóa loại dây này vì đang được sử dụng bởi sản phẩm!';
                } else if ($result) {
                    $_SESSION['success_message'] = 'Xóa loại dây thành công!';
                } else {
                    throw new Exception('Không thể xóa loại dây');
                }
                break;
            }   
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}
header('Location: admin.php?page=loaiday');
    exit;