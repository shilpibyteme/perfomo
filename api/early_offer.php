<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
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
$category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
$log_name = '[{"date_from":'.'"'.$date_from.'"'.',"date_to":'.'"'.$date_to.'"'.',"publisher_id":'.'"'.$publisher_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
if (!empty($publisher_id)) {
        // Prepare and execute the first query
        
      
       $resultsqu = $data->getuserdata($publisher_id);
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
}
$jsondata = array();
     $rediskey = 'early_offer'.$publisher_id.'__'.$date_from.'__'.$date_to;
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

    $results = $data->getearlyoffer($date_from,$date_to,$publisher_id);     
    if(pg_num_rows($results)>0){
        while ($resultkey = pg_fetch_array($results)) {
        $rank = $resultkey['rank'];
        $publisher_name = $resultkey['publisher_name'];
        if($rank==1){
           $keyword_name = $resultkey['keyword_name'];
    
           $response_code = 0;
           $response_desc = 'successful';
            //$score = strtotime($keywordfirstseendate);
          // $nredis->zAdd($key,$score, json_encode($jsondata));
          $key = 'early_offer'.$publisher_id.'__'.$date_from.'__'.$date_to;
          $jsondata = [
            'early_keyword_name' => $keyword_name,
              'rank'=>$rank,
              'publisher_name'=>$publisher_name,
        ];
          $score = $rank;
          $nredis->zAdd($key, $score, json_encode($jsondata));
          $ttlInSeconds = 3600;
          $nredis->expire($key, $ttlInSeconds);
          response($keyword_name,$response_code,$response_desc);
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

function response($keyword_name,$response_code,$response_desc){
    $response['keyword_name'] = $keyword_name;
    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    $json_response = json_encode($response);
    echo $json_response;
}
?>
