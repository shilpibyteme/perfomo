<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
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
 $name = $_REQUEST['name'];
 $email = $_REQUEST['email'];
 $existemail = $data->getuseremail($email);
 if(pg_num_rows($existemail)>0){
 $rowque = pg_fetch_array($existemail);
 $email=$rowque['email'];
	 if ($email== $email) {
				echo "alredy exist data";
	  }
 }else{
 
 $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
 $image = $_REQUEST['image'];
 $subscriber = 'false';
 $request = 'false';
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$data = new PocModel;
        $datas = [
                'name' =>$name,
                'email' =>$email,
                'image' =>$image,
                'subscriber' =>$subscriber,
				'request'=>$request,
               ];
          $result = $data->insertsignupdata($datas);   
		 
    	echo "insert data";
    }
 
}
}
?>
