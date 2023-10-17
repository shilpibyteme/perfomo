<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   


if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 require '../RedisMaster.php';
	 $rediskeyuser = $_GET['key'];
	 $publisher_id = $_GET['publisher_id'];
	 $article_id=$_GET['article_id'];
	 $category_id=$_GET['category_id'];
	 $allusers = $nredis->get($rediskeyuser);
	if($allusers){
      echo $allusers;
     }else{
	 $query = "SELECT id, title, pubdate, link, pub_category_id, author, guid, summary, mediaurl
	FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON CAST(dev_performo.publisher_category_mapping.id AS integer)=dev_performo.article_master.pub_category_id WHERE pubdate > (NOW() - INTERVAL '1 hours' ) AND id=$article_id AND publisher_id='$publisher_id' ORDER BY pubdate DESC"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($row = pg_fetch_array($result)) {
	$article_id=$row['id'];
	$link=$row['link'];	
	$keywordname = get_meta_tags($link);
    $keywordfirstseendate = $row['pubdate'];
    $keywordlastseendate = NULL;

    $sqlnew = SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE keyword_name ='$keywordname'; 
    $resultnew = pg_query($query);
    $rownew = pg_fetch_row($resultnew);
    $keyword_name = $rownew['keyword_name'];
    if($rownew['keyword_name'] == $keywordname){
    $query1 = "UPDATE dev_performo.article_keyword_mapping SET article_id=$article_id, keyword_name='$keywordname', keywordfirstseendate =$keywordfirstseendate, keywordlastseendate=NULL WHERE keyword_name=''";
    $result = pg_query($conn, $query1);
    }else{
    $keywordlastseendate = date('Y-m-d H:i:s');
   $query2 = "UPDATE dev_performo.article_keyword_mapping SET article_id=$article_id, keyword_name='$keywordname', keywordfirstseendate =$keywordfirstseendate, keywordlastseendate=NULL WHERE keyword_name='$keyword_name'";
    $result = pg_query($conn, $query2);
    }
	$response_code = 0;
	$response_desc = 'successful';
	response($response_code,$response_desc);
    }

 }
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($response_desc){
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
