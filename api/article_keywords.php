<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
 $data= new PocModel;
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key']=="@123abcd1366") {
 

$article_id = isset($_GET['article_id']) ? $_GET['article_id'] : '';
$log_name =  '[{"article_id":'.'"'.$article_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');

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
$jsondata = array();
	 $rediskey = 'metadata__'.$article_id;
     if($nredis->exists($rediskey)){
     $allarticlekey = $nredis->hGetAll($rediskey);
	// $allarticlekey = $nredis->zRevRange($rediskey, 0, -1);
        foreach ($allarticlekey as $jsonString) {
            $jsonArray = json_decode($jsonString, true);
    		$jsonArrayOutput[] = $jsonArray; 
        }
        echo json_encode($jsonArrayOutput);

     }else{
    $resultart = $data->getkeyword($article_id);
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
