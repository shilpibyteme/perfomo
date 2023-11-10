<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';

$data = new PocModel;
date_default_timezone_set('Asia/Kolkata');
$headers = getallheaders();

if (!array_key_exists('Authorization', $headers)) {
    echo json_encode(["error" => "Authorization header is missing"]);
    exit;
} else {
    if ($headers['Authorization'] !== 'Bearer 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        echo json_encode(["error" => "Token keyword is missing"]);
        exit;
    } else {
        $queryStartTime = microtime(true);
        $date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : '';
        $dateto = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : '';
        $dateToObj = DateTime::createFromFormat('Y-m-d', $dateto);
        $dateToObj->add(new DateInterval('P1D'));
        $date_to = $dateToObj->format('Y-m-d');
        $publisher_id = isset($_REQUEST['publisher_id']) ? $_REQUEST['publisher_id'] : '';
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
        $log_name = '[{"date_from":' . '"' . $date_from . '"' . ',"date_to":' . '"' . $date_to . '"' . ',"category_id":' . '"' . $category_id . '"' . ',"publisher_id":' . '"' . $publisher_id . '"' . '}]';
        $createdate = date('Y-m-d H:i:s');

        if (!empty($publisher_id)) {
            $resultsqu = $data->getuserdata($publisher_id);
            if (pg_num_rows($resultsqu) > 0) {
                $rowque = pg_fetch_array($resultsqu);
                $username = $rowque['name'];
                $userdata = [
                    'log_name' => $log_name,
                    'username' => $username,
                    'createdate' => $createdate,
                ];
                $sqlquery = $data->insertuserlog($userdata);
            }
        }

        $jsondata = array();
        $rediskey = 'leggard__'.$category_id.'__'.$publisher_id.'__'.$date_from.'__'.$date_to;
        if ($nredis->exists($rediskey)) {
            $allarticlenew = $nredis->zRevRange($rediskey, 0, -1);
            if ($allarticlenew) {
                $jsonArray = [];
                foreach ($allarticlenew as $jsonString) {
                    $jsonArray[] = json_decode($jsonString, true);
                }
                $jsondata = $jsonArray;
            }
        } else {
            $queryStartTime = microtime(true);
             $resultnew = $data->getlegarddata($date_from,$date_to,$category_id,$publisher_id); 

            if (pg_num_rows($resultnew) > 0) {
                while ($resultkey = pg_fetch_array($resultnew)) {
                    $rank = $resultkey['rank'];
					$publisher_name = $resultkey['publisher_name'];
                    if ($rank == 1) {
                        $keyword_name = $resultkey['keyword_name'];

                        $response_code = 0;
                        $response_desc = 'successful';

                        $jsondata[] = [
							'legard_keyword_name' => $keyword_name,
							'rank'=>$rank,
							'publisher_name'=>$publisher_name,
						];
                    }
					 //$score = strtotime($keywordfirstseendate);
					  //$key = 'leggard__'.$category_id.'__'.$publisher_id.'__'.$date_from.'__'.$date_to;
					 // $score = $rank;
					  //$nredis->zAdd($key, $score, json_encode($jsondata));
					  //$ttlInSeconds = 3600;
					  //$nredis->expire($key, $ttlInSeconds);
                }
            }

            // Calculate the query execution time
            $queryEndTime = microtime(true);
            $queryExecutionTime = $queryEndTime - $queryStartTime;
        }

        response($jsondata);
    }
}

function response($jsondata) {
    echo json_encode($jsondata);
}
?>
