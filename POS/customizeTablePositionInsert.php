<?php
require "./config/db.php";

$position = $_POST['position'];
$title = $_POST['title'];

$sql = "UPDATE customizable_data SET `position` = '$position' WHERE title = '$title' ";

if ($conn->query($sql) !== TRUE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
