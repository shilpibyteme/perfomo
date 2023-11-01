<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
$headers = getallheaders();
if (!array_key_exists('Authorization', $headers)) {

    echo json_encode(["error" => "Authorization header is missing"]);
    exit;
}
else {

    if ($headers['Authorization'] !== 'Bearer 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {

        echo json_encode(["error" => "Token keyword is missing"]);
        exit;
    }else{
 $jsondata = array();
 $userid = $_REQUEST['userid'];
 $category = $_REQUEST['category'];
 $sources = $_REQUEST['sources'];
 $newsource = trim($sources);
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$data = new PocModel;
$log_name = '[{"userid":'.'"'.$userid.'"'.',"category":'.'"'.$category.'"'.'}]';
$createdate = date('Y-m-d H:i:s');

       $resultsqu = $data->getuserdataById($userid);
	   if(pg_num_rows($resultsqu)>0){
       $rowque = pg_fetch_array($resultsqu);
            $username=$rowque['name'];
                $userdata = [
                    'log_name' =>$log_name,
                    'username' =>$username,
                    'createdate' =>$createdate,
                     ];
                $sqlquery = $data->insertuserlog($userdata);
	   }

    $resultsql = $data->getuserprefernence($category,$userid);
    $rowsqls = pg_fetch_array($resultsql);
    $catid=$rowsqls['category'];
    $useriddata=$rowsqls['user_id'];
    if($rowsqls['category']==$category && $rowsqls['user_id']==$userid){
    $uppredata = [
                'newsource' =>$newsource,
                'catid' =>$catid,
               ];

         $result = $data->updateprefernce($useriddata,$uppredata);
       	  echo "data updated!!";
          $rediskey = $category.'__'.$userid;    
         $allKeys = $nredis->del($rediskey);  
       }else{
        $datas = [
                'category' =>$category,
                'userid' =>$userid,
                'newsource' =>$newsource,
               ];
          $result = $data->insertpreference($datas);   
		 
    	echo "insert data";
    }
  }
}
?>
