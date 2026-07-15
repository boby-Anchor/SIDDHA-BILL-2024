<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

$user_id = null;
$shop_id = null;
$invoice_number = null;

try {

    // =========================
    // SESSION VALIDATION
    // =========================
    if (!isset($_SESSION['store_id'])) {
        echo json_encode([
            'status' => 'session_expired',
            'message' => 'Session expired. Please login again.'
        ]);
        exit;
    }

    $userData = $_SESSION['store_id'][0];

    $user_id = (int)$userData['id'];
    $shop_id = (int)$userData['shop_id'];

    // =========================
    // GENERATE INVOICE NUMBER
    // =========================
    $invoiceId_rs = $conn->query("
        SELECT AUTO_INCREMENT 
        FROM information_schema.tables 
        WHERE table_schema = '$db' 
        AND table_name = 'new_p_transfers'
    ");

    $invoiceId_row = $invoiceId_rs->fetch_assoc();
    $invoiceId = $invoiceId_row['AUTO_INCREMENT'];

    $invoice_number = "0" . $user_id . $shop_id . $invoiceId;

    // =========================
    // RECEIVE DATA
    // =========================
    $billData = json_decode($_POST['billData'], true);
    $poArray  = json_decode($_POST['products'], true);

    if (
        !is_array($billData) || empty($billData) ||
        !is_array($poArray) || empty($poArray)
    ) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid bill data or product list.'
        ]);
        exit;
    }

    // =========================
    // BILL DATA
    // =========================
    $bill = $billData[0];

    $po_shop_id           = (int)$bill['po_shop_id'];
    $sub_total            = (float)$bill['sub_total'];
    $discount_percentage  = (float)$bill['discount_percentage'];
    $net_total            = (float)$bill['net_total'];

    $currentDateTime = date("Y-m-d H:i:s");

    $productsAllTotal = 0;

    // =========================
    // START TRANSACTION
    // =========================
    $conn->begin_transaction();

    // =========================================================
    // PROCESS PRODUCTS
    // =========================================================
    foreach ($poArray as $product) {

        $code            = trim($product['code']);
        $ucv             = (float)$product['ucv'];
        $unit_price      = (float)$product['unit_price'];
        $item_price      = (float)$product['item_price'];
        $product_name    = trim($product['product_name']);
        $product_cost    = (float)$product['product_cost'];
        $product_qty     = (float)$product['product_qty'];
        $product_unit    = trim($product['product_unit']);
        $product_total   = (float)$product['product_total'];

        if (
            empty($code) ||
            empty($product_name) ||
            empty($product_unit) ||
            $product_qty <= 0
        ) {

            throw new Exception("Invalid product entry detected.");
        }

        // =====================================================
        // CALCULATE MINIMUM UNIT QTY
        // =====================================================
        $minimum_qty = 0;

        switch ($product_unit) {

            case 'kg':
            case 'l':
                $minimum_qty = $product_qty * $ucv * 1000;
                break;

            case 'g':
            case 'ml':
                $minimum_qty = $product_qty * $ucv;
                break;

            case 'm':
                $minimum_qty = $product_qty * $ucv * 100;
                break;

            case 'pieces':
            case 'pack / bottle':
                $minimum_qty = $product_qty;
                break;

            default:
                $minimum_qty = $product_qty;
                break;
        }

        // =====================================================
        // CHECK IF STOCK EXISTS IN PO SHOP
        // =====================================================
        $stock_query = "
            SELECT *
            FROM stock2
            WHERE stock_item_code = '$code'
            AND item_s_price = '$item_price'
            AND stock_shop_id = '$po_shop_id'
            LIMIT 1
        ";

        $stock_result = $conn->query($stock_query);

        // =====================================================
        // UPDATE EXISTING STOCK
        // =====================================================
        if ($stock_result && $stock_result->num_rows > 0) {

            $stock_data = $stock_result->fetch_assoc();

            $new_qty = $stock_data['stock_item_qty'] + $product_qty;
            $new_mu_qty = $stock_data['stock_mu_qty'] + $minimum_qty;

            $update_stock_query = "
                UPDATE stock2 SET
                    stock_item_qty = '$new_qty',
                    stock_mu_qty = '$new_mu_qty'
                WHERE stock_item_code = '$code'
                AND item_s_price = '$item_price'
                AND stock_shop_id = '$po_shop_id'
            ";

            if (!$conn->query($update_stock_query)) {
                throw new Exception($conn->error);
            }
        } else {

            // =================================================
            // GET SOURCE STOCK DATA
            // =================================================
            $po_stock_result = $conn->query("
                SELECT *
                FROM stock2
                WHERE stock_item_code = '$code'
                AND item_s_price = '$item_price'
                AND stock_shop_id = '$shop_id'
                LIMIT 1
            ");

            if (!$po_stock_result || $po_stock_result->num_rows == 0) {
                throw new Exception("Source stock not found for item: $product_name");
            }

            $po_stock_data = $po_stock_result->fetch_assoc();

            $cost_input     = $po_stock_data["stock_item_cost"];
            $cost_per_unit  = $po_stock_data["unit_cost"];
            $unit_s_price   = $po_stock_data["unit_s_price"];
            $item_discount  = $po_stock_data["added_discount"];

            // =================================================
            // INSERT NEW STOCK
            // =================================================
            $insert_stock_query = "
                INSERT INTO stock2 (
                    stock_item_code,
                    stock_item_name,
                    stock_item_qty,
                    stock_item_cost,
                    stock_mu_qty,
                    unit_cost,
                    unit_s_price,
                    added_discount,
                    item_s_price,
                    stock_shop_id
                ) VALUES (
                    '$code',
                    '$product_name',
                    '$product_qty',
                    '$cost_input',
                    '$minimum_qty',
                    '$cost_per_unit',
                    '$unit_s_price',
                    '$item_discount',
                    '$item_price',
                    '$po_shop_id'
                )
            ";

            if (!$conn->query($insert_stock_query)) {
                throw new Exception($conn->error);
            }
        }

        // =====================================================
        // INSERT INVOICE ITEMS
        // =====================================================
        $insert_invoice_item_query = "
            INSERT INTO new_p_items (
                invoiceNumber,
                item_code,
                invoiceItem,
                invoiceItem_qty,
                invoiceItem_unit,
                invoiceItem_price,
                invoiceItem_total
            ) VALUES (
                '$invoice_number',
                '$code',
                '$product_name',
                '$product_qty',
                '$product_unit',
                '$product_cost',
                '$product_total'
            )
        ";

        if (!$conn->query($insert_invoice_item_query)) {
            throw new Exception($conn->error);
        }

        // =====================================================
        // DEDUCT STOCK FROM MAIN SHOP
        // =====================================================

        if (
            $product_unit == 'kg' ||
            $product_unit == 'l'
        ) {

            if ($item_price == $product_cost) {

                $deduct_mu_qty = $product_qty * 1000 * $ucv;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = stock_item_qty - '$product_qty',
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND item_s_price = '$product_cost'
                ";
            } else {

                $deduct_mu_qty = $product_qty;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = ROUND((stock_mu_qty - '$deduct_mu_qty') / '$ucv', 2),
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND unit_s_price = '$product_cost'
                ";
            }
        } elseif (
            $product_unit == 'g' ||
            $product_unit == 'ml'
        ) {

            if ($item_price == $product_cost) {

                $deduct_mu_qty = $product_qty * $ucv;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = stock_item_qty - '$product_qty',
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND item_s_price = '$product_cost'
                ";
            } else {

                $deduct_mu_qty = $product_qty;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = ROUND((stock_mu_qty - '$deduct_mu_qty') / '$ucv', 2),
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND unit_s_price = '$product_cost'
                ";
            }
        } elseif ($product_unit == 'm') {

            if ($item_price == $product_cost) {

                $deduct_mu_qty = $product_qty * 100 * $ucv;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = stock_item_qty - '$product_qty',
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND item_s_price = '$product_cost'
                ";
            } else {

                $deduct_mu_qty = $product_qty;

                $deduct_query = "
                    UPDATE stock2 SET
                        stock_item_qty = ROUND((stock_mu_qty - '$deduct_mu_qty'), 2),
                        stock_mu_qty = stock_mu_qty - '$deduct_mu_qty'
                    WHERE stock_shop_id = '$shop_id'
                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                    AND unit_s_price = '$product_cost'
                ";
            }
        } elseif (
            $product_unit == 'pieces' ||
            $product_unit == 'pack / bottle'
        ) {

            $deduct_query = "
                UPDATE stock2 SET
                    stock_item_qty = stock_item_qty - '$product_qty',
                    stock_mu_qty = stock_mu_qty - '$product_qty'
                WHERE stock_shop_id = '$shop_id'
                AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')
            ";
        } else {

            $qty_rs = $conn->query("
                SELECT *
                FROM stock2
                WHERE (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                AND stock_shop_id = '$shop_id'
                AND (unit_s_price = '$product_cost' OR item_s_price = '$product_cost')
                LIMIT 1
            ");

            $qty_data = $qty_rs->fetch_assoc();

            $minimum_new_qty = (
                floatval($qty_data['stock_mu_qty']) /
                floatval($qty_data['stock_item_qty'])
            );

            $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;

            $new_stock_item_qty = $new_minimum_qty / $minimum_new_qty;

            $deduct_query = "
                UPDATE stock2 SET
                    stock_item_qty = '$new_stock_item_qty',
                    stock_mu_qty = stock_mu_qty - '$product_qty'
                WHERE stock_shop_id = '$shop_id'
                AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')
            ";
        }

        if (!$conn->query($deduct_query)) {
            throw new Exception($conn->error);
        }

        $productsAllTotal += $product_total;
    }

    // =========================================================
    // INSERT MAIN INVOICE
    // =========================================================
    $invoice_query = "
        INSERT INTO new_p_transfers (
            invoice_id,
            user_id,
            shop_id,
            po_shop_id,
            created,
            sub_total,
            discount_percentage,
            net_total
        ) VALUES (
            '$invoice_number',
            '$user_id',
            '$shop_id',
            '$po_shop_id',
            '$currentDateTime',
            '$sub_total',
            '$discount_percentage',
            '$net_total'
        )
    ";

    if (!$conn->query($invoice_query)) {
        throw new Exception($conn->error);
    }

    // =========================================================
    // COMMIT TRANSACTION
    // =========================================================
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock transfer successful. Reloading...',
        'invoice_number' => $invoice_number
    ]);
} catch (Throwable $th) {

    // =========================
    // ROLLBACK
    // =========================
    $conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $th->getMessage()
    ]);
}
