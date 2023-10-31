<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
if ($_GET['token_key']=="@123abcd1366") {  
 $jsondata = array();
 $name = $_GET['name'];
 $email = $_GET['email'];
 $image = $_GET['image'];
 $subscriber = $_GET['subscriber'];
 date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$data = new PocModel;
        $datas = [
                'name' =>$name,
                'email' =>$email,
                'image' =>$image,
                'subscriber' =>$subscriber,
               ];
          $result = $data->insertsignupdata($datas);   
		 
    	echo "insert data";
    }
?>
