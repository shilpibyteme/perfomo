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
	  $rediskeynew = "categories";
	  if($nredis->exists($rediskeynew)){
 				$allcat = $nredis->zRevRange($rediskeynew, 0, -1);
        foreach ($allcat as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
     }else{
	$result = $data->getcategory(); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$category_id = $row['category_id'];
	$category_name = $row['category_name'];
	//$feed_url = $row['feed_url'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'category_id' => $category_id,
        'category_name' => $category_name,
    ];
	  response($category_id,$category_name,$response_code,$response_desc);
	 
      
     }

      $score = rand(10,1000000);
      $key ="categories";
      $nredis->zAdd($key,$score, json_encode($jsondata));
      $ttlInSeconds = 3600;
      $nredis->expire($key, $ttlInSeconds);
    
  }
 }
}

function response($category_id, $category_name,$response_code,$response_desc){
	$response['category_id'] = $category_id;
	$response['category_name'] = $category_name;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	 
	 
	$json_response = json_encode($response);
	echo $json_response;
}
?>


