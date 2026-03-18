<?php
session_start();
require_once '../../config/db.php';
$user_id;
$user_name;
$shop_id;

if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];
    $user_id = $userData['id'];
    $user_name = $userData['name'];
    $shop_id = $userData['shop_id'];
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to login again.'
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    try {
        $result = $conn->query("SELECT * FROM p_medicine WHERE id = '$id'");

        if (!$result) {
            error_log($conn->error);
            throw new Exception("Error fetching product: " . $conn->error);
        }

        if ($result->num_rows === 0) {
            throw new Exception("Product not found");
        }

        $product = $result->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'data' => $product
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
