<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submitBrand'])) {
        $unitvariation = $_POST['unitvariation'];
        $main_unit_id = $_POST['main_unit']; // Assuming you add 'name' attribute to select element

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO unit_category_variation (ucv_name,p_unit_id,ucv_status) VALUES (?,?,'1')");
        $stmt->bind_param("si", $unitvariation, $main_unit_id);


        if ($stmt->execute()) {
            // Redirect to the same page
            header("Location: ../add-unit-variation.php");
            exit();
        } else {
            // Handle error
            $error_message = "Error occurred while adding unit variation.";
        }
    }
}
