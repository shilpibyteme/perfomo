<?php
header("Content-Type:application/json");
include('../database.php');
require './query.php';
require '../RedisMaster.php';
date_default_timezone_set('Asia/Kolkata');
if ($_GET['token_key'] == "@123abcd1366" && $_GET['publisher_id'] != '' && $_GET['category_id'] != '') {
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.',"category_id":'.'"'.$category_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');

$user = new PocModel;
$resultsqu = $user->getuserdata($publisher_id);
if (pg_num_rows($resultsqu) > 0) {
$rowque = pg_fetch_array($resultsqu);

$username=$rowque['name'];
$userdata = [
        'log_name' =>$log_name,
        'username' =>$username,
        'createdate' =>$createdate,
    ];

   $result = $user->insertuserlog($userdata);
}

$queryExecutionTime = 0;

    $rediskeynew = $publisher_id . '__' . $category_id;
    if ($nredis->exists($rediskeynew)) {
        $allarticlenew = $nredis->zRevRange($rediskeynew, 0, -1);
        if ($allarticlenew) {
            $jsonArray = [];
            foreach ($allarticlenew as $jsonString) {
                $jsonArray[] = json_decode($jsonString, true);
            }
            $jsonResultnew = json_encode($jsonArray);
            echo $jsonResultnew;
        } else {
            $jsonResultnew = [];
            echo $jsonResultnew;
        }
    } else {

        $resultsql = $user->getpublishcategeory($category_id,$publisher_id);

        if (pg_num_rows($resultsql) > 0) {
            $items = array(); // Initialize an empty array

                while ($rownew = pg_fetch_array($resultsql)) {
                    $pub_id = $rownew['id'];
                    $items[] = $pub_id;
                }

                $datapubid = implode(',', $items);
                // Record the start time of the SQL query execution
                $queryStartTime = microtime(true);
               
                $result = $user->getarticlemasterdata($datapubid);

                // Calculate the query execution time
                $queryEndTime = microtime(true);
                $queryExecutionTime = $queryEndTime - $queryStartTime;

                if (pg_num_rows($result) > 0) {
                    while ($row = pg_fetch_array($result)) {
                        $id = $row['articleid'];
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
                        $jsondata = [
                            'id' => $id,
                            'title' => $title,
                            'pubdate' => $pubdate,
                            'link' => $link,
                            'category' => $category,
                            'publisher' => $publisher,
                            'author' => $author,
                            'guid' => $guid,
                            'mediaurl' => $mediaurl
                        ];
                        response($id, $title, $pubdate, $link, $category, $publisher, $author, $guid, $mediaurl, $response_code, $response_desc);
                        $key = $publisher_id . '__' . $category_id;
                        $score = strtotime($pubdate);
                        $nredis->zAdd($key, $score, json_encode($jsondata));
                        $ttlInSeconds = 3600;
                        $nredis->expire($key, $ttlInSeconds);
                    }
                } else {
                    $emptyArray = array();
                    echo json_encode($emptyArray);
                    die;
                }
          
        } else {
            $emptyArray = array();
            echo json_encode($emptyArray);
            die;
        }
    }
} else {
    response(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 400, "Invalid Request");
}

function response($id, $title, $pubdate, $link, $category, $publisher, $author, $guid, $mediaurl, $response_code, $response_desc)
{
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

//echo "Query execution time: " . $queryExecutionTime . " seconds<br>";
?>
