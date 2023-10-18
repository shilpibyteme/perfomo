<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
$jsondata = array();
 $article_id = $_GET['article_id'];
if ($_GET['token_key']=="@123abcd1366" && !empty($article_id)) {
	include('../database.php');
	 
	$rediskeynew = 'authors__'.$article_id;
	if($nredis->exists($rediskeynew)){
    $allarticlenew = $nredis->zRevRange($rediskeynew, 0, -1);
        foreach ($allarticlenew as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }

     }else{
    
	  $query = "SELECT * FROM dev_performo.article_master WHERE dev_performo.article_master.id='$article_id'";  
    $result = pg_query($query); 
    
	while ($rowkey = pg_fetch_array($result)) {
	$author = $rowkey['author'];
	$pubdate = $rowkey['pubdate'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'author' => $author,
    ];
     $key ='authors__'.$article_id;
     $score = strtotime($pubdate);
     $nredis->zAdd($key,$score, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	  response($author,$response_code,$response_desc);
    }

  }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($author,$response_code,$response_desc){
	$response['author'] = $author;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
