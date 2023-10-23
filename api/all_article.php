<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
include('../database.php');
$nredis = new Predis\Client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);
date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$page_number = $_GET['page_number'];
$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.',"category_id":'.'"'.$category_id.'"'.',"page_number":'.'"'.$page_number.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$sqldataque = "SELECT name FROM dev_performo.puser WHERE publisher_id='$publisher_id'";
$resultsqu = pg_query($sqldataque);
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];

$sqlquery ="INSERT INTO dev_performo.userlog (log_name,username, created) VALUES ('$log_name','$username','$createdate')";
$resultsql = pg_query($sqlquery);

$queryExecutionTime = 0;
if ($_GET['token_key'] == "@123abcd1366" && $_GET['publisher_id'] != '' && $_GET['category_id'] != '') {
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
        $sqlq = "SELECT id FROM dev_performo.publisher_category_mapping WHERE category_id='$category_id' AND publisher_id='$publisher_id'";
        $resultsql = pg_query($sqlq);
        if (pg_num_rows($resultsql) > 0) {
            while ($rownew = pg_fetch_array($resultsql)) {
                $pub_id = $rownew['id'];
                

                // Record the start time of the SQL query execution
                $queryStartTime = microtime(true);

                $query = "SELECT dev_performo.article_master.*,dev_performo.publisher_category_mapping.*,dev_performo.article_master.id as articleid FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id WHERE pub_category_id=$pub_id ORDER BY pubdate DESC LIMIT $page_number";

                $result = pg_query($query);

                // Calculate the query execution time
                $queryEndTime = microtime(true);
                $queryExecutionTime = $queryEndTime - $queryStartTime;

                // Output the execution time (for testing purposes)
                

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
