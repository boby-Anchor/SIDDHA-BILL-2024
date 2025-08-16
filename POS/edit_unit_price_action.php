<?php
include('config/db.php');
session_start();

if (isset($_SESSION['store_id'])) {
    $userLoginData = $_SESSION['store_id'][0];
    $user_id = $userLoginData['id'] ?? null;
    $shop_id = $userLoginData['shop_id'] ?? null;
} else {
    echo json_encode(array(
        'status' => 'sessionExpired',
        'message' => 'Session expired. Wait to Login again.',
    ));
    exit();
}

if (isset($_POST['products'])) {

    $poArray = json_decode($_POST['products'], true);

    $errorOccurred = false;
    $notificationMessage = "";

    if (is_array($poArray) && !empty($poArray)) {

        try {

            foreach ($poArray as $product) {

                $product_code = $product['product_code'] ?? "code na.\n";
                $product_name = $product['product_name'] ?? "nama na.\n";

                $item_cost = $product['item_cost'] ?? "item cost na\n";
                $unit_cost = $product['unit_cost'] ?? "unit cost na\n";
                $item_price = $product['item_price'] ?? "item price na\n";
                $unit_price = $product['unit_price'] ?? "unit price na\n";

                if (
                    !empty($product['product_code']) &&
                    !empty($product['product_name']) &&
                    // !empty($product['item_cost']) &&
                    // !empty($product['unit_cost']) &&
                    !empty($product['item_price'])
                    // !empty($product['unit_price'])
                ) {

                    $product_code = $product['product_code'];
                    $product_name = $product['product_name'];

                    $item_cost = $product['item_cost'];
                    $unit_cost = $product['unit_cost'] ?? 0;
                    $item_price = $product['item_price'];
                    $unit_price = $product['unit_price'] ?? 0;

                    $conn->query("UPDATE stock2 SET unit_s_price = '$unit_price'
                        WHERE stock_item_code = '$product_code' AND item_s_price='$item_price' AND stock_shop_id = '$shop_id'");

                    if ($conn->affected_rows > 0) {
                        $notificationMessage .= $product_name . " Price update Success.\n";
                    } else {
                    $notificationMessage .= $product_name . " No data were updated.\n";
                    }

                } else {
                    $errorOccurred = true;
                    $error_message = "Data update error\n";
                }
            }
        } catch (Exception $exception) {
            $errorOccurred = true;
            $error_message = "ERROR: " . $exception->getMessage() . "\n";
            error_log($error_message);
            $notificationMessage .= $exception->getMessage();
        } finally {
            if ($errorOccurred) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => $error_message
                ));
                exit();
            } else {
                echo json_encode(array(
                    'status' => 'success',
                    'message' => $notificationMessage
                ));
            }
        }
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'No products found or invalid data received.',
        ));
        exit();
        // "No products found or invalid data received.";
    }
} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'No Data received in the POST request.',
    ));
    exit();
    // "No Data received in the POST request.";
}