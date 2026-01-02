<?php

session_start();
require_once '../../config/db.php';

if (isset($_SESSION['store_id'])) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newBarcode = $_POST['new_barcode'];
        $oldBarcode = $_POST['original_barcode'];

        // Start transaction
        $conn->begin_transaction();
        try {
            // Update p_medicine
            $updateQuery = "UPDATE p_medicine SET `code` = '$newBarcode' WHERE code = '$oldBarcode'";

            if ($conn->query($updateQuery) !== TRUE) {
                error_log($conn->error);
                throw new Exception("Error updating p_medicine: " . $conn->error);
            }
            // update GRN items
            $updateGrn = "UPDATE grn_item SET grn_p_id = '$newBarcode' WHERE grn_p_id = '$oldBarcode'";
            if ($conn->query($updateGrn) !== TRUE) {
                error_log($conn->error);
                throw new Exception("Error updating grn_item: " . $conn->error);
            }
            // update Invoice items
            $updateInvoiceItem = "UPDATE invoiceitems SET barcode = '$newBarcode' WHERE barcode = '$oldBarcode'";
            if ($conn->query($updateInvoiceItem) !== TRUE) {
                error_log($conn->error);
                throw new Exception("Error updating invoice_item: " . $conn->error);
            }
            // update PO items
            $updatePO = "UPDATE poinvoiceitems SET item_code = '$newBarcode' WHERE item_code = '$oldBarcode'";
            if ($conn->query($updatePO) !== TRUE) {
                error_log($conn->error);
                throw new Exception("Error updating po_item: " . $conn->error);
            }

            // update Stock
            $updateStock = "UPDATE stock2 SET stock_item_code  = '$newBarcode' WHERE stock_item_code  = '$oldBarcode'";
            if ($conn->query($updateStock) !== TRUE) {
                error_log($conn->error);
                throw new Exception("Error updating Stock: " . $conn->error);
            }
            // Commit transaction if all queries succeeded
            $conn->commit();

            echo json_encode([
                "status"  => "success",
                "message" => "Barcode updated successfully"
            ]);
            exit;
        } catch (Exception $e) {
            // Rollback transaction if any query failed
            $conn->rollback();
            error_log($e->getMessage());
            echo json_encode([
                "status"  => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to login again.'
    ]);
}
