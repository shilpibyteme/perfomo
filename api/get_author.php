<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
date_default_timezone_set('Asia/Kolkata');
$article_id = isset($_GET['article_id']) ? $_GET['article_id'] : '';
if ($_GET['token_key']=="@123abcd1366" && !empty($article_id)) {
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
 $data= new PocModel;
if (!empty($article_id)) {
        // Prepare and execute the first query
        
      
       $resultsqu = $data->getarticleuser($article_id);
       $rowque = pg_fetch_array($resultsqu);
            $username=$rowque['name'];
                $userdata = [
                    'log_name' =>$log_name,
                    'username' =>$username,
                    'createdate' =>$createdate,
                     ];
                $sqlquery = $data->insertuserlog($userdata);
 
}


	include('../database.php');
	 
	$rediskeynew = 'authors__'.$article_id;
	if($nredis->exists($rediskeynew)){
    $allarticlenew = $nredis->zRevRange($rediskeynew, 0, -1);
        foreach ($allarticlenew as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
            echo json_encode($jsonArray); // Output each keyword as a separate JSON object
        }

     }else{
	 $resultartic = $data->getarticle($article_id);
    if (pg_num_rows($resultartic) > 0) { 
	while ($rowkey = pg_fetch_array($resultartic)) {
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
