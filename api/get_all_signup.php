<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
$jsonpublisher=array();
$email=$_GET['email'];
if ($_GET['token_key']=="@123abcd1366" && $_GET['email']!='') {

	      $rediskeynew ='{access}:'.$email;
       
		  if($nredis->exists($rediskeynew)){
			$allarticlenew = $nredis->sRandMember($rediskeynew);
			echo $allarticlenew;
			 $fromcache = true;
		   }else{
     $resultpub = $data->getuseremail($email);
	while ($row = pg_fetch_array($resultpub)) {
    $subscriber = $row['subscriber'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsonpublisher[] = [
        'subscriber' => $subscriber,
      ];
    
     
     $score = rand(10,1000000);
      $key ='{access}:'.$email;
	  $nredis->sAdd($key, json_encode($jsonpublisher));
    //  $nredis->zAdd($key,$score, json_encode($jsonpublisher));
      $ttlInSeconds = 3600;
      $nredis->expire($key, $ttlInSeconds);
	  response($subscriber,$response_code,$response_desc);
	}
 }
}else{
	response(NULL,NULL, NULL, 400,"Invalid Request");
	}

function response($subscriber,$response_code,$response_desc){
    $response['subscriber'] = $subscriber;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>


