<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
 $jsondata = array();
 $userid = $_GET['userid'];
 $category = $_GET['category'];
 $source = $_GET['source'];
if ($_GET['token_key']=="@123abcd1366" && $_GET['userid']!=''&& $_GET['category']!='') {
	include('../database.php');
	 require '../RedisMaster.php';
	 foreach($source as $val){
		$sqlq = "INSERT INTO dev_performo.user_preferences (category, user_id, publisher_name)
         VALUES ($category,$userid, $val);";
    	$resultsql = pg_query($sqlq); 
	 }
}

?>
