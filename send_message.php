<?php
require_once 'connect.php';
session_start();

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if message is provided
if (!isset($_POST['message']) || empty($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'No message provided']);
    exit();
}

$userId = $_SESSION['user_id'];
$message = trim($_POST['message']);
$currentTime = date('Y-m-d H:i:s');

// Connect to database
$connect = new Connect();

// Save user message (role 0 = user)
$userMessageSaved = $connect->saveChat($userId, $message, 0, $currentTime);

if (!$userMessageSaved) {
    echo json_encode(['success' => false, 'message' => 'Failed to save user message']);
    exit();
}

// Call Flask API for bot response
$flaskApiUrl = 'http://localhost:5001/api/chat';
$postData = [
    'message' => $message
];

// Initialize cURL session
$ch = curl_init($flaskApiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 seconds timeout

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Close cURL session
curl_close($ch);

// Check for errors
if ($error || $httpCode != 200) {
    // If API call fails, provide a default response
    $botResponse = "Xin lỗi, tôi không thể xử lý yêu cầu của bạn lúc này. Vui lòng thử lại sau.";
} else {
    // Decode API response
    $responseData = json_decode($response, true);
    $botResponse = $responseData['response'] ?? "Xin lỗi, tôi không hiểu yêu cầu của bạn.";
}

// Save bot response (role 1 = bot)
$botMessageSaved = $connect->saveChat($userId, $botResponse, 1, date('Y-m-d H:i:s'));

// Return response to client
echo json_encode([
    'success' => true,
    'botResponse' => $botResponse
]);