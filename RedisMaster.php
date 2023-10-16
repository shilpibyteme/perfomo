<?php 
   global $nredis;
   require '../vendor/autoload.php';
   
   $nredis = new Predis\client();
   //echo $redis->ping();
   $nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360); 
   //echo "Connection to server sucessfully"; 
   //set the data in redis string 
   //$redis->set("tutorial-name", "Redis tutorial"); 
   // Get the stored data and print it 
   //echo "Stored string in redis:: " .$redis->get("tutorial-name"); 
?>