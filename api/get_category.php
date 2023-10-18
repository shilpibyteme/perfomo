<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
include('../database.php');
if ($_GET['token_key']=="@123abcd1366") {
	
	  $rediskeynew = "categories";
	  if($nredis->exists($rediskeynew)){
 				$allcat = $nredis->zRevRange($rediskeynew, 0, -1);
        foreach ($allcat as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
     }else{
	 $query = 'SELECT DISTINCT(category_name),category_id FROM dev_performo.publisher_category_mapping';
    $result = pg_query($query); 
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
}else{
	response(NULL,NULL,400,"Invalid Request");
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


