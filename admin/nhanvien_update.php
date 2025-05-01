<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../connect.php';
$connect = new Connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            if (isset($_POST['tendangnhap']) && isset($_POST['email']) && isset($_POST['matkhau'])) {
                $result = $connect->addStaff($_POST['tendangnhap'], $_POST['email'], $_POST['matkhau']);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Thêm nhân viên thành công' : 'Không thể thêm nhân viên'
                ]);
            }
            break;

        case 'edit':
            if (isset($_POST['id']) && isset($_POST['tendangnhap']) && isset($_POST['email'])) {
                $result = $connect->updateStaff(
                    $_POST['id'],
                    $_POST['tendangnhap'],
                    $_POST['email'],
                    !empty($_POST['matkhau']) ? $_POST['matkhau'] : null
                );

                // Cập nhật quyền truy cập
                $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
                $permData = [
                    'sanpham' => isset($permissions['sanpham']) ? 1 : 0,
                    'danhmuc' => isset($permissions['danhmuc']) ? 1 : 0,
                    'loaimay' => isset($permissions['loaimay']) ? 1 : 0,
                    'loaiday' => isset($permissions['loaiday']) ? 1 : 0,
                    'nhacungcap' => isset($permissions['nhacungcap']) ? 1 : 0,
                    'donhang' => isset($permissions['donhang']) ? 1 : 0,
                    'khachhang' => isset($permissions['khachhang']) ? 1 : 0,
                    'nhanvien' => isset($permissions['nhanvien']) ? 1 : 0,
                    'danhgia' => isset($permissions['danhgia']) ? 1 : 0,
                    'tinnhan' => isset($permissions['tinnhan']) ? 1 : 0,
                    'baocao' => isset($permissions['baocao']) ? 1 : 0,
                    'khuyenmai' => isset($permissions['khuyenmai'])? 1 : 0
                ];
                $permResult = $connect->updatePermissions($_POST['id'], $permData);

                echo json_encode([
                    'success' => $result && $permResult,
                    'message' => ($result && $permResult) ? 'Cập nhật thành công' : 'Không thể cập nhật thông tin hoặc quyền'
                ]);
            }
            break;

        case 'get':
            if (isset($_POST['id'])) {
                $staff = $connect->getStaffById($_POST['id']);
                echo json_encode([
                    'success' => $staff !== false,
                    'data' => $staff
                ]);
            }
            break;

        case 'get_permissions':
            if (isset($_POST['id'])) {
                $permissions = $connect->getPermissions($_POST['id']);
                echo json_encode([
                    'success' => true,
                    'data' => $permissions
                ]);
            }
            break;

        case 'toggle_status':
            if (isset($_POST['id']) && isset($_POST['status'])) {
                $result = $connect->updateStaffStatus($_POST['id'], $_POST['status']);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Cập nhật trạng thái thành công' : 'Không thể cập nhật trạng thái'
                ]);
            }
            break;
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;