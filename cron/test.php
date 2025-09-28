#!/usr/bin/php
<?php
    // include_once(__DIR__."/config.php");
	  // include_once(__DIR__."/conn.php");
    // include_once(__DIR__."/nhl_function.php");
    // include_once(__DIR__."/nhl_class.php");
    include_once("/home/services/php_utils/http.util.php");

    $url = "https://api-web.nhle.com/v1/schedule-calendar/2024-10-22";
    $content = "";
    $headers = "";
    $httpCode = curl_contents($url, $content, $headers);
    echo "httpCode: " . $httpCode . "\n";
    echo "content: " . $content . "\n";
    echo "headers: " . $headers . "\n";

		// $url = "https://www.nhl.com/video/recap-wsh-3-stl-2-f-ot-309676020";
		// $Video = new NhlVideo($url);
		
		/*    
    $Nhl = new Nhl();
    $nhl_teams = $Nhl->get_all_team();
    $teams = array();
    foreach ($nhl_teams as $team) {
    	$teams[$team["nhl_team_id"]] = $team;
    }
    echo "\n******************** start time(" . date("Y-m-d H:i:s") . ") *************************\n";
    $start_date = "2023-11-06";
    $end_date = "2023-11-08";
    $NhlScore = new NhlScore($start_date, $end_date);
    $NhlScore->update_scores();

    echo "\n******************** end time(" . date("Y-m-d H:i:s") . ") *************************\n";
    */

    
    // close_connection();
?>
