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
    if ($_POST['action'] === 'toggle_status' && 
        isset($_POST['id']) && isset($_POST['status'])) {
        
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $success = $connect->updateCustomerStatus($id, $status);
        
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Cập nhật thành công' : 'Không thể cập nhật trạng thái'
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;