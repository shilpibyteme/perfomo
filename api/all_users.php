<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
include('../database.php');
require '../RedisMaster.php';

if ($_GET['token_key']=="@123abcd1366" && $_GET['publisher_id']) {
	
	$rediskey = "usersdata";
   if ($nredis->exists($rediskey)) {
    $jsonData = $nredis->executeRaw(['JSON.GET', $rediskey]);
    $allusers = json_decode($jsonData, true); // Decode the JSON data
    echo json_encode($allusers);
     }else{
     $publisher_id = $_GET['publisher_id'];
	 $query = "SELECT name FROM dev_performo.puser WHERE publisher_id='$publisher_id'"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$name = $row['name'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'name' => $name
    ];
    $jsonData = json_encode($jsondata);
	echo 'Data to be stored in Redis:';
	var_dump($jsonData);
	$key = "usersdata";
	$nredis->executeRaw(['JSON.SET', $key, '.', $jsonData]);
    //response($name,$response_code,$response_desc);
    }
    
 }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($name,$response_code,$response_desc){
	$response['title'] = $name;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
