<?php
	include_once('nhl_class_nhl.php');

  $Nhl = new Nhl();
  echo "get teams start date(" . date("H:i:s") . ")... \n";
  $teams = $Nhl->get_all_team();
  $team_count = count($teams);
  $players = $Nhl->get_all_player();
  $player_count = count($players);
  echo "end date(" . date("H:i:s") . ") team_count: " . $team_count . ", player_count: " . $player_count;
  echo "\n********************************* date(" . date("H:i:s") . ") ******************************************\n";
?>
