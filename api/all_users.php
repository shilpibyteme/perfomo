<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   


if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskeyuser = $_GET['key'];
	 $publisher_id = $_GET['publisher_id'];
	 $allusers = $nredis->get($rediskeyuser);
	if($allusers){
      echo $allusers;
     }else{
	 $query = "SELECT * FROM dev_performo.puser WHERE publisher_id='$publisher_id'"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$name = $row['name'];
	$password = $row['password'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'name' => $name,
        'password' => $password,
    ];
	response($name,$password,$response_code,$response_desc);
    }
    $nredis->set("users_data", json_encode($jsondata));
    $nredis->flushall();
 }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($name,$password,$response_code,$response_desc){
	$response['title'] = $name;
	$response['password'] = $password;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
