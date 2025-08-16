<?php
session_start();
include('config/db.php');

// Values
$user_id = 0;
$shop_id = 0;
$invoiceNumber = "000";
$currentDateTime = date("Y-m-d H:i:s");

// Write invoices to txt file
try {
    function printInvoiceData($invDataLog)
    {
        $error_log_path = "error_log_invData.txt";
        $log_message_str = json_encode($invDataLog, JSON_PRETTY_PRINT) . PHP_EOL;
        file_put_contents($error_log_path, $log_message_str, FILE_APPEND);
    }
} catch (Exception $exception) {
    error_log("Invoice data log error." . $exception->getMessage());
}

// Session check of login and values assign
if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];
    $user_id = $userData['id'];
    $shop_id = $userData['shop_id'];

    if ($user_id == 0 or $shop_id == 0) {
        echo json_encode(array(
            'status' => 'sessionDataError',
            'message' => 'Session error. Wait to login again. shop: ' . $shop_id . ' user: ' . $user_id,
        ));
        exit();
    }
} else {
    echo json_encode(array(
        'status' => 'sessionExpired',
        'message' => 'Session Expired! Wait to login again.',
    ));
    exit();
}

// Get the invoice number
if ($invoiceId_rs = $conn->query("SELECT `AUTO_INCREMENT` FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'invoices'")) {
    if ($invoiceId_rs->num_rows > 0) {
        $invoiceId_row = $invoiceId_rs->fetch_assoc();
        $invoiceId = $invoiceId_row['AUTO_INCREMENT'];
        $invoiceNumber .= $user_id . $shop_id . $invoiceId;
    } else {
        error_log("No AUTO_INCREMENT found for invoices table.");
        echo json_encode(array(
            'status' => 'invoiceNumberError',
            'message' => 'Invoice number Creation Error! Try again.',
        ));
        exit();
    }
} else {
    error_log("Query error: " . $conn->error);
    echo json_encode(array(
        'status' => 'invoiceNumberError',
        'message' => 'Invoice number Query Error!.',
    ));
    exit();
}

// Get the data from post request
$itemData = isset($_POST['itemData']) ? json_decode($_POST['itemData'], true) : [];
$dMData = isset($_POST['dMData']) ? json_decode($_POST['dMData'], true) : [];
$billData = isset($_POST['billData']) ? json_decode($_POST['billData'], true) : [];


if (empty($billData) || (empty($itemData) && empty($dMData))) {
    error_log("Bill data error");
    echo json_encode(array(
        'status' => 'billDataError',
        'message' => 'Bill Data Error! Try again.',
    ));
    exit();
}

$invDataLog = [
    'invoiceNumber' => $invoiceNumber,
    'timestamp' => date('Y-m-d H:i:s'),
    'billData' => is_array($billData) ? $billData : "(No Data Received: $billData)",
    'dMData' => is_array($dMData) ? $dMData : "(No Data Received: $dMData)",
    'itemData' => is_array($itemData) ? $itemData : "(No Data Received: $itemData)",
];
printInvoiceData($invDataLog);

// Check invoice number duplication query
$cm = $conn->query("SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'");

$patientName;
$contactNo;
$doctorName;
$regNo;

$balance;
$subTotal;
$discountPercentage;
$deliveryCharges;
$valueAddedServices;
$cashAmount;
$cardAmount;
$paymentMethodSelector;
$selectBillType;

