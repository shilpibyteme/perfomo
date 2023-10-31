<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
$jsonpublisher=array();
if ($_GET['token_key']=="@123abcd1366") {

	      $rediskeynew = "get_all_signup";
       
 				if($nredis->exists($rediskeynew)){
 				$allpublisher = $nredis->zRevRange($rediskeynew, 0, -1);
        foreach ($allpublisher as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
     }else{
     $resultpub = $data->getsignup();
	while ($row = pg_fetch_array($resultpub)) {
	$publisher_id = $row['publisher_id'];
	$name = $row['name'];
	$email = $row['email'];
	$image = $row['image'];
    $subscriber = $row['subscriber'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsonpublisher[] = [
        'name' => $name,
        'email' => $email,
        'image' => $image,
        'subscriber' => $subscriber,
      ];
    response($publisher_id,$name,$email,$image,$subscriber,$response_code,$response_desc); 
     }
     $score = rand(10,1000000);
      $key ="get_all_signup";
      $nredis->zAdd($key,$score, json_encode($jsonpublisher));
      $ttlInSeconds = 3600;
      $nredis->expire($key, $ttlInSeconds);
   
 }
}else{
	response(NULL,NULL, NULL, 400,"Invalid Request");
	}

function response($publisher_id,$name,$email,$image,$subscriber,$response_code,$response_desc){
	$response['publisher_id'] = $publisher_id;
	$response['name'] = $name;
	$response['email'] = $email;
    $response['publisher_id'] = $publisher_id;
	$response['name'] = $name;
	$response['email'] = $email;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	 
	 
	$json_response = json_encode($response);
	echo $json_response;
}
?>


