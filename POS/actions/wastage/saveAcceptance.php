<?php
session_start();
require_once "../../config/db.php";

$user_id;
$shop_id;
$batch_id;
$description;

try {
    if (isset($_SESSION['store_id'])) {
        $userData = $_SESSION['store_id'][0];
        $user_id = $userData['id'];
        $shop_id = $userData['shop_id'];
    } else {
        echo json_encode(array(
            'status' => 'session_expired',
            'message' => 'Session expired. Wait to login again.',
        ));
        exit();
    }
} catch (exception $exception) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Unknown error.',
    ));
    exit();
}

try {
    if (isset($_POST['batch_id'])) {
        $batch_id = $_POST['batch_id'];
        $description = $_POST['description'];
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data not received.',
        ]);
        exit();
    }
} catch (Throwable $th) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Data fetch error.',
    ));
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE wastage_batches 
    SET status = 1, 
        approved_by = ?, 
        approval_description = ? 
    WHERE id = ?");

    $stmt->bind_param("isi", $user_id, $description, $batch_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Wastage batch approved successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
} catch (Exception $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage(),
    ]);
    exit();
}
