<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   


if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskeyuser = $_GET['key'];
	 $article_id = $_GET['article_id'];
	 $allrank = $nredis->get($rediskeyuser);
	if($allrank){
      echo $allrank;
     }else{
	 $query = "SELECT * FROM dev_performo.article_ranking WHERE article_id='$article_id'"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$rank = $row['rank'];
	$rank_datetime = $row['rank_datetime'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'rank' => $rank,
        'rank_datetime' => $rank_datetime,
    ];
	response($rank,$rank_datetime,$response_code,$response_desc);
    }
    $nredis->set("articlerank", json_encode($jsondata));
    $nredis->flushall();
 }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($rank,$rank_datetime,$response_code,$response_desc){
	$response['rank'] = $rank;
	$response['rank_datetime'] = $rank_datetime;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
