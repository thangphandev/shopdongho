<?php
header('Content-Type: application/json');

// Get POST data
$postData = json_decode(file_get_contents('php://input'), true);

// Call Flask API for report generation
$flaskApiUrl = 'http://127.0.0.1:5001/api/report';

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
    echo json_encode([
        'success' => false,
        'message' => 'Failed to generate report'
    ]);
} else {
    echo $response;
}