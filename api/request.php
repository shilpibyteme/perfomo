<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
 date_default_timezone_set('Asia/Kolkata');
$headers = getallheaders();
if (!array_key_exists('Authorization', $headers)) {

    echo json_encode(["error" => "Authorization header is missing"]);
    exit;
}
else {

    if ($headers['Authorization'] !== 'Bearer 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {

        echo json_encode(["error" => "Token keyword is missing"]);
        exit;
    }else{
 $jsondata = array();
 $email = $_REQUEST['email'];
    $jsondata = array();
    $resultsql = $data->getuseremail($email);
	
    $rowsqls = pg_fetch_array($resultsql);
	
    $id=$rowsqls['id'];
     $uppredata = [
                'request' =>'true',
               ];

         $result = $data->updatesignup($id,$uppredata);
       	  echo "data updated!!";
		       $key ='{access}:'.$email;  
           $allKeys = $nredis->del($key);  
  }
}

?>
