<?php 
include('../database.php');
class PocModel
{

    public function getuserdata($dataid)
    {
    $sqldata = "SELECT name FROM dev_performo.puser WHERE publisher_id='$dataid'";
     $resultnew = pg_query($sqldata);
        return $resultnew; 
        
    }
    public function getdateuser($dataid)
    {
   $sqldatas = "SELECT article_id FROM dev_performo.article_ranking WHERE rank_datetime >='$dataid'";
     $resultnews = pg_query($sqldatas);
        return $resultnews; 
        

    }
    public function insertuserlog($userdata)
    {
       $logname = $userdata['log_name'];
        $username = $userdata['username'];
        $createdate = $userdata['createdate'];
        $sqlquery ="INSERT INTO dev_performo.userlog (log_name,username, created) VALUES ('$logname','$username','$createdate')";
        $resultsql = pg_query($sqlquery);
        if($resultsql){
            return true;
        }else{
            return false;
        }

    }
     public function getpublishcategeory($category_id,$publisher_id)
    {
    $sqlq = "SELECT id FROM dev_performo.publisher_category_mapping WHERE category_id='$category_id' AND publisher_id='$publisher_id'";
        return $resultsql = pg_query($sqlq);
    }
     public function getarticlemasterdata($datapubid)
    {
     $query = "SELECT dev_performo.article_master.*,dev_performo.publisher_category_mapping.*,dev_performo.article_master.id as articleid FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id WHERE pub_category_id IN($datapubid) ORDER BY pubdate DESC";

        return $result = pg_query($query);

    }
    public function getarticleuser($article_id)
    {
       $sqldataque = "SELECT name FROM dev_performo.puser JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.publisher_id=dev_performo.puser.publisher_id JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE dev_performo.article_master.id='$article_id'";
        $resultsqus = pg_query($sqldataque);
        return $resultsqus; 
       

    }
    public function getkeyword($article_id)
    {
      echo  $querart = "SELECT * FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'";
        $resultart = pg_query($querart); 
        return $resultart; 
        die;

    }
    public function getranking($article_id)
    {
        $query = "SELECT * FROM dev_performo.article_ranking WHERE article_id='$article_id'"; 
         $result = pg_query($query);
        return $result; 
        

    }
    public function getarticle($article_id)
    {
        $queryart = "SELECT * FROM dev_performo.article_master WHERE dev_performo.article_master.id='$article_id'";  
        $resultartic = pg_query($queryart); 
       if(pg_num_rows($resultartic)>0){
        return $resultartic; 
        }else{
            return false;
        }

    }
    public function getcategory()
    {
    $querycat = 'SELECT DISTINCT(category_name),category_id FROM dev_performo.publisher_category_mapping ORDER BY category_id DESC';
    $resultcat = pg_query($querycat);  
       if(pg_num_rows($resultcat)>0){
        return $resultcat; 
        }else{
            return false;
        }

    }
    public function getpublisher()
    {
     $querypub = 'SELECT DISTINCT(publisher_name),publisher_id,publisher_salt FROM dev_performo.publisher_category_mapping';
    $resultpub = pg_query($querypub);  
       if(pg_num_rows($resultpub)>0){
        return $resultpub; 
        }else{
            return false;
        }

    }
    public function getpreferencedata($category,$userid)
    {
    $query = "SELECT * FROM dev_performo.user_preferences WHERE category='$category' AND user_id=$userid";
    $result = pg_query($query);  
 
        return $result; 
       

    }
    public function getarticlehours()
    {
    echo $querys = "SELECT dev_performo.article_master.id as articleid,dev_performo.article_master.link as link FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON CAST(dev_performo.publisher_category_mapping.id AS integer) = dev_performo.article_master.pub_category_id WHERE  pubdate > NOW() - INTERVAL '5 HOURS' ORDER BY pubdate DESC"; 
        $resulthours = pg_query($querys);  
        return $resulthours; 
        

    }
     public function getarticlemapping($article_id)
    {
        $sqldata = "SELECT keyword_name FROM dev_performo.article_keyword_mapping WHERE article_id='$article_id'"; 
       $resultnew = pg_query($sqldata);
       if(pg_num_rows($resultnew)>0){
        return $resultnew;
            return false;
        }

    }
     public function keyworkcheckstatus($keywordname)
    {
       $sqldatass = "SELECT status FROM dev_performo.article_keyword_mapping WHERE keyword_name='$keywordname'"; 
        $resultnewss = pg_query($sqldatass);
       if(pg_num_rows($resultnewss)>0){
        return $resultnewss;
            return false;
        }

    }
   /* public function insertkeyword($keyworddata)
    {
        $article_id = $keyworddata['article_id'];
        $keyword_name = $keyworddata['keyword_name'];
        $keywordfirstseendate = $keyworddata['keywordfirstseendate'];
         $keywordlastseendate = $keyworddata['keywordlastseendate'];
         $flag = $keyworddata['status'];
         $query2 ="INSERT INTO dev_performo.article_keyword_mapping (article_id, keyword_name, keywordfirstseendate,keywordlastseendate,status)
        VALUES ('$article_id', '$vkey','$keywordfirstseendate',$keywordlastseendate,$flag)"; 
        $result = pg_query($db, $query2);

    }
    */
    public function updatekeyword($vkeyval,$upkeyworddata)
    {
        $keywordlastseendate = $upkeyworddata['keywordlastseendate'];
         $flag = $upkeyworddata['status'];
       $query1 = "UPDATE dev_performo.article_keyword_mapping SET keywordlastseendate='$keywordlastseendate',status='$flag' WHERE keyword_name='$vkeyval'";
        $result = pg_query($db, $query1);

    }
     public function getuserdataById($userid)
    {
        $sqldataque = "SELECT name FROM dev_performo.puser WHERE id='$userid'";
        $resultsqu = pg_query($sqldataque);
        if(pg_num_rows($resultsqu)>0){
        return $resultsqu; 
        }else{
            return false;
        }
    }
    public function getuserprefernence($category,$userid)
    {
       $sqlq = "SELECT category,user_id FROM dev_performo.user_preferences WHERE category='$category' AND user_id='$userid'";
        $resultsql = pg_query($sqlq); 
        return $resultsql; 
        
    }
    public function updateprefernce($useriddata,$uppredata)
    {
        $newsource = $uppredata['newsource'];
        $catid = $uppredata['catid'];
        $query2 = "UPDATE dev_performo.user_preferences SET publisher_name = '$newsource' WHERE category='$catid' AND user_id=$useriddata";
        $result = pg_query($query2);

    }
    public function insertpreference($datas)
    {
        $category = $datas['category'];
        $userid = $datas['userid'];
        $newsource = $datas['newsource'];
        $sqlq = "INSERT INTO dev_performo.user_preferences (category, user_id, publisher_name)
         VALUES ('$category',$userid, '$newsource')";
        $resultsql = pg_query($sqlq); 

    }
  public function getuseraccordingcat($category)
    {
          $q = "SELECT name FROM dev_performo.puser JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.publisher_id=dev_performo.puser.publisher_id JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE dev_performo.publisher_category_mapping.category_id='$category'";
         $resultqq= pg_query($q); 
        return $resultqq; 
        
    }
    public function getsearchkeyword($category,$publisher_id,$keywords)
    {
      echo $sqlq = "SELECT article_id FROM dev_performo.article_keyword_mapping JOIN dev_performo.article_master ON dev_performo.article_keyword_mapping.article_id=dev_performo.article_master.id JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id=dev_performo.article_master.pub_category_id WHERE  dev_performo.publisher_category_mapping.category_id ='$category' AND dev_performo.publisher_category_mapping.publisher_id ='$publisher_id' AND dev_performo.article_keyword_mapping.keyword_name LIKE '%$keywords%'";
        $resultsql = pg_query($sqlq);
   
        return $resultsql; 
        
    }
     public function getsearchactricle($article_id)
    {
      $query = "SELECT dev_performo.article_master.*,dev_performo.publisher_category_mapping.*,dev_performo.article_master.id as articleid FROM dev_performo.article_master JOIN dev_performo.publisher_category_mapping ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id WHERE article_master.id='$article_id'"; 
      $result = pg_query($query);
        if(pg_num_rows($result)>0){
        return $result; 
        }else{
            return false;
        }
    }
    public function getpostionrankingtime($date_from,$date_to,$category_id,$publisher_id)
    {
      $queries = "SELECT rank, count(rank) FROM dev_performo.article_ranking 
        where dev_performo.article_ranking.article_id in 
        (
        SELECT article_master.id FROM dev_performo.publisher_category_mapping JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id 
     WHERE article_master.pubdate >='$date_from' AND article_master.pubdate < '$date_to' AND publisher_category_mapping.category_id='$category_id' AND publisher_category_mapping.publisher_id='$publisher_id' 

        ) group by rank";
        $result1 = pg_query($queries);
        return $result1; 
        
    }
    public function getpostionranking($date_from,$date_to,$category_id,$publisher_id)
    {
      $query3 = "SELECT rank, count(rank) FROM dev_performo.article_ranking 
        where dev_performo.article_ranking.article_id in 
        (
        SELECT article_master.id FROM dev_performo.publisher_category_mapping JOIN dev_performo.article_master ON dev_performo.publisher_category_mapping.id =dev_performo.article_master.pub_category_id 
     WHERE article_master.pubdate >='$date_from' AND article_master.pubdate < '$date_to' AND publisher_category_mapping.category_id='$category_id' AND publisher_category_mapping.publisher_id='$publisher_id' 

        ) group by rank;";
      $result3 = pg_query($query3);
        return $result3; 
        
    }
    public function gettopkeyword($date_from,$date_to)
    {
      $query1 = "SELECT name FROM dev_performo.trends_keywords WHERE firstseendate >='$date_from' AND firstseendate >='$date_to' ORDER BY firstseendate DESC LIMIT 5";
      $result2 = pg_query($query1);
        return $result2; 
        
    }
    public function getearlyoffer($date_from,$date_to,$category_id,$publisher_id)
    {
    $query1 = "select * 
     from (
     select akm.keyword_name,rank() over(partition by akm.keyword_name order by am.pubdate),pcm.publisher_name,pcm.publisher_id,pcm.category_id,am.pubdate  
     from dev_performo.article_keyword_mapping akm
     inner join dev_performo.article_master am on am.id = akm.article_id
     inner join dev_performo.publisher_category_mapping pcm on pcm.id = am.pub_category_id
     ) T 
     where pubdate >= '$date_from'
       and pubdate < '$date_to' AND category_id='$category_id' AND publisher_id='$publisher_id'  ORDER BY pubdate DESC";
      $result2 = pg_query($query1);
        return $result2; 
        
    }
    public function getlegarddata($date_from,$date_to,$category_id,$publisher_id)
    {
    $querynew = "SELECT * 
     from (
     select akm.keyword_name,rank() over(partition by akm.keyword_name order by am.pubdate),pcm.publisher_name,pcm.publisher_id,pcm.category_id,am.pubdate  
     from dev_performo.article_keyword_mapping akm
     inner join dev_performo.article_master am on am.id = akm.article_id
     inner join dev_performo.publisher_category_mapping pcm on pcm.id = am.pub_category_id
     ) T 
     where pubdate >= '$date_from'
       and pubdate < '$date_to' 
	   AND publisher_id='$publisher_id'
	   AND category_id='$category_id'
	   ORDER BY pubdate,rank DESC";
      $resultnew = pg_query($querynew);
        return $resultnew; 
        
    }
    public function getlmissedtarin($date_from,$date_to,$category_id,$publisher_id)
    {
   $querymissed = "SELECT * 
     from (
     select DISTINCT(akm.keyword_name),rank() over(partition by akm.keyword_name order by am.pubdate),pcm.publisher_name,pcm.publisher_id,pcm.category_id,am.pubdate  
     from dev_performo.article_keyword_mapping akm
     inner join dev_performo.article_master am on am.id = akm.article_id
     inner join dev_performo.publisher_category_mapping pcm on pcm.id = am.pub_category_id
     ) T 
     where pubdate >= '$date_from'
       and pubdate < '$date_to'
	   AND publisher_id='$publisher_id'
	   AND category_id='$category_id'";
      $resultmissed = pg_query($querymissed);
        return $resultmissed; 
        
    }
    public function insertsignupdata($datas)
    {
        $name = $datas['name'];
        $email = $datas['email'];
        $image = $datas['image'];
        $subscriber = $datas['subscriber'];
        $sqlq = "INSERT INTO dev_performo.puser (name,publisher_id, email, image,subscriber)
         VALUES ('$name','-999','$email', '$image','$subscriber')";
        $resultsql = pg_query($sqlq); 

    }
    public function getsignup()
    {
          $q = "SELECT * FROM dev_performo.puser";
                $resultqq= pg_query($q); 
        if(pg_num_rows($resultqq)>0){
        return $resultqq; 
        }else{
            return false;
        }
    }
    public function getuseremail($email)
    {
       $sqlq = "SELECT * FROM dev_performo.puser WHERE  email='$email'";
        $resultsql = pg_query($sqlq); 
        return $resultsql; 
        
    }
    public function updatesignup($id,$uppredata)
    {
        $request = $uppredata['request'];
       $query2 = "UPDATE dev_performo.puser SET request='$request' WHERE id='$id'";
        $result = pg_query($query2);

    }
}
?>