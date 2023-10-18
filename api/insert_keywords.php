<?php
header("Content-Type:application/json");
require '../vendor/autoload.php';
$nredis = new Predis\client();
$nredis->connect('redis-11360.c264.ap-south-1-1.ec2.cloud.redislabs.com', 11360);   
if ($_GET['token_key']=="@123abcd1366") {
	include('../database.php');
	 require '../RedisMaster.php';

	 $publisher_id = $_GET['publisher_id'];
	 $category_id=$_GET['category_id'];
	 $query = "SELECT dev_performo.article_master.id as articleid FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON CAST(dev_performo.publisher_category_mapping.id AS integer) = dev_performo.article_master.pub_category_id WHERE category_id = '$category_id' AND publisher_id = '$publisher_id' AND pubdate > NOW() - INTERVAL '5 HOURS'ORDER BY pubdate DESC"; 

      $result = pg_query($query); 
      $jsondata = array();
	 while ($row = pg_fetch_array($result)) {
       $article_id=$row['articleid'];
	   $sqldata = "SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'"; 
       $resultnew = pg_query($sqldata);
       if(pg_num_rows($resultnew)>0){
       $rownew = pg_fetch_array($resultnew);
       $keyword_name = $rownew['keyword_name'];
      // foreach($keyword_name as $valkey){
         $link= $row['link'];
	         $keyword = get_meta_tags($link);
	        $keywordname = explode(',', $keyword['keywords']);
	     	//first check for new element latest vs exitsing keywords->array_diff
	   	    $datnew = array_diff($keywordname, $rownew);
	        if($datnew){ 
	        /*$keywordfirstseendate = date('Y-m-d H:i:s');
	         $query1 = "UPDATE dev_performo.article_keyword_mapping SET keywordfirstseendate='$keywordfirstseendate' WHERE article_id='$article_id'";
		    $result = pg_query($db, $query1);
		    echo 'updated';
		    */
		    }else{
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
	  
	    // existing kewords vs latest keywords
	   $queries = "SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'"; 
       $resdata = pg_query($queries);
      // $rownews = pg_fetch_array($resdata);
       $keywordNames = array(); // Initialize an array to store the results
	    while ($rownews = pg_fetch_array($resdata)) {
	        $keywordNames[] = $rownews['keyword_name'];
	    }
	    foreach ($keywordNames as $keyword) {
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
	    }else{
	      foreach($keywordname as $vkey){
	     $keywordfirstseendate = date('Y-m-d H:i:s');
	   	 $keywordlastseendate = 'NULL';
	     $flag=1;
	     $query2 ="INSERT INTO dev_performo.article_keyword_mapping (article_id, keyword_name, keywordfirstseendate,keywordlastseendate,status)
	       VALUES ('$article_id', '$vkey','$keywordfirstseendate',$keywordlastseendate,$flag)"; 
	    $result = pg_query($db, $query2);
    	}
	    echo 'inserted';
	    }
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
	    
      }
     
    }	   
?>
