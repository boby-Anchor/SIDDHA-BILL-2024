<?php

$error_message = isset($_POST['message']) ? json_decode($_POST['message'], true) : 'Empty error message received.';

function printErrorLog($error_message)
{
    $error_log_path = $_SERVER['DOCUMENT_ROOT'] . "/s.ceylonhospitals.com/POS/error_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    $log_entry = "[$timestamp] [ERROR] $error_message\n";
    if (file_put_contents($error_log_path, $log_entry, FILE_APPEND) !== false) {
        echo 'success';
    } else {
        echo 'error';
    }
}
