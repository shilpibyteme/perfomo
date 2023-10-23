<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
 $jsondata = array();
 $userid = $_GET['userid'];
 $category = $_GET['category'];
 $sources = $_GET['sources'];
 $newsource = trim($sources);
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$log_name = '[{"userid":'.'"'.$userid.'"'.',"category":'.'"'.$category.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$sqldataque = "SELECT name FROM dev_performo.puser WHERE id='$userid'";
$resultsqu = pg_query($sqldataque);
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];

$sqlquery ="INSERT INTO dev_performo.userlog (log_name,username, created) VALUES ('$log_name','$username','$createdate')";
$resultsql = pg_query($sqlquery);
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
   $sqlq = "SELECT category,user_id FROM dev_performo.user_preferences WHERE category='$category' AND user_id='$userid'";
    $resultsql = pg_query($sqlq); 
    $rowsql = pg_fetch_array($resultsql);
    $catid=$rowsql['category'];
    $useriddata=$rowsql['user_id'];
    if($rowsql['category']==$category && $rowsql['user_id']==$userid){
    $sqlq = "UPDATE dev_performo.user_preferences SET publisher_name = '$newsource' WHERE category='$catid' AND user_id=$useriddata";
    	$resultsql = pg_query($sqlq); 
    	echo "updated data";
    }else{
		echo $sqlq = "INSERT INTO dev_performo.user_preferences (category, user_id, publisher_name)
         VALUES ('$category',$userid, '$newsource')";
    	$resultsql = pg_query($sqlq); 
    	echo "insert data";
    }

	

}

?>
