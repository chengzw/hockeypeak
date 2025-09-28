#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
	  include_once(__DIR__."/conn.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    
    $Nhl = new Nhl();
    echo "\n******************** start time(" . date("Y-m-d H:i:s") . ") *************************\n";
    $start_date = "2019-10-04";
    $end_date = "2024-02-19";
    $NhlScore = new NhlScore($start_date, $end_date);
    // $NhlScore->update_gamecenter_recap();
    // $NhlScore->update_recap_video();
    $NhlScore->update_missing_recap_video();

    echo "\n******************** end time(" . date("Y-m-d H:i:s") . ") *************************\n";
    
    close_connection();
?>
