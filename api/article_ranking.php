<?php
header("Content-Type:application/json");
require './query.php';
include('../database.php');
require '../RedisMaster.php';
$data = new PocModel;
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key']=="@123abcd1366") {

$article_id = isset($_GET['article_id']) ? $_GET['article_id'] : '';
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
 $data= new PocModel;
if (!empty($article_id)) {
        // Prepare and execute the first query
        
      
       $resultsqu = $data->getarticleuser($article_id);
       $rowque = pg_fetch_array($resultsqu);
            $username=$rowque['name'];
                $userdata = [
                    'log_name' =>$log_name,
                    'username' =>$username,
                    'createdate' =>$createdate,
                     ];
                $sqlquery = $data->insertuserlog($userdata);
 
}
$jsondata = array();
$article_id = $_GET['article_id'];

	include('../database.php');
	 $rediskeyrank ='ranking__'.$article_id;
    if($nredis->exists($rediskeyrank)){
	 $allarticlrank = $nredis->zRevRange($rediskeyrank, 0, -1);
        foreach ($allarticlrank as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
     }else{
	$result = $data->getranking($article_id); 
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
