<?php
   $db = pg_connect("host=dpg-ckho6v6afg7c73fit3qg-a.singapore-postgres.render.com port=5432 dbname=dev_performo user=performo password=mLDsC1aDIccyzYdDQNqmU9xTnPfifxRy");
   if(!$db) {
    //  echo "Error : Unable to open database\n";
   } else {
      //echo "Opened database successfully\n";
   }
?>