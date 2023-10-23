<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
include('../database.php');
require '../RedisMaster.php';

date_default_timezone_set('Asia/Kolkata');
$article_id = isset($_GET['article_id']) ? $_GET['article_id'] : '';
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');

if (!empty($article_id)) {
        // Prepare and execute the first query
        $sqldataque = "SELECT name FROM dev_performo.puser JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.publisher_id=dev_performo.puser.publisher_id JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE dev_performo.article_master.id=$article_id";
        $resultsqu = pg_query_params($db, $sqldataqarticle_idue, array($article_id));
        if ($resultsqu) {
            $rowque = pg_fetch_array($resultsqu);
            if ($rowque) {
                $username = $rowque['name'];
                // Prepare and execute the second query
                $sqlquery = "INSERT INTO dev_performo.userlog (log_name, username, created) VALUES ($1, $2, $3)";
                $resultsql = pg_query_params($db, $sqlquery, array($log_name, $username, $createdate));
            }
        }
   
}
$jsondata = array();

if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskey = 'metadata__'.$article_id;
     if($nredis->exists($rediskey)){
     $allarticlekey = $nredis->hGetAll($rediskey);
	// $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
           $jsonArray = json_decode($jsonString, true);
    		$jsonArrayOutput[] = $jsonArray; 
        }
        echo json_encode($jsonArrayOutput);

     }else{
	 $querart = "SELECT * FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'";
    $resultart = pg_query($querart); 
  if(pg_num_rows($resultart)>0){
	 while ($resultkey = pg_fetch_array($resultart)) {
	 $keyword_name = $resultkey['keyword_name'];
	 $keywordfirstseendate = $resultkey['keywordfirstseendate'];
	 $keywordlastseendate = $resultkey['keywordlastseendate'];
	 $response_code = 0;
	 $response_desc = 'successful';
      //$score = strtotime($keywordfirstseendate);
    // $nredis->zAdd($key,$score, json_encode($jsondata));
    $key = 'metadata__' . $article_id;
	$nredis->hSet($key, $keyword_name, json_encode([
	    'keyword_name' => $keyword_name,
	    'keywordfirstseendate' => $keywordfirstseendate,
	    'keywordlastseendate' => $keywordlastseendate,
	]));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	//response($keyword_name,$keywordfirstseendate,$keywordlastseendate,$response_code,$response_desc);
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
