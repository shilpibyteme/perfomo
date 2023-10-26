<?php
header("Content-Type:application/json");
include('../database.php');
require './query.php';
require '../RedisMaster.php';
$data = new PocModel;
$category = $_GET['category_id'];
$publisher_id = $_GET['publisher_id'];
if ($_GET['token_key']=="@123abcd1366" && $_GET['keywords']!='' && $category!='' && $publisher_id!='') {
$respnseStartTime = microtime(true);
$keywords = $_GET['keywords'];
$queryExecutionTime = 0;
 $jsondata = array();


$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.',"category_id":'.'"'.$category.'"'.',"keywords":'.'"'.$keywords.'"'.'}]';
    $createdate = date('Y-m-d H:i:s');
    $resultqq=$data->getuseraccordingcat($category);
 if(pg_num_rows($resultqq)>0){
$rowque = pg_fetch_array($resultqq);
$username=$rowque['name'];
$userdata = [
        'log_name' =>$log_name,
        'username' =>$username,
        'createdate' =>$createdate,
    ];
    $sqlquery = $data->insertuserlog($userdata);

	 $rediskeyatr = '{keyword}:'.$keywords.'__'.$publisher_id.'__'.$category;
	 $fromcache = false;
	 $jsondata = array();
	 if($nredis->exists($rediskeyatr)){
	 $allarticlenew = $nredis->sRandMember($rediskeyatr);
	 echo $allarticlenew;
	  $fromcache = true;
    }else{
	  
	   $queryStartTime = microtime(true);
	$resultsql = $data->getsearchkeyword($category,$publisher_id,$keywords); 
     if(pg_num_rows($resultsql)>0){
    $rowsql = pg_fetch_array($resultsql);
    $article_id =$rowsql['article_id'];
    $result = $data->getsearchactricle($article_id);
     if(pg_num_rows($result)>0){
    $queryEndTime = microtime(true);
    $queryExecutionTime = $queryEndTime - $queryStartTime;
	  while ($row = pg_fetch_array($result)) {
											 $id =$rowsql['article_id'];
                        $title = $row['title'];
                        $pubdate = $row['pubdate'];
                        $link = $row['link'];
                        $categoryname = $row['category_name'];
                        $publishername = $row['publisher_name'];
                        $author = $row['author'];
                        $guid = $row['guid'];
                        $summary = $row['summary'];
                        $mediaurl = $row['mediaurl'];
                        $response_code = 0;
                        $response_desc = 'successful';
                        $jsondata = [
                            'id' => $id,
                            'title' => $title,
                            'pubdate' => $pubdate,
                            'link' => $link,
                            'category' => $categoryname,
                            'publisher' => $publishername,
                            'author' => $author,
                            'guid' => $guid,
                            'mediaurl' => $mediaurl
                        ];
      $key ='{keyword}:'.$keywords.'__'.$publisher_id.'__'.$category;
     $score = strtotime($pubdate);
     $nredis->sAdd($key, json_encode($jsondata));
     $ttlInSeconds = 3600;
     $nredis->expire($key, $ttlInSeconds);
	  response($id, $title, $pubdate, $link, $categoryname, $publishername, $author, $guid, $mediaurl, $response_code, $response_desc);

    }
    }else{
   	$emptyArray = array();
    echo json_encode($emptyArray);
    die;
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
}else{
   	$emptyArray = array();
    echo json_encode($emptyArray);
    die;
   }
function response($id, $title, $pubdate, $link, $categoryname, $publishername, $author, $guid, $mediaurl, $response_code, $response_desc)
{
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
//$respnseEndTime = microtime(true);
//$responseExecutionTime = $respnseEndTime - $respnseStartTime;
//echo "Query execution time: " . $queryExecutionTime. " seconds<br>";
//echo "Response execution time: " . $responseExecutionTime. " seconds<br>";
//echo 'Cache'.$fromcache;
?>
