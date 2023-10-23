<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);  
date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.',"category_id":'.'"'.$category_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$sqldataque = "SELECT name FROM dev_performo.puser WHERE publisher_id='$publisher_id'";
$resultsqu = pg_query($sqldataque);
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];

$sqlquery ="INSERT INTO dev_performo.userlog (log_name,username, created) VALUES ('$log_name','$username','$createdate')";
$resultsql = pg_query($sqlquery);

if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 require '../RedisMaster.php';
	 $query = "SELECT dev_performo.article_master.id as articleid,dev_performo.article_master.link as link FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON CAST(dev_performo.publisher_category_mapping.id AS integer) = dev_performo.article_master.pub_category_id WHERE category_id = '$category_id' AND publisher_id = '$publisher_id' AND pubdate > NOW() - INTERVAL '5 HOURS'ORDER BY pubdate DESC"; 

      $result = pg_query($query); 
      $jsondata = array();
	 while ($row = pg_fetch_array($result)) {
       $article_id=$row['articleid'];
	   $sqldata = "SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'"; 
       $resultnew = pg_query($sqldata);
       if(pg_num_rows($resultnew)>0){
       $rownew = pg_fetch_array($resultnew);
       $keyword_name = $rownew['keyword_name'];
         $link= $row['link'];
	         $keyword = get_meta_tags($link);
	        $keywordname = explode(',', $keyword['keywords']);
	     	//first check for new element latest vs exitsing keywords->array_diff
	   	   
	   	     $sqldatass = "SELECT status FROM dev_performo.article_keyword_mapping WHERE keyword_name='$keywordname'"; 
           $resultnewss = pg_query($sqldatass);
           $rownewss = pg_fetch_array($resultnewss);
           $datnew = array_diff($keywordname, $rownew);
	        if($rownewss['status']=='0'){ 
			     foreach($datnew as $vkey){
			    $keywordfirstseendate = date('Y-m-d H:i:s');
			   	 $keywordlastseendate = 'NULL';
			     $flag=1;
			     $query2 ="INSERT INTO dev_performo.article_keyword_mapping (article_id, keyword_name, keywordfirstseendate,keywordlastseendate,status)
			       VALUES ('$article_id', '$vkey','$keywordfirstseendate',$keywordlastseendate,$flag)"; 
			    $result = pg_query($db, $query2);
		    	}
			    echo 'inserted';
			    
			    }
	    }else{
	       $link= $row['link'];
	       $keyword = get_meta_tags($link);
	       $keywordname = explode(',', $keyword['keywords']);
           foreach($keywordname as $vkey){
	       $keywordfirstseendate = date('Y-m-d H:i:s');
		   	 $keywordlastseendate = 'NULL';
		     $flag=1;
		     $query2 ="INSERT INTO dev_performo.article_keyword_mapping (article_id, keyword_name, keywordfirstseendate,keywordlastseendate,status)
		       VALUES ('$article_id', '$vkey','$keywordfirstseendate',$keywordlastseendate,$flag)"; 
		    $result = pg_query($db, $query2);
		   }
	    }
	    
	    // existing kewords vs latest keywords
	   $queries = "SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'"; 
        $resdata = pg_query($queries);
       $keywordNames = array(); // Initialize an array to store the results
	    while ($rownews = pg_fetch_array($resdata)) {
	        $keywordNames[] = $rownews['keyword_name'];
	    }
	    $keyword = 'aditya chopra, bollywood, salman khan, tiger 3, yash raj films';
	    $keywordname = explode(',', $keyword);
	    $datnewold = array_diff($keywordNames,$keywordname);
         if($datnewold){ 
         foreach($datnewold as $vkeyval){	
	     $flag=0;	
         $keywordlastseendate = date('Y-m-d H:i:s');
         $query1 = "UPDATE dev_performo.article_keyword_mapping SET keywordlastseendate='$keywordlastseendate',status='$flag' WHERE keyword_name='$vkeyval'";
	    $result = pg_query($db, $query1);
	    echo 'updated';

	     }
	    }
      }
     
    }	   
?>
