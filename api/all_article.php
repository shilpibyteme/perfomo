<?php
header("Content-Type:application/json");
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   

 $publisher_id = $_GET['publisher_id'];
 $category_id = $_GET['category_id'];
if ($_GET['token_key']=="@123abcd1366" && $_GET['publisher_id']!=''&& $_GET['category_id']!='') {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskeynew = $_GET['key'];
	 $allarticledatas= $nredis->get($rediskeynew);
	if($allarticledatas){
     echo $allarticledatas;
    }else{
	 $sqlq = "SELECT id FROM dev_performo.publisher_category_mapping WHERE category_id='$category_id' AND publisher_id='$publisher_id'";
    $resultsql = pg_query($sqlq); 
    $rowsql = pg_fetch_array($resultsql);
    $pub_id =$rowsql['id']; 
    $page_number = $_GET['page_number'];
	  $query = "SELECT * FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id
	 WHERE pub_category_id=$pub_id ORDER BY pubdate DESC LIMIT $page_number"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$title = $row['title'];
	$pubdate = $row['pubdate'];
	$link = $row['link'];
	$category = $row['category_name'];
	$publisher = $row['publisher_name'];
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
        'category' => $category,
        'publisher' => $publisher,
        'author' => $author,
        'guid' => $guid,
        'mediaurl' => $mediaurl
    ];
	response($title,$pubdate,$link,$category,$publisher,$author,$guid,$mediaurl,$response_code,$response_desc);
    }  
    $key="articledata";
    $nredis->set($key,json_encode($jsondata));
  
 }
  
}else{
	response(NULL, NULL,NULL, NULL,NULL, NULL, NULL, 400,"Invalid Request");
	}

function response($title,$pubdate,$link,$category,$publisher,$author,$guid,$mediaurl,$response_code,$response_desc){
	$response['title'] = $title;
	$response['pubdate'] = $pubdate;
	$response['link'] = $link;
	$response['category'] = $category;
	$response['publisher'] = $publisher;
	$response['author'] = $author;
	$response['guid'] = $guid;
	$response['mediaurl'] = $mediaurl;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
