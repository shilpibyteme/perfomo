<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
date_default_timezone_set('Asia/Kolkata');
$category = $_GET['category'];
$userid = $_GET['userid'];
if ($_GET['token_key']=="@123abcd1366" && !empty($category) && !empty($userid)) {
$log_name =  '[{"category":'.'"'.$category.'"'.',"userid":'.'"'.$userid.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
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
	 $rediskey = $category.'__'.$userid;
	 if($nredis->exists($rediskey)){
	  $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
    }else{
	$result = $data->getpreferencedata($category,$userid);
    if(pg_num_rows($result)>0){
	while ($row = pg_fetch_array($result)) {
	$category = $row['category'];
	$user_id = $row['user_id'];
	$publisher_name =  $row['publisher_name'];
	$response_code = 0;
	$response_desc = 'successful';
	  $jsondata[] = [
        'category' => $category,
        'user_id' => $user_id,
        'publisher_name' => $publisher_name,
      ];
      $key =$category.'__'.$userid;

      $score = rand(10,1000000);
     $nredis->zAdd($key,$score, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
     response($category,$user_id,$publisher_name,$response_code,$response_desc);
    }
   }else{
       $emptyArray = array();
       echo json_encode($emptyArray);
       die;
    }
 }

}else{
	response(NULL, NULL,NULL, 400,"Invalid Request");
	}

function response($category,$user_id,$publisher_name,$response_code,$response_desc){
	$response['category'] = $category;
	$response['user_id'] = $user_id;
	$response['publisher_name'] = $publisher_name;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>