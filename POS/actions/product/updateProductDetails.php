<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'sessionExpired',
        'message' => 'Session expired! Wait to login again.'
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_brand = $_POST['product_brand'];
    $product_category = $_POST['category'];
    $product_sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
}

$product;

try {
    $stmt = $conn->prepare("SELECT * FROM p_medicine WHERE id = ?");
    if (!$stmt) {
        error_log($conn->error);
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        throw new Exception("Product not found");
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

try {
    if (
        $product['code'] === $barcode &&
        $product['name'] === $product_name &&
        $product['sku'] === $product_sku &&
        $product['brand'] == $product_brand &&
        $product['category'] == $product_category
    ) {
        echo json_encode([
            "status"  => "error",
            "message" => "No changes detected"
        ]);
        exit();
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}

// Start transaction
$conn->begin_transaction();

try {
    $oldBarcode = $product['code'];

    if ($product['sku'] != $product_sku) {
        $stmt = $conn->prepare("SELECT sku FROM p_medicine WHERE sku = ?");
        if (!$stmt) {
            error_log($conn->error);
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $product_sku);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            throw new Exception("SKU already exists!");
        }
        $stmt->close();

        $updateQuery = "UPDATE p_medicine SET `sku` = '$product_sku' WHERE code = '$oldBarcode'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating p_medicine: " . $conn->error);
        }
    }
    if ($product['brand'] != $product_brand) {
        $updateQuery = "UPDATE p_medicine SET `brand` = '$product_brand' WHERE code = '$oldBarcode'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating p_medicine: " . $conn->error);
        }
    }
    if ($product['category'] != $product_category) {
        $updateQuery = "UPDATE p_medicine SET `category` = '$product_category' WHERE code = '$oldBarcode'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating p_medicine: " . $conn->error);
        }
    }
    if ($product['name'] != $product_name) {
        // Update p_medicine
        $updateQuery = "UPDATE p_medicine SET `name` = '$product_name' WHERE code = '$oldBarcode'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating p_medicine: " . $conn->error);
        }
        // update Invoice items
        $updateInvoiceItem = "UPDATE invoiceitems SET invoiceItem = '$product_name' WHERE barcode = '$oldBarcode'";
        if ($conn->query($updateInvoiceItem) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating invoice_item: " . $conn->error);
        }
        // update PO items
        $updatePO = "UPDATE poinvoiceitems SET invoiceItem = '$product_name' WHERE item_code = '$oldBarcode'";
        if ($conn->query($updatePO) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating po_item: " . $conn->error);
        }
        // update Stock
        $updateStock = "UPDATE stock2 SET stock_item_name = '$product_name' WHERE stock_item_code  = '$oldBarcode'";
        if ($conn->query($updateStock) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Stock: " . $conn->error);
        }
    }

    if ($oldBarcode != $barcode) {

        $stmt = $conn->prepare("SELECT code FROM p_medicine WHERE code = ?");
        if (!$stmt) {
            error_log($conn->error);
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $barcode);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            throw new Exception("Barcode already exists!");
        }
        $stmt->close();

        // Update p_medicine
        $updateQuery = "UPDATE p_medicine SET `code` = '$barcode' WHERE code = '$oldBarcode'";
        if ($conn->query($updateQuery) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating p_medicine: " . $conn->error);
        }
        // update GRN items
        $updateGrn = "UPDATE grn_item SET grn_p_id = '$barcode' WHERE grn_p_id = '$oldBarcode'";
        if ($conn->query($updateGrn) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating grn_item: " . $conn->error);
        }
        // update Invoice items
        $updateInvoiceItem = "UPDATE invoiceitems SET barcode = '$barcode' WHERE barcode = '$oldBarcode'";
        if ($conn->query($updateInvoiceItem) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating invoice_item: " . $conn->error);
        }
        // update PO items
        $updatePO = "UPDATE poinvoiceitems SET item_code = '$barcode' WHERE item_code = '$oldBarcode'";
        if ($conn->query($updatePO) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating po_item: " . $conn->error);
        }
        // update Stock
        $updateStock = "UPDATE stock2 SET stock_item_code  = '$barcode' WHERE stock_item_code  = '$oldBarcode'";
        if ($conn->query($updateStock) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Stock: " . $conn->error);
        }
        // update Wastage
        $updateStock = "UPDATE wastage_batch_items SET barcode  = '$barcode' WHERE barcode  = '$oldBarcode'";
        if ($conn->query($updateStock) !== TRUE) {
            error_log($conn->error);
            throw new Exception("Error updating Stock: " . $conn->error);
        }
    }
    $conn->commit();

    echo json_encode([
        "status"  => "success",
        "message" => "Details updated successfully"
    ]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
