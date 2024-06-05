<?php
include('config/db.php');

if (isset($_POST['unitId'])) {
    $unitId = $_POST['unitId'];

    // Query to get unit category variations based on unitId
    $sql = $conn->prepare("SELECT ucv_id, ucv_name , unit FROM unit_category_variation INNER JOIN medicine_unit ON medicine_unit.id = unit_category_variation.p_unit_id WHERE p_unit_id = ?");
    $sql->bind_param("i", $unitId);
    $sql->execute();
    $result = $sql->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}
?>
