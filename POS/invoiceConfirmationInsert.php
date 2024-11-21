<?php
session_start();
include('config/db.php');

try {
    function printErrorLog($error_message)
    {
        $error_log_path = "error_log.txt";
        file_put_contents($error_log_path, $error_message, FILE_APPEND);
    }
} catch (Exception $exception) {
}
try {
    function printInvoiceData($invDataLog)
    {
        $error_log_path = "error_log_invData.txt";
        $log_message_str = json_encode($invDataLog, JSON_PRETTY_PRINT) . PHP_EOL;

        file_put_contents($error_log_path, $log_message_str, FILE_APPEND);
    }
} catch (Exception $exception) {
    printErrorLog($exception->getMessage());
}

$invoiceNumber = $_SESSION["invoiceNumber"];
$currentDateTime = date("Y-m-d H:i:s");

$itemData = isset($_POST['itemData']) ? json_decode($_POST['itemData'], true) : [];
$dMData = isset($_POST['dMData']) ? json_decode($_POST['dMData'], true) : [];
$billData = isset($_POST['billData']) ? json_decode($_POST['billData'], true) : [];

$invDataLog = [
    'timestamp' => date('Y-m-d H:i:s'),
    'billData' => is_array($billData) ? $billData : "(No Data Received: $billData)",
    'dMData' => is_array($dMData) ? $dMData : "(No Data Received: $dMData)",
    'itemData' => is_array($itemData) ? $itemData : "(No Data Received: $itemData)",
];

printInvoiceData($invDataLog);

$cm = runQuery("SELECT invoice_id  FROM `invoices` WHERE invoice_id = '$invoiceNumber'");

//  check if session is started
if (isset($_SESSION['store_id'])) {
    $userData = $_SESSION['store_id'][0];

    $userId = $userData['id'];
    $shop_id = $userData['shop_id'];

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
    $paymentmethodselector;
    $selectBillType;

    //  check if invoice is already inserted
    if (empty($cm) && !empty($billData)) {

        try {
            if (is_array($billData) && !empty($billData)) {
                foreach ($billData as $product) {
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
                    $paymentmethodselector = $product['paymentmethodselector'];
                    $selectBillType = $product['selectBillType'];
                }  // close for-each $billData
            }  // billData[] end
        } catch (Exception $exception) {
            printErrorLog($exception->getMessage());
        }

        try {
            if (is_array($dMData) && !empty($dMData)) {
                foreach ($dMData as $product) {
                    $product_name = $product['product_name'];
                    $item_cost = $product['item_cost'];
                    $item_price = $product['item_price'];

                    $conn->query("INSERT INTO dm_items (invoice_id, invoiceDate, dmName, itemPrice, totalPrice)
                VALUES ('$invoiceNumber', '$currentDateTime', '$product_name', '$item_cost', '$item_price')");
                }  // close for-each $dMData 
            }  // dMData[] end
        } catch (Exception $exception) {
            printErrorLog($exception->getMessage());
        }


        try {

            if (is_array($itemData) && !empty($itemData)) {
                foreach ($itemData as $product) {
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
                        $isPaththu = isset($isPaththu) && $isPaththu == 1 ? $isPaththu : null;

                        $conn->query("INSERT INTO invoiceitems (invoiceNumber, invoiceDate, invoiceItem, invoiceItem_qty, invoiceItem_unit, invoiceItem_price, invoiceItem_total, isPaththu)
                    VALUES ('$invoiceNumber', '$currentDateTime', '$product_name', '$product_qty', '$product_unit', '$product_cost', '$productTotal', '$isPaththu')");

                        if ($product_unit == 'kg' || $product_unit == 'l') {

                            if ($item_price == $product_cost) {

                                $total_qty = $product_qty * 1000;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = (stock_item_qty - $product_qty),
                        stock_mu_qty = (stock_mu_qty - $total_qty)
                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code'
                        OR stock_minimum_unit_barcode = '$code')
                        AND (item_s_price = '$product_cost'
                        OR unit_s_price = '$product_cost')");
                            } else {
                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = (stock_item_qty - ROUND($product_qty / ($ucv * 1000), 3)),
                        stock_mu_qty = (stock_mu_qty - $product_qty)
                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' 
                        OR stock_minimum_unit_barcode = '$code')
                        AND unit_s_price = '$product_cost'");
                            }
                        } else if ($product_unit == 'g' || $product_unit == 'ml') {

                            if ($item_price == $product_cost) {
                                $total_qty = $product_qty * $ucv;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = (stock_item_qty - $sell_p_qty),
                        stock_mu_qty = (stock_mu_qty - $total_qty)
                        WHERE stock_shop_id = '$shop_id'
                        AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                        AND (item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
                            } else {
                                $total_qty = $product_qty * 1000;
                                $sell_p_qty = ($total_qty / $ucv);

                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = (stock_item_qty - ROUND($product_qty / $ucv, 3)),
                        stock_mu_qty = (stock_mu_qty - $product_qty)
                        WHERE stock_shop_id = '$shop_id' 
                        AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                        AND unit_s_price = '$product_cost'");
                            }
                        } else if ($product_unit == 'm') {

                            if ($item_price == $product_cost) {
                                $product_minimum_qty = $product_qty * 100;
                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = (stock_item_qty -  $product_qty), 
                        stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                        WHERE stock_shop_id = '$shop_id'
                        AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                        AND item_s_price = '$product_cost'");
                            } else {
                                $product_minimum_qty = $product_qty;
                                $conn->query("UPDATE stock2 SET
                        stock_item_qty = ROUND((stock_mu_qty - $product_minimum_qty) / 100, 2), 
                        stock_mu_qty = (stock_mu_qty - $product_minimum_qty)
                        WHERE stock_shop_id = '$shop_id' AND (stock_item_code = '$code' OR stock_minimum_unit_barcode = '$code')
                        AND unit_s_price = '$product_cost'");
                            }
                        } else if ($product_unit == 'pack / bottle' || $product_unit == 'pieces') {
                            $conn->query("UPDATE stock2 SET stock_item_qty =  (stock_item_qty - $product_qty)
                        WHERE stock_shop_id = '$shop_id' AND stock_item_code = '$code' AND item_s_price = '$product_cost' OR unit_s_price = '$product_cost')");
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
                    }
                }  // close for-each $itemData
            }  // itemData[] end
        } catch (Exception $exception) {
            printErrorLog($exception->getMessage());
        }

        try {
            $conn->query("INSERT INTO invoices (invoice_id, user_id, shop_id, created, p_name, contact_no, d_name,reg,bill_type_id, payment_method, total_amount, discount_percentage, delivery_charges, value_added_services, paidAmount, cardPaidAmount, balance)
        VALUES ('$invoiceNumber', '$userId', '$shop_id', '$currentDateTime', '$patientName', '$contactNo', '$doctorName','$regNo','$selectBillType', '$paymentmethodselector', '$subTotal', '$discountPercentage', '$deliveryCharges', '$valueAddedServices', '$cashAmount', '$cardAmount', '$balance')");
        } catch (Exception $exception) {
            printErrorLog($exception->getMessage());
        }
    }
}

unset($_SESSION["invoiceNumber"]);
