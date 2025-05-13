<?php
include('../../config/db.php');
$orderNumber = $_POST['orderNumber'];
$status = $_POST['status'];

$conn->query("UPDATE hub_order_details SET hub_order_status = '$status' WHERE hub_order_number = '$orderNumber'");

// echo 'success';
