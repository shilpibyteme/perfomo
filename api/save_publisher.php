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
