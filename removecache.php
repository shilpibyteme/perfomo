 <?php 
 global $nredis;
   require '../vendor/autoload.php';
   
   $nredis = new Predis\client();
   //echo $redis->ping();
   $nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);
    $this->_redis->flushAll();
