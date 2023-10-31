<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
if ($_GET['token_key']=="@123abcd1366") {  
 $jsondata = array();
 $email = $_GET['email'];
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$data = new PocModel;
    $resultsql = $data->getuseremail($email);
    $rowsqls = pg_fetch_array($resultsql);
    $id=$rowsqls['id'];
     $uppredata = [
                'subscriber' =>'false',
               ];

         $result = $data->updatesignup($id,$uppredata);
       	  echo "data updated!!";
}

?>
