<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
 $jsondata = array();
 $userid = $_GET['userid'];
 $category = $_GET['category'];
 $sources = $_GET['sources'];
 //$newsource = explode(',', $sources);
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');

	// foreach($newsource as $val){
	$sqlq = "INSERT INTO dev_performo.user_preferences (category, user_id, publisher_name)
         VALUES ('$category',$userid, '$sources');";
    	$resultsql = pg_query($sqlq); 
	// }
}

?>
