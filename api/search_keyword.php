<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);  
$respnseStartTime = microtime(true);
$keywords = $_GET['keywords'];
$queryExecutionTime = 0;
 $jsondata = array();
 $userid = $_GET['userid'];
 $category_id = $_GET['category_id'];
 $sources = $_GET['sources'];
 $newsource = trim($sources);
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$log_name = '[{"keywords":'.'"'.$keywords.'"'.',"category_id":'.'"'.$category_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$sqldataque = "SELECT name FROM dev_performo.puser JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.publisher_id=dev_performo.puser.publisher_id JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE dev_performo.publisher_category_mapping.category_id=$category";
$resultsqu = pg_query($sqldataque);
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];

$sqlquery ="INSERT INTO dev_performo.userlog (log_name,username, created) VALUES ('$log_name','$username','$createdate')";
if ($_GET['token_key']=="@123abcd1366" && $_GET['keywords']!='' && $_GET['category_id']!='$category_id') {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskeyatr = '{keyword}:'.$keywords;
	 $fromcache = false;
	 $jsondata = array();
	 if($nredis->exists($rediskeyatr)){
	 $allarticlenew = $nredis->sRandMember($rediskeyatr);
	 echo $allarticlenew;
	  $fromcache = true;
    }else{
    
    $sources = $_GET['sources'];
	 $newsource = trim($sources);
	  $category = $_GET['category_id'];
	   $queryStartTime = microtime(true);
	   $sqlq = "SELECT article_id FROM dev_performo.article_keyword_mapping JOIN dev_performo.article_master ON dev_performo.article_keyword_mapping.article_id=dev_performo.article_master.id JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE  dev_performo.publisher_category_mapping.category_id ='$category' AND dev_performo.publisher_category_mapping.publisher_name IN('$newsource') AND dev_performo.article_keyword_mapping.keyword_name LIKE '%$keywords%'";
    $resultsql = pg_query($sqlq); 
     if(pg_num_rows($resultsql)>0){
    $rowsql = pg_fetch_array($resultsql);
    $article_id =$rowsql['article_id'];
    $query = "SELECT * FROM dev_performo.article_master WHERE id='$article_id'"; 
    $result = pg_query($query); 
    $queryEndTime = microtime(true);
    $queryExecutionTime = $queryEndTime - $queryStartTime;
	while ($row = pg_fetch_array($result)) {
	$title = $row['title'];
	$pubdate = $row['pubdate'];
	$link = $row['link'];
	$author = $row['author'];
	$guid = $row['guid'];
	$summary = $row['summary'];
	$mediaurl = $row['mediaurl'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'title' => $title,
        'pubdate' => $pubdate,
        'link' => $link,
        'author' => $author,
        'guid' => $guid,
        'mediaurl' => $mediaurl
    ];
      $key ='{keyword}:'.$keywords;
     $score = strtotime($pubdate);
     $nredis->sAdd($key, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	   response($title,$pubdate,$link,$author,$guid,$mediaurl,$response_code,$response_desc);
    }
   }else{
   	$emptyArray = array();
    echo json_encode($emptyArray);
    die;
   }
 }

}else{
	response(NULL, NULL,NULL, NULL,NULL, NULL, NULL, 400,"Invalid Request");
	}

function response($title,$pubdate,$link,$author,$guid,$mediaurl,$response_code,$response_desc){
	$response['title'] = $title;
	$response['pubdate'] = $pubdate;
	$response['link'] = $link;
	$response['author'] = $author;
	$response['guid'] = $guid;
	$response['mediaurl'] = $mediaurl;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
$respnseEndTime = microtime(true);
$responseExecutionTime = $respnseEndTime - $respnseStartTime;
echo "Query execution time: " . $queryExecutionTime. " seconds<br>";
echo "Response execution time: " . $responseExecutionTime. " seconds<br>";
echo 'Cache'.$fromcache;
?>
