<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key']=="@123abcd1366") {
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$log_name = '[{"date_from":'.'"'.$date_from.'"'.',"date_to":'.'"'.$date_to.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$jsondata = array();
     $rediskey = 'top_keyword'.$date_from;
     if($nredis->exists($rediskey)){
     $allarticlekey = $nredis->hGetAll($rediskey);
    // $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            $jsonArrayOutput[] = $jsonArray; 
        }
        echo json_encode($jsonArrayOutput);

     }else{

    $results = $data->gettopkeyword($date_from,$date_to);     
   if(pg_num_rows($results)>0){
     while ($resultkey = pg_fetch_array($results)) {
     $topkeycount = $resultkey['name'];
     $response_code = 0;
     $response_desc = 'successful';
      //$score = strtotime($keywordfirstseendate);
    // $nredis->zAdd($key,$score, json_encode($jsondata));
    $key =  'top_keyword'.$date_from;
    $nredis->hSet($key, $topkeycount, json_encode([
        'topkeycount' => $topkeycount,
    ]));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
    response($topkeycount,$response_code,$response_desc);
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

function response($topkeycount,$response_code,$response_desc){
    $response['topkeycount'] = $topkeycount;
    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    $json_response = json_encode($response);
    echo $json_response;
}
?>
