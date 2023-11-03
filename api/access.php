<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data= new PocModel;
$jsonpublisher=array();
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
        $email=$_REQUEST['email'];
        if ($email!='') {
	      $rediskeynew ='{access}:'.$email;   
          $allKeys = $nredis->del($rediskeynew); 
		  if($nredis->exists($rediskeynew)){
			$allarticlenew = $nredis->sRandMember($rediskeynew);
			echo $allarticlenew;
			 $fromcache = true;
		   }else{
     $resultpub = $data->getuseremail($email);
	while ($row = pg_fetch_array($resultpub)) {
    $subscriber = $row['subscriber'];
	$id = $row['id'];
	$publisher_id = $row['publisher_id'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsonpublisher[] = [
        'subscriber' => $subscriber,
		'userid' => $id,
		'publisher_id' => $publisher_id,
      ];
    
     
     $score = rand(10,1000000);
      $key ='{access}:'.$email;
	  $nredis->sAdd($key, json_encode($jsonpublisher));
    //  $nredis->zAdd($key,$score, json_encode($jsonpublisher));
      $ttlInSeconds = 3600;
      $nredis->expire($key, $ttlInSeconds);
	  response($subscriber,$id,$publisher_id,$response_code,$response_desc);
	}
 }
}else{
	response(NULL,NULL, NULL, 400,"Invalid Request");
	}
  }
}
function response($subscriber,$id,$publisher_id,$response_code,$response_desc){
    $response['subscriber'] = $subscriber;
	$response['userid'] = $id;
	$response['publisher_id'] = $publisher_id;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>


