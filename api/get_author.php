<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   


if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskeyauth = $_GET['key'];
	 $article_id = $_GET['article_id'];
	 $allkeyauth = $nredis->get($rediskeyauth);
	//if($allkeyauth){
    //  echo $allkeyauth;
    // }else{
	 $query = "SELECT * FROM dev_performo.article_master WHERE id='$article_id'"; 
    $result = pg_query($query); 
    $jsondata = array();
	while ($rowkey = pg_fetch_array($result)) {
	$author = $rowkey['author'];
	$response_code = 0;
	$response_desc = 'successful';
	$jsondata[] = [
        'author' => $author,
    ];
	response($author,$response_code,$response_desc);
    }
    //print_r($jsondata);
    $nredis->set("articleauthor", json_encode($jsondata));
    $nredis->flushall();
 //}
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
