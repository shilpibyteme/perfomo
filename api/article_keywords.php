<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
$jsondata = array();
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 $rediskeyuser = $_GET['key'];
	 $article_id = $_GET['article_id'];
	 $allkeyword = $nredis->get($rediskeyuser);
	if($allkeyword){
      echo $allkeyword;
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
	response($keyword_name,$keywordfirstseendate,$keywordlastseendate,$response_code,$response_desc);
    }
    //print_r($jsondata);
    $nredis->set("articlekeywords", json_encode($jsondata));
    //$nredis->flushall();
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
