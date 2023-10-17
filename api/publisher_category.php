<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   


if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskey = $_GET['key'];
	 $newcat = $nredis->get($rediskey);
	 if($newcat){
      echo $newcat;
     }else{
	 $query = 'SELECT * FROM dev_performo.publisher_category_mapping';
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$category_id = $row['category_id'];
	$publisher_id = $row['publisher_id'];
	$category_name = $row['category_name'];
	$publisher_name = $row['publisher_name'];
	$publisher_salt = $row['publisher_salt'];
	$feed_url = $row['feed_url'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'category_id' => $category_id,
        'publisher_id' => $publisher_id,
        'category_name' => $category_name,
        'publisher_name' => $publisher_name,
        'publisher_salt' => $publisher_salt,
        'feed_url' => $feed_url
    ];
	response($category_id, $publisher_id, $category_name,$publisher_name,$publisher_salt,$feed_url,$response_code,$response_desc);
     }

    $nredis->set("publisher_category_publisher", json_encode($jsondata));
   // $nredis->flushall();
    
 }
}else{
	response(NULL, NULL,NULL, NULL,NULL, NULL, 400,"Invalid Request");
	}

function response($category_id, $publisher_id, $category_name,$publisher_name,$publisher_salt,$feed_url,$response_code,$response_desc){
	$response['category_id'] = $category_id;
	$response['publisher_id'] = $publisher_id;
	$response['category_name'] = $category_name;
	$response['publisher_name'] = $publisher_name;
	$response['publisher_salt'] = $publisher_salt;
	$response['feed_url'] = $feed_url;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	 
	 
	$json_response = json_encode($response);
	echo $json_response;
}
?>


