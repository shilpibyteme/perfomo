<?php
header("Content-Type:application/json");
include('../database.php');
require '../RedisMaster.php';
require './query.php';
$data = new PocModel;
if ($_GET['token_key']=="@123abcd1366") {
date_default_timezone_set('Asia/Kolkata');
$jsondata = array();
$publisher_id = $_GET['publisher_id'];
$category_id = $_GET['category_id'];
$log_name = '[{"publisher_id":'.'"'.$publisher_id.'"'.',"category_id":'.'"'.$category_id.'"'.'}]';
$createdate = date('Y-m-d H:i:s');
$data = new PocModel;
$resultsqu = $data->getuserdata($publisher_id);
if (pg_num_rows($resultsqu) > 0) {
$rowque = pg_fetch_array($resultsqu);
$username=$rowque['name'];

$userdata = [
        'log_name' =>$log_name,
        'username' =>$username,
        'createdate' =>$createdate,
    ];
     $result = $data->insertuserlog($userdata);
}
     $jsondata = array();
	   $resulthours = $data->getarticlehours($category_id,$publisher_id);
     if (pg_num_rows($resulthours) > 0) {
	    while ($row = pg_fetch_array($resulthours)) {
       $article_id=$row['articleid'];

	    $resultnew = $data->getarticlemapping($article_id);
       if(pg_num_rows($resultnew)>0){
       $rownew = pg_fetch_array($resultnew);
       $keyword_name = $rownew['keyword_name'];
         $link= $row['link'];
	         $keyword = get_meta_tags($link);
	        $keywordname = explode(',', $keyword['keywords']);
	     	//first check for new element latest vs exitsing keywords->array_diff
	   	   
	   	     $resultnewss = $data->keyworkcheckstatus($keywordname);
           $rownewss = pg_fetch_array($resultnewss);
           $datnew = array_diff($keywordname, $rownew);
	        if($rownewss['status']=='0'){ 
			     foreach($datnew as $vkey){
			    $keywordfirstseendate = date('Y-m-d H:i:s');
			   	 $keywordlastseendate = 'NULL';
			     $flag=1;
			     $keyworddata = [
        				'article_id' =>$article_id,
        				'keyword_name' =>$keyword_name,
        				'keywordfirstseendate' =>$keywordfirstseendate,
        				'keywordlastseendate' =>$keywordlastseendate,
        				'status' =>$flag,

   						 ];
   				$result = $data->insertkeyword($keyworddata);	  
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
				$result = pg_query($query2);	  
		   }
	    }
	    
	    // existing kewords vs latest keywords
	   $resdata = $data->getarticlemapping($article_id);
       $keywordNames = array(); // Initialize an array to store the results
	    while ($rownews = pg_fetch_array($resdata)) {
	        $keywordNames[] = $rownews['keyword_name'];
	    }
	    $link= $row['link'];
	    $keyword = get_meta_tags($link);
	    //$keyword = 'aditya chopra, bollywood, salman khan, tiger 3, yash raj films';
	    $keywordname = explode(',', $keyword);
	    $datnewold = array_diff($keywordNames,$keywordname);
      if($datnewold){ 
        foreach($datnewold as $vkeyval){	
	      $flag=0;	
        $keywordlastseendate = date('Y-m-d H:i:s');
        $upkeyworddata = [
        				'keywordlastseendate' =>$keywordlastseendate,
        				'status' =>$flag,
   						 ];

   				$result = $data->updatekeyword($vkeyval,$upkeyworddata);	  
	       echo 'updated';

	       }
	      }
       }
      } else{
           $emptyArray = array();
           echo json_encode($emptyArray);
           die;
      }
    }	   
?>
