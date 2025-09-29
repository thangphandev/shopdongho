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
                    'ten_chinh_sách' => $_POST['ten_chinh_sach'],
                    'noi_dung_chinh_sach' => $_POST['noi_dung_chinh_sach'],
                ];
                
                // Kiểm tra tên chính sách đã tồn tại chưa
                if ($connect->isWarrantyNameExists($data['ten_chinh_sách'])) {
                    $_SESSION['error_message'] = "Tên chính sách bảo hành đã tồn tại!";
                } else {
                    if ($connect->addWarranty($data)) {
                        $_SESSION['success_message'] = "Thêm chính sách bảo hành thành công!";
                    } else {
                        $_SESSION['error_message'] = "Thêm chính sách bảo hành thất bại!";
                    }
                }
                break;

            case 'edit':
                $data = [
                    'id_chinh_sach' => $_POST['id_chinh_sach'],
                    'ten_chinh_sách' => $_POST['ten_chinh_sach'],
                    'noi_dung_chinh_sach' => $_POST['noi_dung_chinh_sach'],
                ];
                
                // Kiểm tra tên chính sách đã tồn tại chưa (trừ chính nó)
                if ($connect->isWarrantyNameExistsExcept($data['ten_chinh_sách'], $data['id_chinh_sach'])) {
                    $_SESSION['error_message'] = "Tên chính sách bảo hành đã tồn tại!";
                } else {
                    if ($connect->updateWarranty($data)) {
                        $_SESSION['success_message'] = "Cập nhật chính sách bảo hành thành công!";
                    } else {
                        $_SESSION['error_message'] = "Cập nhật chính sách bảo hành thất bại!";
                    }
                }
                break;

            case 'delete':
                $id = $_POST['id_chinh_sach'];
                
                // Kiểm tra xem chính sách có đang được sử dụng không
                if ($connect->isWarrantyInUse($id)) {
                    $_SESSION['error_message'] = "Không thể xóa chính sách bảo hành này vì đang được sử dụng bởi sản phẩm!";
                } else {
                    if ($connect->deleteWarranty($id)) {
                        $_SESSION['success_message'] = "Xóa chính sách bảo hành thành công!";
                    } else {
                        $_SESSION['error_message'] = "Xóa chính sách bảo hành thất bại!";
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}   

header('Location: admin.php?page=baohanh');
exit;