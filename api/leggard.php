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
$dateto = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : '';
$dateToObj = DateTime::createFromFormat('Y-m-d', $dateto);
$dateToObj->add(new DateInterval('P1D'));
$date_to = $dateToObj->format('Y-m-d');
$publisher_id = isset($_REQUEST['publisher_id']) ? $_REQUEST['publisher_id'] : '';
$category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
$log_name = '[{"date_from":'.'"'.$date_from.'"'.',"date_to":'.'"'.$date_to.'"'.',"category_id":'.'"'.$category_id.'"'.',"publisher_id":'.'"'.$publisher_id.'"'.'}]';
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
     $rediskey = 'legard'.$category_id.'__'.$publisher_id.'__'.$date_from.'__'.$date_to;
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

    $resultnew = $data->getlegarddata($date_from,$date_to,$category_id,$publisher_id);     
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
          $key = 'legard'.$category_id.'__'.$publisher_id.'__'.$date_from.'__'.$date_to;
         // $score = $rank;
          //$nredis->zAdd($key, $score, json_encode($jsondata));
          //$ttlInSeconds = 3600;
          //$nredis->expire($key, $ttlInSeconds);
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