//  check if invoice is already inserted
if ($cm->num_rows == 0 && !empty($billData)) {

    // Assign bill data
    if (is_array($billData) && !empty($billData)) {
        foreach ($billData as $product) {
            try {
                $patientName = $product['patientName'];
                $contactNo = $product['contactNo'];
                $doctorName = $product['doctorName'];
                $regNo = $product['regNo'];

                $balance = $product['balance'];
                $subTotal = $product['subTotal'];
                $discountPercentage = $product['discountPercentage'];
                $deliveryCharges = $product['deliveryCharges'];
                $valueAddedServices = $product['valueAddedServices'];
                $cashAmount = $product['cashAmount'];
                $cardAmount = $product['cardAmount'];
                $paymentMethodSelector = $product['paymentMethodSelector'];
                $selectBillType = $product['selectBillType'];
            } catch (Exception $exception) {
                error_log("Bill data error." . $exception->getMessage());
            }
        }  // close for-each $billData
    } else {
        error_log("Empty bill Data." . $exception->getMessage());
    }  // billData[] end

    if (is_array($dMData) && !empty($dMData)) {
        foreach ($dMData as $product) {
            try {
                $product_name = $product['product_name'];
                $item_cost = $product['item_cost'];
                $item_price = $product['item_price'];

                $conn->query("INSERT INTO dm_items (invoice_id, invoiceDate, dmName, itemPrice, totalPrice)
                VALUES ('$invoiceNumber', '$currentDateTime', '$product_name', '$item_cost', '$item_price')");
            } catch (Exception $exception) {
                error_log("DM Data error." . $exception->getMessage());
            }
        }  // close for-each $dMData
    } // dMData[] end

    if (is_array($itemData) && !empty($itemData)) {
        foreach ($itemData as $product) {
            try {
                $isPaththu = $product['isPaththu'];
                $code = $product['code'];
                $ucv = $product['ucv'];
                $unit_price = $product['unit_price'];
                $item_price = $product['item_price'];
                $product_name = $product['product_name'];
                $product_cost = $product['product_cost'];
                $product_qty = $product['product_qty'];
                $product_unit = $product['product_unit'];
                $productTotal = $product['productTotal'];

                if (
                    !empty($product_unit) && !empty($ucv) && !empty($item_price) && !empty($product_name) &&
                    is_numeric($product_cost) && is_numeric($product_qty) && is_numeric($productTotal) && !empty($invoiceNumber)
                ) {

                    if (
                        !($conn->query("INSERT INTO invoiceitems (invoiceNumber, invoiceDate, barcode, invoiceItem, invoiceItem_qty, invoiceItem_unit, invoiceItem_price, invoiceItem_total, isPaththu)
                    VALUES ('$invoiceNumber', '$currentDateTime', '$code', '$product_name', '$product_qty', '$product_unit', '$product_cost', '$productTotal', '$isPaththu')"))
                    ) {
                        error_log("invoice items failed" . $conn->error);
                    }

                    if ($product_unit == 'kg' || $product_unit == 'l') {

                        if ($item_price == $product_cost) {
                            try {
                                $total_qty = $product_qty * 1000;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                                    stock_item_qty = (stock_item_qty - $product_qty),
                                    stock_mu_qty = (stock_mu_qty - $total_qty)
                                    WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code'
                                    OR stock_minimum_unit_barcode = '$code')
                                    AND (item_s_price = '$product_cost'
                                    OR unit_s_price = '$product_cost')");
                            } catch (Exception $exception) {
                                error_log("Item error kg/l.\n" . $exception->getMessage());
                            }
                        } else {
                            try {
                                $conn->query("UPDATE stock2 SET
                                stock_item_qty = (stock_item_qty - ROUND($product_qty / ($ucv * 1000), 3)),
                                stock_mu_qty = (stock_mu_qty - $product_qty)
                                WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                                OR stock_minimum_unit_barcode = '$code')
                                AND unit_s_price = '$product_cost'");
                            } catch (Exception $exception) {
                                error_log("Mu error kg/l\n" . $exception->getMessage());
                            }
                        }
                    } else if ($product_unit == 'g' || $product_unit == 'ml') {

                        if ($item_price == $product_cost) {
                            try {
                                $total_qty = $product_qty * $ucv;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                                    stock_item_qty = (stock_item_qty - $sell_p_qty),
                                    stock_mu_qty = (stock_mu_qty - $total_qty)
                                    WHERE stock_shop_id = '$shop_id'
                                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                            } catch (Exception $exception) {
                                error_log("Item error g/ml\n" . $exception->getMessage());
                            }
                        } else {
                            try {
                                $total_qty = $product_qty * 1000;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                                    stock_item_qty = (stock_item_qty - ROUND($product_qty / $ucv, 3)),
                                    stock_mu_qty = (stock_mu_qty - $product_qty)
                                    WHERE stock_shop_id = '$shop_id' 
                                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND unit_s_price = '$product_cost'");
                            } catch (Exception $exception) {
                                error_log("Mu error g/ml\n" . $exception->getMessage());
                            }
                        }
                    } else if ($product_unit == 'm') {

                        if ($item_price == $product_cost) {
                            try {
                                $product_minimum_qty = $product_qty * 100;

                                $conn->query("UPDATE stock2 SET
                                    stock_item_qty = (stock_item_qty -  $product_qty),
                                    stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                                    WHERE stock_shop_id = '$shop_id'
                                    AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                                    AND item_s_price = '$product_cost'");
                            } catch (Exception $exception) {
                                error_log("Item error m\n" . $exception->getMessage());
                            }
                        } else {
                            try {
                                $product_minimum_qty = $product_qty;
                                $conn->query("UPDATE stock2 SET
                            stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / 100, 2), 
                            stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                            WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                            AND unit_s_price = '$product_cost'");
                            } catch (Exception $exception) {
                                error_log("Mu error m\n" . $exception->getMessage());
                            }
                        }
                    } else if ($product_unit == 'pack / bottle' || $product_unit == 'pieces') {
                        try {
                            $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $product_qty)
                                WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost'");
                        } catch (Exception $exception) {
                            error_log("Item error\n" . $exception->getMessage());
                        }
                    }
                    // else {
                    //     $qty_rs = $conn->query("SELECT * FROM stock2 WHERE (stock_item_code = '$code' 
                    // OR stock_minimum_unit_barcode = '$code')
                    // AND stock_shop_id = '$shop_id' AND (unit_s_price = '$product_cost' 
                    // OR item_s_price = '$product_cost')");

                    //     $qty_data = $qty_rs->fetch_assoc();

                    //     $minimum_new_qty = (floatval($qty_data['stock_mu_qty']) / $qty_data['stock_item_qty']);
                    //     echo $minimum_new_qty;

                    //     $new_minimum_qty = $qty_data['stock_mu_qty'] - $product_qty;
                    //     echo $new_minimum_qty;

                    //     $new_stock_item_qty = ROUND($new_minimum_qty / $minimum_new_qty, 2);
                    //     echo $new_stock_item_qty;

                    //     $conn->query("UPDATE stock2 SET stock_item_qty = $new_stock_item_qty, 
                    // stock_mu_qty = (stock_mu_qty - $product_qty)
                    // WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                    // OR stock_minimum_unit_barcode = '$code')
                    // AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                    // }
                } else {
                    $error_message = "Invalid product data for invoice inside Invoice Items loop {$invoiceNumber}: ";
                    $error_message .= "product_unit=" . var_export($product_unit, true) . ", ";
                    $error_message .= "ucv=" . var_export($ucv, true) . ", ";
                    $error_message .= "item_price=" . var_export($item_price, true) . ", ";
                    $error_message .= "product_name=" . var_export($product_name, true) . ", ";
                    $error_message .= "product_cost=" . var_export($product_cost, true) . ", ";
                    $error_message .= "product_qty=" . var_export($product_qty, true) . ", ";
                    $error_message .= "productTotal=" . var_export($productTotal, true);

                    error_log($error_message);
                    printInvoiceData("Error: " . $error_message);
                }
            } catch (Exception $exception) {
                error_log("Invoice items loop error" . $exception->getMessage());
            }
        }  // close for-each $itemData
    } // itemData[] end

    if (
        $conn->query(
            "INSERT INTO invoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
                VALUES ('$invoiceNumber', '$user_id', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentMethodSelector', '$subTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount', '$cardAmount', '$balance')"
        )
    ) {
        echo json_encode(array(
            'status' => 'success',
            'invoiceNumber' => $invoiceNumber,
            'message' => 'Invoice Saved Successfully!',
        ));
    } else {
        error_log("Query error: " . $conn->error);
        echo json_encode(array(
            'status' => 'invoiceError',
            'message' => 'Invoice Save Error!.',
        ));
    }
}
