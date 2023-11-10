<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$jsondataran = array();
 $data= new PocModel;
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

$article_id = $_REQUEST['article_id'];
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$data= new PocModel;
       $resultsqu = $data->getarticleuser($article_id);
	     if(pg_num_rows($resultsqu)>0){
       $rowque = pg_fetch_array($resultsqu);
            $username=$rowque['name'];
                $userdata = [
                    'log_name' =>$log_name,
                    'username' =>$username,
                    'createdate' =>$createdate,
                     ];
                $sqlquery = $data->insertuserlog($userdata);
		 }

	 $rediskeynew ='ranking__'.$article_id;
    if ($nredis->exists($rediskeynew)) {
        $allarticlenew = $nredis->zRevRange($rediskeynew,0,-1);
        if ($allarticlenew) {
            $jsonArray = [];
            foreach ($allarticlenew as $jsonString) {
                $jsonArray[] = json_decode($jsonString, true);
            }
            $jsonResultnew = json_encode($jsonArray);
            echo $jsonResultnew;
        } else {
            $jsonResultnew = [];
            echo $jsonResultnew;
        }
    }else{
	$result = $data->getranking($article_id); 
     if(pg_num_rows($result)>0){
			while ($rowran = pg_fetch_array($result)) {
			$rank = $rowran['rank'];
			$rank_datetime = $rowran['rank_datetime'];
			$response_code = 0;
			$response_desc = 'successful';
			$jsondataran = [
				'rank' => $rank,
				'rank_datetime' => $rank_datetime,
			];
			
			 $key ='ranking__'.$article_id;
			 $score = strtotime($rank_datetime);
			 $nredis->zAdd($key,$score, json_encode($jsondataran));
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
}
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
