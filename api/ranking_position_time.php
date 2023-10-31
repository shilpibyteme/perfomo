<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key']=="@123abcd1366") {
 

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$publisher_id = isset($_GET['publisher_id']) ? $_GET['publisher_id'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$log_name = '[{"date_from":'.'"'.$date_from.'"'.',"date_to":'.'"'.$date_to.'"'.',"publisher_id":'.'"'.$publisher_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
if (!empty($publisher_id)) {
        // Prepare and execute the first query
        
      
       $resultsqu = $data->getuserdata($publisher_id);
       
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
     $rediskey = 'position__minutes'.$publisher_id;
     if($nredis->exists($rediskey)){
     $allarticlekey = $nredis->hGetAll($rediskey);
    // $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            $jsonArrayOutput[] = $jsonArray; 
        }
        echo json_encode($jsonArrayOutput);

     }else{

    $results = $data->getpostionrankingtime($date_from,$date_to,$publisher_id);     
   if(pg_num_rows($results)>0){
     while ($resultkey = pg_fetch_array($results)) {
     $rank_minute = $resultkey['count'] * 15;
     $rank = $resultkey['rank'];
     if($rank == 1 || $rank == 2 || $rank == 3){
        $response_code = 0;
        $response_desc = 'successful';
         //$score = strtotime($keywordfirstseendate);
       // $nredis->zAdd($key,$score, json_encode($jsondata));
       $key = 'position__minutes' . $publisher_id;
       $nredis->hSet($key, $rank, json_encode([
           'rank_minute' => $rank_minute,
           'rank' => $rank,
       ]));
   
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
    response($rank_minute,$rank,$response_code,$response_desc);
}

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

function response($rank_minute,$rank,$response_code,$response_desc){
    $response['rank_minute'] = $rank_minute;
    $response['rank'] = $rank;
    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    $json_response = json_encode($response);
    echo $json_response;
}
?>
