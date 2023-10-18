<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
$jsondata = array();
$article_id = $_GET['article_id'];
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskey = 'keyword__'.$article_id;
     if($nredis->exists($rediskey)){
	 $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }

     }else{
	 $query = "SELECT * FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'";
    $result = pg_query($query); 
    
	while ($rowkey = pg_fetch_array($result)) {
	$keyword_name = $rowkey['keyword_name'];
	$keywordfirstseendate = $rowkey['keywordfirstseendate'];
	$keywordlastseendate = $rowkey['keywordlastseendate'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'keyword_name' => $keyword_name,
        'keywordfirstseendate' => $keywordfirstseendate,
        'keywordlastseendate'=>$keywordlastseendate,
    ];
      $key ='keyword__'.$article_id;
      $score = strtotime($keywordfirstseendate);
     $nredis->zAdd($key,$score, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	response($keyword_name,$keywordfirstseendate,$keywordlastseendate,$response_code,$response_desc);
    }

 }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($keyword_name,$keywordfirstseendate,$keywordlastseendate,$response_code,$response_desc){
	$response['keyword_name'] = $keyword_name;
	$response['keywordfirstseendate'] = $keywordfirstseendate;
	$response['keywordlastseendate'] = $keywordlastseendate;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
