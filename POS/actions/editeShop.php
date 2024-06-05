<?php
session_start();
require_once("../config/db.php");

if (isset($_POST["saveEdite"])) {
    $shopId = $_POST["shopId"];

    $shopName = $_POST["shopName"];
    $shopTel = $_POST["shopTel"];
    $shopWhatsapp = $_POST["shopWhatsapp"];
    $shopEmail = $_POST["shopEmail"];
    $shopAdress = $_POST["shopAdress"];

    $managerName = $_POST["managerName"];
    $managerTel = $_POST["managerTel"];
    $managerWhatsapp = $_POST["managerWhatsapp"];
    $managerEmail = $_POST["managerEmail"];
    $managerAddress = $_POST["managerAddress"];

    $conn->query("UPDATE `shop` SET `shopName` = '$shopName' , `shopEmail` = '$shopEmail', `shopAddress` = '$shopAdress' , `shopTel` = '$shopTel' , `shopWhatsApp` = '$shopWhatsapp' ,`shopManagerName` = '$managerName' , `shopManagerEmail` = '$managerEmail' , `shopManagerAddress` = '$managerAddress' , `shopManagerTel` = '$managerTel' , `shopManagerWhatsApp` = '$managerWhatsapp' WHERE `shopId` = '$shopId'");

    $_SESSION['msg'] ="Update Saved Success...";
    echo "<script>window.history.back();</script>";
    exit();
}else{
    $_SESSION['e-msg'] ="Somthing Went Wrong! Try again...";
    echo "<script>window.history.back();</script>";
    exit();
}
