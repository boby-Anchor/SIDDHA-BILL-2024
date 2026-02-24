<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    echo json_encode([
        'status' => 'session_expired',
        'message' => 'Session expired. Login again',
    ]);
    exit();
}

require_once '../config/db.php';

$chk = 0;
$datetime = date('Y-m-d H:i:s');

function errorThrow($message)
{
    echo json_encode([
        'status' => 'error',
        'message' => $message,
    ]);
}

if (isset($_POST['product_details'])) {
    $productDetails = $_POST['product_details'];
    $product_code = $conn->real_escape_string($productDetails['product_code']);
    $product_name = $conn->real_escape_string($productDetails['product_name']);
    $product_code = $conn->real_escape_string($productDetails['product_code']);
    $unit = $conn->real_escape_string($productDetails['unit']);
    $category_product = $conn->real_escape_string($productDetails['category_product']);
    $unit_variation = $conn->real_escape_string($productDetails['unit_variation']);
    $brand_product = $conn->real_escape_string($productDetails['brand_product']);
    $details = $conn->real_escape_string($productDetails['details']);
    $defaultImagePath = "../dist/img/product/not-available.png";
    $uploadFileRename = "not-available.png";

    $check = numRows("SELECT * FROM p_medicine WHERE `name`= '$product_name' AND `category` = '$category_product' AND `brand` = '$brand_product' AND `medicine_unit_id` = '$unit' AND `code` = '$product_code' AND unit_variation = '$unit_variation'");
    if ($check > 0) {
        $chk = 1;
        errorThrow("This product already exist");
        exit();
    }

    $check = numRows("SELECT * FROM p_medicine WHERE `code` = '$product_code'");
    if ($check > 0) {
        $chk = 1;
        errorThrow("This Barcode already exist");
        exit();
    }

    if (!$chk > 0) {
        $insert = $conn->query("INSERT INTO p_medicine (`name`,`details`,`category`,`brand`,`medicine_unit_id`,`code`,`img`,`date`,`unit_variation`)
        VALUES ('$product_name','$details','$category_product','$brand_product','$unit','$product_code','$uploadFileRename','$datetime','$unit_variation')");
        $product_id = $conn->insert_id;
        $shop_result = $conn->query("SELECT * FROM shop");
        while ($shop_data = $shop_result->fetch_assoc()) {
            $conn->query("INSERT INTO producttoshop (medicinId,shop_id,productToShopStatus) VALUES ('$product_id','" . $shop_data['shopId'] . "','remove')");
        }

        $conn->query("UPDATE producttoshop  SET productToShopStatus = 'added'  WHERE shop_id = '1' AND medicinId ='$product_id'");

        echo json_encode([
            'status' => 'success',
            'message' => 'New item added successfully',
        ]);
    }
} else {
    errorThrow("No product details received.");
}
