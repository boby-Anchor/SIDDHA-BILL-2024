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

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$brand_id   = intval($_POST['brand_id'] ?? 0);
$brand_name = trim($_POST['brand_name'] ?? '');
$details    = trim($_POST['details'] ?? '');

if ($brand_id <= 0 || $brand_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name and ID are required.'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM p_brand WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        throw new Exception("Brand not found.");
    }

    $existing = $result->fetch_assoc();
    $stmt->close();

    if ($existing['name'] === $brand_name && $existing['details'] === $details) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No changes detected.'
        ]);
        exit;
    }

    if ($existing['name'] !== $brand_name) {
        $stmt = $conn->prepare("SELECT id FROM p_brand WHERE name = ? AND id <> ?");
        $stmt->bind_param("si", $brand_name, $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            throw new Exception("A brand with this name already exists.");
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("UPDATE p_brand SET name = ?, details = ? WHERE id = ?");
    $stmt->bind_param("ssi", $brand_name, $details, $brand_id);
    if (!$stmt->execute()) {
        throw new Exception("Error updating brand: $stmt->error");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Brand updated successfully.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}