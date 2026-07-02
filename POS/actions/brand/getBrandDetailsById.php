<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to log in again.'
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id'] ?? 0);

    try {
        $stmt = $conn->prepare("SELECT * FROM p_brand WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Error fetching brand: " . $conn->error);
        }

        if ($result->num_rows === 0) {
            throw new Exception("Brand not found.");
        }

        $brand = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'data' => $brand
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
