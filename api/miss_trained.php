<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
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
 
$date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : '';
$date_to = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : '';
$publisher_id = isset($_REQUEST['publisher_id']) ? $_REQUEST['publisher_id'] : '';
$log_name = '[{"date_from":'.'"'.$date_from.'"'.',"date_to":'.'"'.$date_to.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$jsondata = array();
     $rediskey ='missed_train__'.$publisher_id.'__'.$date_from.'__'.$date_to;
     if ($nredis->exists($rediskey)) {
        $allarticlenew = $nredis->zRevRange($rediskey, 0, -1);
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

    $results = $data->getlmissedtarin($date_from,$date_to);     
    if(pg_num_rows($results)>0){
        while ($resultkey = pg_fetch_array($results)) {
            $publisherid = $resultkey['publisher_id'];
        if($publisherid == $publisher_id && !empty($resultkey['keyword_name'])){
			$publishername = $resultkey['publisher_name'];
		   $rank = $resultkey['rank'];
           $keyword_name = $resultkey['keyword_name']; 
           $pubdate = $resultkey['pubdate']; 
           $response_code = 0;
           $response_desc = 'successful';
           $score = strtotime($pubdate);
          $key = 'missed_train__'.$publisher_id.'__'.$date_from.'__'.$date_to;
          $jsondata = [
              'missed_train' => $keyword_name,
              'rank'=>$rank,
              'publisher_name'=>$publishername,
              'publisher_id'=>$publisherid,
        ];
       
       $nredis->zAdd($key, $score, json_encode($jsondata));
       $ttlInSeconds = 3600;
       $nredis->expire($key, $ttlInSeconds);
       response($keyword_name,$rank,$publishername,$publisherid,$response_code,$response_desc);
    }else{
        $emptyArray = array();
            echo json_encode($emptyArray);
            die;
    }
       }
     }else{
         $emptyArray = array();
             echo json_encode($emptyArray);
             die;
     }
 }
}
    }

function response($keyword_name,$rank,$publishername,$publisherid,$response_code,$response_desc){
    $response['keyword_name'] = $keyword_name;
    $response['rank'] = $rank;
    $response['publishername'] = $publishername;
    $response['publisherid'] = $publisherid;
    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    $json_response = json_encode($response);
    echo $json_response;
}
?>
