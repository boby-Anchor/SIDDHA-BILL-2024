<?php
session_start();
require_once '../../config/db.php';

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $shop_id = $userLoginData['shop_id'];
    $user_id = $userLoginData['id'];
} else {
    echo json_encode([
        'status' => 'session_expired',
        'message' => 'Session error. Wait to login again.'
    ]);
    exit();
}

$allowedTables = ['p_supplier'];

$id = $_POST['id'];
$table = $_POST['table'];
$status = $_POST['status'];

if (empty($id) || empty($table) || $status === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing data"
    ]);
    exit();
}

if (!in_array($table, $allowedTables)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized..!'
    ]);
    exit();
}

$stmt = $conn->prepare("UPDATE `$table` SET `status` = ?, `created_by` = ? WHERE `id` = ?");
$stmt->bind_param("iii", $status, $user_id, $id);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data updated successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data update failed.'
    ]);
}
