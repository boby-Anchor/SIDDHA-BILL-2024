<?php
// Database connection
$conn ;

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$productId = $_POST['productId'];
$updatedProductName = $_POST['updatedProductName'];
// Add more data fields if necessary

// Update the database
$sql = "UPDATE p_medicine SET name = '$updatedProductName' WHERE id = $productId";

if ($conn->query($sql) === TRUE) {
  echo "Product updated successfully";
} else {
  echo "Error updating product: " . $conn->error;
}

$conn->close();
?>
