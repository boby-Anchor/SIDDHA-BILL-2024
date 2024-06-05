<?php
 include('config/db.php');
?>
<select name='quantity_unit' class='form-control bg-dark' id='qtyUnitSelector'>
    <option value='0'>select unit</option>
    <?php
    $medicine_unit_rs = $conn->query("SELECT * FROM `medicine_unit`");
    while ($medicine_data = $medicine_unit_rs->fetch_assoc()) {
    ?> <option value='<?= $medicine_data["unit"] ?>'><?= $medicine_data["unit"] ?></option>
    <?php
    }
    ?>
</select>
<button class='btn btn-primary' data-toggle='modal' data-target='#addunitmodal'>+</button>
<?php
?>