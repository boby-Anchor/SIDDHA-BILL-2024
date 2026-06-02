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
        SELECT poinvoices.invoice_id AS PO_Number,
            poinvoices.created AS date,
            poinvoiceitems.invoiceItem_qty AS qty,
            poinvoiceitems.invoiceItem_price AS price,
            shop.shopName AS PO_Shop
        FROM `poinvoiceitems`
        INNER JOIN poinvoices
            ON poinvoiceitems.invoiceNumber = poinvoices.invoice_id
        INNER JOIN shop
            ON poinvoices.po_shop_id = shop.shopId
        WHERE poinvoiceitems.item_code = ?
    ";

    $params = [$barcode];
    $types = "s";

    if ($startDate !== '' && $endDate !== '') {
        $sql .= " AND poinvoices.created BETWEEN ? AND ?";
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
