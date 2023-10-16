<?php

require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
include('../database.php');
$jsondata = array();
if ($_GET['token_key']=="@123abcd1366") {
	 $category = $_GET['category'];
	 $rediskeyatr = $_GET['key'];
	
	 //$guid = $_GET['guid'];
	 $preferencuser = $nredis->get($rediskeyatr);
	if($preferencuser){
     echo $preferencuser;
    }else{

	 $query = "SELECT * FROM dev_performo.user_preferences WHERE category='$category'";
    $result = pg_query($query);    
	while ($row = pg_fetch_array($result)) {
	$category = $row['category'];
	$user_id = $row['user_id'];
	$publisher_name = str_replace(" ","",$row['publisher_name']);
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'category' => $category,
        'user_id' => $user_id,
        'publisher_name' => $publisher_name,
        'response_code' => $response_code,
        'response_desc' => $response_desc
    ];

	response($category,$user_id,$publisher_name,$response_code,$response_desc);
    }
    $nredis->set("user_preference", json_encode($jsondata));
 }

}else{
	response(NULL, NULL,NULL, 400,"Invalid Request");
	}

function response($category,$user_id,$publisher_name,$response_code,$response_desc){
	$response['category'] = $category;
	$response['user_id'] = $user_id;
	$response['publisher_name'] = $publisher_name;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}

?>
