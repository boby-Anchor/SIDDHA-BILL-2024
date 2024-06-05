<?php
require "../config/db.php";
session_start();
if (isset($_GET["newUnit"])) {
    $newUnit = $_GET["newUnit"];

    $currentDateTime = date("Y-m-d H:i:s");

    $medicine_unit_rs = $conn->query("SELECT * FROM `medicine_unit` WHERE `unit` = '$newUnit'");
    $medicine_unit_num = $medicine_unit_rs->num_rows;

    if ($medicine_unit_num == "0" && $newUnit != "") {

        $conn->query("INSERT INTO medicine_unit (unit,DATE) VALUES ('$newUnit','$currentDateTime')");
        echo "Unit added successfully !";
    } elseif ($medicine_unit_num == "0" && $newUnit == "") {
        echo "Unit Can't be Empty !";
    } else {

        echo "This unit already exists !";
    }
}
