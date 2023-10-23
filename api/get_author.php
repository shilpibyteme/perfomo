<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);  

date_default_timezone_set('Asia/Kolkata');
$article_id = isset($_GET['article_id']) ? $_GET['article_id'] : '';
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');

if (!empty($article_id)) {
        // Prepare and execute the first query
        $sqldataque = "SELECT name FROM dev_performo.puser JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.publisher_id=dev_performo.puser.publisher_id JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE dev_performo.article_master.id=$1";
        $resultsqu = pg_query_params($db, $sqldataque, array($article_id));
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
    if (pg_num_rows($result) > 0) { 
	while ($rowkey = pg_fetch_array($result)) {

	$author = $rowkey['author'];
	if($author!=NULL){
	$pubdate = $rowkey['pubdate'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'author' => $author,
    ];
     }else{
     	$emptyArray = array();
            echo json_encode($emptyArray);
            die;
     }
     $key ='authors__'.$article_id;
     $score = strtotime($pubdate);
     $nredis->zAdd($key,$score, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	  response($author,$response_code,$response_desc);
    }

   } else {
            $emptyArray = array();
            echo json_encode($emptyArray);
            die;
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
