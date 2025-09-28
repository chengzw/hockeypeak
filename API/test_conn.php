<?php

  include_once('conn.php');
  
  $sql = "select * from teams limit 5";
  $result = query_with_result($sql);
  while($row = result_fetch_array($result)) {
  	print_r($row);
  }
  
  close_connection();
  echo "PPP";
?>
