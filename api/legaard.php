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
$publisher_id = isset($_GET['publisher_id']) ? $_GET['publisher_id'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
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
     $rediskey = 'legard'.$publisher_id.'__'.$date_from.'__'.$date_to;
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

    $resultnew = $data->getlegarddata($date_from,$date_to,$publisher_id);     
    if(pg_num_rows($resultnew)>0){

        while ($resultss = pg_fetch_array($resultnew)) {;
        $rank = $resultss['rank'];
        $publisher_name = $resultss['publisher_name'];
        $keyword_name = $resultss['keyword_name'];
           $response_code = 0;
           $response_desc = 'successful';
           $jsondata = [
            'legard_keyword_name' => $keyword_name,
            'rank'=>$rank,
            'publisher_name'=>$publisher_name,
        ];
            //$score = strtotime($keywordfirstseendate);
          // $nredis->zAdd($key,$score, json_encode($jsondata));
          $key = 'legard'.$publisher_id.'__'.$date_from.'__'.$date_to;
          $score = $rank;
          $nredis->zAdd($key, $score, json_encode($jsondata));
          $ttlInSeconds = 3600;
          $nredis->expire($key, $ttlInSeconds);
         /* $nredis->hSet($key, $i, json_encode([
              'legard_keyword_name' => $keyword_name,
              'rank'=>$rank,
              'publisher_name'=>$publisher_name,
          ]));
*/
          response($keyword_name,$response_code,$response_desc);
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

function response($keyword_name,$response_code,$response_desc){
    $response['keyword_name'] = $keyword_name;
    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    $json_response = json_encode($response);
    echo $json_response;
}
?>
