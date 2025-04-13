<?php
session_start();
require_once '../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the image ID from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$imageId = isset($data['imageId']) ? intval($data['imageId']) : null;

if ($imageId) {
    $connect = new Connect();
    // Delete the image
    $success = $connect->deleteProductImage($imageId);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Image deleted successfully' : 'Failed to delete image'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
}