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
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Cập nhật thành công' : 'Không thể cập nhật thông tin'
                ]);
            }
            break;

        case 'get':
            if (isset($_POST['id'])) {
                $staff = $connect->getStaffById($_POST['id']);
                echo json_encode([
                    'success' => true,
                    'data' => $staff
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