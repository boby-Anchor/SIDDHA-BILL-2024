<?php
include ('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['sd'], true);
    $start_date = $data['STDATE'];
    $end_date = $data['ENDDATE'];
    // Ensure user_id is set in the session  
     $user_id ;
     $shop_id ;
     
     if (isset($_SESSION['store_id'])) {
            $userLoginData = $_SESSION['store_id'];
            foreach ($userLoginData as $userData) {
                $user_id = $userData['id'];
                $shop_id = $userData['shop_id'];
            }
     }        
     
    //  $conn->query("INSERT INTO `test`(`c1`, `c2`) VALUES ('$user_id','$shop_id')");
    //  $conn->query("INSERT INTO `test`(`c1`, `c2`) VALUES ('$start_date','$end_date')");

    $sql = $conn->query("SELECT invoices.*, payment_type.payment_type, bill_type.bill_type_name ,users.name ,shop.shopName
                         FROM invoices 
                         INNER JOIN payment_type ON payment_type.payment_type_id = invoices.payment_method 
                         INNER JOIN bill_type ON bill_type.bill_type_id = invoices.bill_type_id 
                         INNER JOIN users ON users.id = invoices.user_id 
                         INNER JOIN shop ON shop.shopId = invoices.shop_id 
                         WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' 
                         AND invoices.shop_id = '$shop_id' 
                         ORDER BY invoices.created
                         ");
    // shop_id
    $result = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) AS total_amount 
                                               FROM invoices 
                                               WHERE DATE(`created`) BETWEEN '$start_date' AND '$end_date' 
                                               "));
    
    $output = '';
    while ($row = mysqli_fetch_assoc($sql)) {
        $output .= '<tr>
                        <td>' . $row['invoice_id'] . '</td>
                        <td>' . $row['p_name'] . '</td>
                        <td>' . $row['contact_no'] . '</td>
                        <td>' . $row['d_name'] . '</td>
                        <td>' . $row['reg'] . '</td>
                        <td>' . $row['total_amount'] . '</td>
                        <td>' . $row['payment_type'] . '</td>
                        <td>' . $row['bill_type_name'] . '</td>
                        <td>' . $row['name'] . '</td>
                        <td>' . $row['shopName'] . '</td>
                    </tr>';
    }
    $output .= '<tr class="bg-dark">
                    <td></td>
                    <td class="fw-bold" style="font-size:larger;">Total Sales</td>
                    <td class="fw-bold" style="font-size:larger;">' . $result['total_amount'] . ' LKR</td>
                </tr>';

    echo $output;
}
?>
