#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
	  include_once(__DIR__."/conn.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    
    $debug = false;
    $Nhl = new Nhl();
    $nhl_teams = $Nhl->get_all_team();
    $teams = array();
    foreach ($nhl_teams as $team) {
    	$teams[$team["nhl_team_id"]] = $team;
    }
    echo "\n******************** start time(" . date("Y-m-d H:i:s") . ") *************************\n";
    $start_date = date("Y-m-d", strtotime("-2 day"));
    $end_date = date("Y-m-d", strtotime("+3 day"));
    // 测试的时候使用下面的定义
    // $start_date = "2025-09-27";
    // $end_date = -1;
    // $end_date = "2025-05-31";
    $NhlScore = new NhlScore($start_date, $end_date);
    $dates = array();
    // $dates = array(/*'2024-10-06',*/ '2024-10-07',);
    if (count($dates) > 0) {
        $NhlScore->update_missing_score($dates);
    }
    else {
        $NhlScore->update_score();
    }

    echo "\n******************** end time(" . date("Y-m-d H:i:s") . ") *************************\n";
    
    close_connection();
?>
