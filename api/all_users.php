<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key']=="@123abcd1366" && $_GET['publisher_id']) {
$publisher_id = $_GET['publisher_id'];
$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$resultsqu = $data->getuserdata($publisher_id);
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];
$userdata = [
        'log_name' =>$log_name,
        'username' =>$username,
        'createdate' =>$createdate,
    ];
   $result = $data->insertuserlog($userdata);


	
	$rediskey = "usersdata";
   if ($nredis->exists($rediskey)) {
    $jsonData = $nredis->executeRaw(['JSON.GET', $rediskey]);
    $allusers = json_decode($jsonData, true); // Decode the JSON data
    echo json_encode($allusers);
     }else{
	 $results =  $data->getuserdata($publisher_id); 
    $jsondata = array();
	while ($rows = pg_fetch_array($results)) {
	$name = $rows['name'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'name' => $name
    ];
     $jsonData = json_encode($jsondata);
	// /var_dump($jsonData);
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
