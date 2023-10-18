<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
$jsondata = array();
$article_id = $_GET['article_id'];
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskeyrank ='ranking__'.$article_id;
    if($nredis->exists($rediskeyrank)){
	 $allarticlrank = $nredis->zRevRange($rediskeyrank, 0, -1);
        foreach ($allarticlrank as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
     }else{
	$query = "SELECT * FROM dev_performo.article_ranking WHERE article_id='$article_id'"; 
    $result = pg_query($query); 
     if(pg_num_rows($result)>0){
	while ($row = pg_fetch_array($result)) {
	$rank = $row['rank'];
	$rank_datetime = $row['rank_datetime'];
	$rank_datetime = $row['rank_datetime'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'rank' => $rank,
        'rank_datetime' => $rank_datetime,
    ];
     $key ='ranking__'.$article_id;
     $score = strtotime($rank_datetime);
     $nredis->zAdd($key,$score, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	response($rank,$rank_datetime,$response_code,$response_desc);
    }
   }else{
             $emptyArray = array();
             echo json_encode($emptyArray);
             die;
            } 
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
