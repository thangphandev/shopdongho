<?php
require_once 'connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$connect = new Connect();
$count = $connect->laysoluongsanpham($_SESSION['user_id']);
echo json_encode(['count' => $count]);