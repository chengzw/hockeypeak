#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
	  include_once(__DIR__."/conn.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    include_once(__DIR__."/catcher_team.php");

    // 获取队伍列表
    $Nhl = new Nhl();
    echo "get teams start date(" . date("Y-m-d H:i:s") . ")... ";
    $teams = $Nhl->get_all_team();
    $team_count = count($teams);
    echo "end date(" . date("Y-m-d H:i:s") . ") team_count:".$team_count;

    $team_count = count($teams);
    foreach ($teams as $team_index => $team) {
      echo "\n  get [".($team_index+1)."/".$team_count."] team start date(" . date("Y-m-d H:i:s") . ")...\n";
      if(!array_key_exists("homepage", $team)) {
          echo " homepage is null! end date(" . date("Y-m-d H:i:s") . ")";
          continue;
      }
      if (substr($team['homepage'], 0,4) != 'http') {
          $team['homepage'] = 'https://www.nhl.com/'.$team['homepage'];
      }
      $homepage = $team['homepage'];
      echo "\tteam url: $homepage";
      $NhlTeam = new NhlTeam($team);
      // echo " nhl_team_id:" . $NhlTeam->nhl_team_id . " ";
      $NhlTeam->update_images();
      $NhlTeam->update_videos();
    }
    echo "\n********************************* date(" . date("Y-m-d H:i:s") . ") ******************************************\n";
    
    close_connection();
?>
