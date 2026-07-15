<?php
include '../config/db.php';

// $sql = "UPDATE orders SET status = 4 WHERE status = 2";
$sql = "INSERT INTO test (c1, c2) VALUES (4, 2)";

error_log("thada thada bro badu wada");
error_log("with cron");

echo "Server time: " . date("Y-m-d H:i:s T");

if ($conn->query($sql) === TRUE) {
    echo "Update successful";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();