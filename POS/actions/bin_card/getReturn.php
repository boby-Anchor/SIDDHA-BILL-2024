<?php
session_start();

if (!isset($_SESSION['store_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/db.php';

header('Content-Type: application/json');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $barcode  = trim($_POST['barcode'] ?? '');
    $startDate = trim($_POST['startDate'] ?? '');
    $endDate   = trim($_POST['endDate'] ?? '');

    if ($barcode === '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Barcode is required.'
        ]);
        exit();
    }

    $sql = "
        SELECT wastage_batches.id AS wastage_number,
            wastage_batches.created AS date,
            wastage_batch_items.qty,
            wastage_batch_items.item_price,
            shop.shopName,
            wastage_reasons.reason
        FROM `wastage_batch_items`
        INNER JOIN wastage_batches
            ON wastage_batch_items.wastage_batch_id = wastage_batches.id
        INNER JOIN shop
            ON wastage_batches.shop_id = shop.shopId
        INNER JOIN wastage_reasons
            ON wastage_batches.wastage_reason_id = wastage_reasons.id
        WHERE wastage_batch_items.barcode = ?
    ";

    $params = [$barcode];
    $types = "s";

    if ($startDate !== '' && $endDate !== '') {
        $sql .= " AND wastage_batches.created BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    } else {
        $sql .= " LIMIT 10";
    }
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data available.'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'details' => $result->fetch_all(MYSQLI_ASSOC)
    ]);

    $stmt->close();
} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
