<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   

if ($_GET['token_key']=="@123abcd1366" && $_GET['keywords']!='') {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskeyatr = $_GET['key'];
	$jsondata = array();
	 //$guid = $_GET['guid'];
	 $allarticles = $nredis->get($rediskeyatr);
	if($allarticles){
     echo $allarticles;
    }else{
    $keywords = $_GET['keywords'];
	$sqlq = "SELECT article_id FROM dev_performo.article_keyword_mapping WHERE keyword_name='$keywords'";
    $resultsql = pg_query($sqlq); 
    $rowsql = pg_fetch_array($resultsql);
   $article_id =$rowsql['article_id'];
    $query = "SELECT * FROM dev_performo.article_master WHERE id='$article_id'"; 
    $result = pg_query($query); 
    
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

	response($title,$pubdate,$link,$author,$guid,$mediaurl,$response_code,$response_desc);
    }
    $nredis->set("serach_key", json_encode($jsondata));
    $nredis->flushall();
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
?>
