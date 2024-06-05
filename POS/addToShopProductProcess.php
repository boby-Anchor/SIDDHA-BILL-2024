<?php
include('config/db.php');

$productId = $_POST["productId"];
$allShop = $_POST["allShop"];
$shopcheck = $_POST["shopcheck"];
$shopId = $_POST["shopId"];


// Clicked All Button =================================================================================

if ($allShop == "true") {
    $conn->query("UPDATE p_medicine SET selectedShops = 'all' WHERE id = '$productId'");

    $checkResult = $conn->query("SELECT * FROM `producttoshop` WHERE `medicinId` = '$productId'");
    $checkRows = numRows("SELECT * FROM producttoshop WHERE medicinId = '$productId'");
    $productToShopData = mysqli_fetch_assoc($checkResult);
    if ($checkRows > 0) {
        if ($productToShopData["productToShopStatus"] == "added") {
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'all' WHERE medicinId = '$productId'");
        } else if ($productToShopData["productToShopStatus"] == "remove") {
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'all' WHERE medicinId = '$productId'");
        }
    }
} else {
    $conn->query("UPDATE p_medicine SET selectedShops = '' WHERE id = '$productId'");

    $checkResult = $conn->query("SELECT * FROM `producttoshop` WHERE `medicinId` = '$productId'");
    $checkRows = numRows("SELECT * FROM producttoshop WHERE medicinId = '$productId'");
    $productToShopData = mysqli_fetch_assoc($checkResult);
    if ($checkRows > 0) {
        if ($productToShopData["productToShopStatus"] == "all") {
            echo "up all";
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'remove' WHERE medicinId = '$productId'");
        }
    }
}


// Clicked One By One Shop=================================================================================================
if ($shopcheck == "true") {
    $checkResult = $conn->query("SELECT * FROM producttoshop WHERE medicinId = '$productId' AND shop_id = '$shopId'");
    $checkRows = numRows("SELECT * FROM producttoshop WHERE medicinId = '$productId' AND shop_id = '$shopId'");
    $productToShopData = mysqli_fetch_assoc($checkResult);
    if ($checkRows > 0) {
        if ($productToShopData["productToShopStatus"] == "added") {
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'remove' WHERE medicinId = '$productId' AND shop_id = '$shopId'");
        } else if ($productToShopData["productToShopStatus"] == "remove") {
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'added' WHERE medicinId = '$productId' AND shop_id = '$shopId'");
        } else if ($productToShopData["productToShopStatus"] == "all") {
            echo "all are all";
            $conn->query("UPDATE producttoshop SET productToShopStatus = 'remove' WHERE medicinId = '$productId' AND shop_id = '$shopId'");
        }
    } else {
        $conn->query("INSERT INTO producttoshop (medicinId,shop_id,productToShopStatus) VALUES ('$productId','$shopId','added')");
    }
} else {
    $conn->query("UPDATE producttoshop SET productToShopStatus = 'remove' WHERE medicinId = '$productId' AND shop_id = '$shopId'");
}
