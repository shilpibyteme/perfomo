<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
include('../database.php');
$jsondata = array();
$category = $_GET['category'];
$userid = $_GET['userid'];
if ($_GET['token_key']=="@123abcd1366" && !empty($category) && !empty($userid)) {

	 $rediskey = $category.'__'.$userid;
	 if($nredis->exists($rediskey)){
	  $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }
    }else{
	$query = "SELECT * FROM dev_performo.user_preferences WHERE category='$category' AND user_id=$userid";
    $result = pg_query($query);    
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