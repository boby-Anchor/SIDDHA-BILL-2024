<?php
session_start();
include('config/db.php');
$poBillData = json_decode($_POST['poBillData'], true);
$poArray = json_decode($_POST['products'], true);
$productsAllTotal = 0;
$message = "";

try {
    function printErrorLog($error_message)
    {
        $error_log_path = "error_log.txt";
        file_put_contents($error_log_path, $error_message, FILE_APPEND);
    }
} catch (Exception $exception) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'path Fatal Error. Contact IT Department',
    ));
    exit();
}

try {

    if (isset($_SESSION['store_id'])) {

        $userLoginData = $_SESSION['store_id'];

        foreach ($userLoginData as $userData) {

            $shop_id = $userData['shop_id'];

            if (is_array($poArray) && !empty($poArray)) {

                $orderNumber = $poBillData['orderNumber'];
                $orderDate = $poBillData['orderDate'];
                $orderTime = $poBillData['orderTime'];
                $deliveryDate = $poBillData['deliveryDate'];

                $orderDateTime = $orderDate . ' ' . $orderTime;

                foreach ($poArray as $product) {

                    $product_code = $product['product_code'];
                    $product_name = $product['product_name'];
                    $product_brand = $product['product_brand'];
                    $product_qty = intval($product['product_qty']);

                    if (
                        !empty($product['product_code']) && !empty($product['product_name'])
                        && !empty($product['product_qty'])
                    ) {
                        $conn->query("INSERT INTO hub_order (HO_number, HO_item, HO_brand, HO_qty, hub_order_unit, HO_price, HO_total,HO_shopId) 
                    VALUES ('$orderNumber', '$product_code', '$product_brand','$product_qty', '', '0', '0','$shop_id')");
                    } else {
                        echo "Invalid product entry ";
                    }
                }

                $orderDateTimeObj = new DateTime($orderDateTime, new DateTimeZone('Asia/Colombo'));
                $dateTimeFormatted = $orderDateTimeObj->format('Y-m-d H:i:s');

                $conn->query("INSERT INTO hub_order_details (hub_order_number,hub_order_subTotal,hub_order_status,hub_order_paymentType,hub_order_paymentStatus,order_date,delivery_date)
            VALUES ('$orderNumber','0','1','1','1','$dateTimeFormatted','$deliveryDate')");
            } else {
                echo "No products found or invalid data received.";
            }
        }
    }
} catch (Exception $exception) {
    printErrorLog($exception->getMessage() . $message);
    echo ($exception->getMessage() . $message);
}
