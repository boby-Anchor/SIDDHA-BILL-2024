<?php
// Database connection
require "./config/db.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch values from AJAX request
$shopId = $_POST['selectedShop'];
$contactNo = $_POST['contactNo'];
$address = $_POST['address'];
$note = $_POST['note'];

// File upload
$logo = '';
if (isset($_FILES['logo'])) {
    $file_name = $_FILES['logo']['name'];
    $file_size = $_FILES['logo']['size'];
    $file_tmp = $_FILES['logo']['tmp_name'];
    $file_type = $_FILES['logo']['type'];

    // Check file type
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));
    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions) === false) {
        die("Please upload a JPEG, JPG, or PNG file.");
    }

    // Check file size
    if ($file_size > 2097152) {
        die('File size must be less than 2 MB');
    }

    // Move file to uploads directory
    $upload_dir = "dist/img/billLogoes/";
    $logo = $upload_dir . uniqid() . '.' . $file_ext;
    move_uploaded_file($file_tmp, $logo);
}

// Check if data already exists for the selectedShop
$sql_check = "SELECT * FROM customize_bills WHERE `customize_bill_shop-id` = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $shopId);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Data already exists for selectedShop
    echo "Data already exists for this shop.";
} else {
    // Insert data into the database
    $sql_insert = "INSERT INTO customize_bills (customize_bills_logo,customize_bills_mobile,customize_bills_address,print_meta_status,print_paper_size,`discount_section-status`,bill_note,`customize_bill_shop-id`) VALUES (?, ?, ?, '1', '48','0', ?,?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssi", $logo, $contactNo, $address, $note, $shopId);

    if ($stmt_insert->execute()) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

$stmt_check->close();
$stmt_insert->close();
$conn->close();
