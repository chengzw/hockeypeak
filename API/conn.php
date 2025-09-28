<?php
  include_once('/home/services/php_utils/mysql.util.php');

  // $conn = mysql_connect("47.75.200.128", "root", "Vidown!!2018");
  // open_connection("47.75.200.128", "root", "Vidown!!2018");
  // open_connection("156.236.71.118", "nhl", "Vidown!!2018");
  // select_db("nhl");
  
  //*
  // $server = "sh-cdb-fk8pw3ra.sql.tencentcdb.com";
  // $port = 63781;
  $user = "nhl";
  $passwd = "Vidown!!2018";
  $db = "nhl";

  $server = "rm-uf6l2ts29wr9lx7tbao.mysql.rds.aliyuncs.com";
  $port = 3306;

  open_connection($server, $user, $passwd, $db, $port);
  // */
?>