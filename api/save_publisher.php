<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
if ($_GET['token_key']=="@123abcd1366") {  
 $jsondata = array();
 $userid = $_GET['userid'];
 $category = $_GET['category'];
 $sources = $_GET['sources'];
 $newsource = trim($sources);
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$data = new PocModel;
$log_name = '[{"userid":'.'"'.$userid.'"'.',"category":'.'"'.$category.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
if (!empty($article_id)) {
        // Prepare and execute the first query
        
      
       $resultsqu = $data->getuserdataById($userid);
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

?>
