<?php 

if(!isset($_POST['submit'])){
    echo "<script>window.history.back();</script>";
	exit();
}else{

    session_start();
    include('../config/db.php');

    $chk = 0;
    $name = $conn->real_escape_string($_POST['name']);   //required
    $email = $conn->real_escape_string($_POST['email']);   
    $phone = $conn->real_escape_string($_POST['phone']);
    $addr1 = $conn->real_escape_string($_POST['address']);   

    // -------------- Empty input field check 
    if(empty($name)){
        $chk=1;
        $_SESSION['e-msg']="Enter supplier name";
        echo "<script>window.history.back();</script>";
        exit();
    }

    // -------------- Empty input field check end

    $check = mysqli_num_rows($conn->query("SELECT `name`, `store` FROM `p_supplier` where `name`='$name' AND `store`='$_SESSION[store_id]'"));
    if($check > 0){
        $chk=1;
        $_SESSION['e-msg']="This name already exist";
        echo "<script>window.history.back();</script>";
        exit();
    }

    if($chk==0){
        $conn->query("INSERT INTO `p_supplier`(`store`, `name`, `email`, `phone`, `address`, `date`) VALUES ('$_SESSION[store_id]','$name','$email','$phone','$addr1', '$date')");          
        $_SESSION['msg'] ="Information submit successfully";
        echo "<script>window.history.back();</script>";
        exit();
        
    }else{
        $_SESSION['e-msg'] ="Something went wrong. Try later !!!";
        echo "<script>window.history.back();</script>";
        exit();
    }

}