<?php
include('../../config/db.php');
session_start();

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $user_id = $userLoginData['id'] ?? null;
    $shop_id = $userLoginData['shop_id'] ?? null;

    if ($user_id != 38) {
        echo json_encode([
            'status' => 'sessionExpired',
            'message' => 'Unauthorized login. Wait to Login again.',
        ]);
        exit;
    }
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.',
    ]);
    exit;
}

$batchNumber = trim($_POST['batch_number']);
$sourceItems = json_decode($_POST['source_items'], true);
$refillItems = json_decode($_POST['refill_items'], true);

if ($batchNumber === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Batch number required'
    ]);
    exit;
}

if (!is_array($sourceItems) && !is_array($refillItems)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No items received'
    ]);
    exit;
}

try {
    $conn->begin_transaction();

    // Insert batch
    $stmtBatch = $conn->prepare(
        "INSERT INTO refill_batch (batch_number) VALUES (?)"
    );
    $stmtBatch->bind_param("s", $batchNumber);
    $stmtBatch->execute();
    $batchId = $stmtBatch->insert_id;
    $stmtBatch->close();

    // Prepare item insert ONCE
    $stmtItems = $conn->prepare("
        INSERT INTO refill_batch_item
        (refill_batch_id, batch_number, barcode, qty, is_source, created)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    // Variables MUST exist before bind_param
    $refill_batch_id = $batchId;
    $batch_no = $batchNumber;
    $barcode = '';
    $qty = 0;
    $is_source = 0;

    // Bind ONCE
    $stmtItems->bind_param(
        "issii",
        $refill_batch_id,
        $batch_no,
        $barcode,
        $qty,
        $is_source
    );

    // Source items
    if (!empty($sourceItems)) {
        $is_source = 1;

        foreach ($sourceItems as $item) {
            $barcode = $item['barcode'];
            $qty = $item['qty'];
            $stmtItems->execute();
        }
    }

    // Refill items
    if (!empty($refillItems)) {
        $is_source = 0;

        foreach ($refillItems as $item) {
            $barcode = $item['barcode'];
            $qty = $item['qty'];
            $stmtItems->execute();
        }
    }

    $stmtItems->close();
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock conversion successful.'
    ]);
    exit;
} catch (Exception $e) {
    $conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
