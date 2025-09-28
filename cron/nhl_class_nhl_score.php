<?php
	include_once('/home/services/php_utils/catcher.class.php');
	include_once('nhl_class_nhl.php');

	class NhlScore extends NhlBase {
    
    public function __construct($start_date, $end_date) {
			parent::__construct($start_date, $end_date);
    }

		public function update_score() {
			$this->process_by_date("score");
		}
		   
    public function update_gamecenter_recap() {
			$this->process_by_date("recap");
    }
    
    public function update_recap_video() {
    	$this->process_by_date("recap_video");
    }
    
    protected function update_date_score($date) {
    	$nextDate = "";
    	$games = $this->get_games($date, $nextDate);
    	$stop = false;
    	foreach ($games as $game) {
    		$sql = "select id from games where gamePk = " . $game["gamePk"];
    		echo "$sql\n";
    		$game_id = query_existing_id($sql);
    		if ($game_id == 0) {
    			$stop = true;
    			echo "the schedule is not exist for " . $game["gamePk"] . "\n";
    			continue;
    		}
    		$game["id"] = $game_id;
    		$stop = false;
    		if ($game["host_score"] > 0 || $game["guest_score"] > 0) {
	    		$sql = sprintf("update games set recap = '%s', gamecenter = '%s', state = 1 where gamePk = %d", 
    						$game["recap"], $game["gameCenter"], $game["gamePk"]);
	    		echo $sql . "\n";
	    		query_no_result($sql);
	    		
	    		$this->update_game_recap_video($game);
	    		
	    		$sql = sprintf("select * from teamgames where game_id = %d and team_id = %d", $game_id, $game["host_id"]);
	    		$id = query_existing_id($sql);
	    		if ($id > 0) {
	    			$sql = sprintf("update teamgames set scores1 = %d, scores2 = %d, scores3 = %d, scores_ot = %d, scores_so = %d, scores_t = %d where game_id = %d and team_id = %d", 
	    						$game["host_scores1"], $game["host_scores2"], $game["host_scores3"], $game["host_scores_ot"], $game["host_scores_so"], ($game["host_scores1"] + $game["host_scores2"] + $game["host_scores3"] + $game["host_scores_ot"] + $game["host_scores_so"]), $game_id, $game["host_id"]);
	    		}
	    		else {
		    		$sql = sprintf("insert into teamgames(game_id, team_id, scores1, scores2, scores3, scores_ot, scores_so, scores_t) values(%d, %d, %d, %d, %d, %d, %d, %d)",
	    						$game_id, $game["host_id"], $game["host_scores1"], $game["host_scores2"], $game["host_scores3"], $game["host_scores_ot"], $game["host_scores_so"], ($game["host_scores1"] + $game["host_scores2"] + $game["host_scores3"] + $game["host_scores_ot"] + $game["host_scores_so"]));
	    		}
	    		echo $sql . "\n";
	    		query_no_result($sql);
	    		
	    		$sql = sprintf("select * from teamgames where game_id = %d and team_id = %d", $game_id, $game["guest_id"]);
	    		$id = query_existing_id($sql);
	    		if ($id > 0) {
	    			$sql = sprintf("update teamgames set scores1 = %d, scores2 = %d, scores3 = %d, scores_ot = %d, scores_so = %d, scores_t = %d where game_id = %d and team_id = %d", 
	    						$game["guest_scores1"], $game["guest_scores2"], $game["guest_scores3"], $game["guest_scores_ot"], $game["guest_scores_so"], ($game["guest_scores1"] + $game["guest_scores2"] + $game["guest_scores3"] + $game["guest_scores_ot"] + $game["guest_scores_so"]), $game_id, $game["guest_id"]);
	    		}
	    		else {
	    			$sql = sprintf("insert into teamgames(game_id, team_id, scores1, scores2, scores3, scores_ot, scores_so, scores_t) values(%d, %d, %d, %d, %d, %d, %d, %d)",
	    						$game_id, $game["guest_id"], $game["guest_scores1"], $game["guest_scores2"], $game["guest_scores3"], $game["guest_scores_ot"], $game["guest_scores_so"], ($game["guest_scores1"] + $game["guest_scores2"] + $game["guest_scores3"] + $game["guest_scores_ot"] + $game["guest_scores_so"]));
	    		}
	    		echo $sql . "\n";
    		}
    		query_no_result($sql);
    	}
    	if ($stop) {
    		echo "$date has no games in schedule, stopped.\n";
    		$this->stop = true;
    	}
    	return $nextDate;
    }
    
    protected function update_date_recaps($date) {
    	$nextDate = "";
    	$games = $this->get_games($date, $nextDate);
    	foreach ($games as $game) {
    		$sql = sprintf("update games set recap = '%s', gamecenter = '%s' where gamePk = %d", 
    						$game["recap"], $game["gameCenter"], $game["gamePk"]);
    		echo $sql . "\n";
    		query_no_result($sql);
    	}
    	return $nextDate;
    }
    
    private function get_date_games_from_db($date, &$nextDate) {
    	$sql = "select * from games where games.playtime >= '$date' and games.playtime < date_add('$date', interval 1 day) order by id";
    	$games = get_rows_by_sql($sql);
    	$nextDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
    	if (count($games) > 0) {
    		$next_id = $games[count($games)-1]["id"] + 1;
    		$sql = "select date_format(playtime, '%Y-%m-%d') as dd from games where id >= $next_id order by playtime limit 1";
    		$result = get_row_by_sql($sql);
    		if (count($result) > 0) {
    			$nextDate = $result["dd"];
    		}
    	}
    	return $games;
    }
    
    protected function update_date_recap_video($date) {
    	$nextDate = "";
    	$games = $this->get_date_games_from_db($date, $nextDate);
    	echo "date: $date, next date: $nextDate\n";
    	foreach ($games as $game) {
    		$this->update_game_recap_video($game);
    		// print_r($game);
    	}
    	return $nextDate;
    }
    
    private function update_game_recap_video($game) {
    	$debug_game_id = 0;
    	$game_id = $game["id"];
    	if ($game["id"] == "" || $game["id"] == 0) {
    		echo "game: $game_id is invalid.\n";
    		return;
    	}
    	if ($game["recap"] == "") {
    		echo "game: $game_id has no recap.\n";
    		return;
    	}
    	if ($game["recap_video_id"] > 0) {
    		echo "game: $game_id has newvideo id already.\n";
    		return;
    	}
    	if ($debug_game_id > 0 && $game_id != $debug_game_id) {
    		echo "game $game_id is not debug game($debug_game_id).\n";
    		return;
    	}
    	
			$recap_url = "https://www.nhl.com" . $game["recap"];
			$NhlVideo = new NhlVideo();
			$newvideo_id = $NhlVideo->update_video($recap_url, VIDEO_KIND_RECAP);
			if ($newvideo_id > 0) {
				$sql = sprintf("update games set recap_video_id = %d where id = %d", $newvideo_id, $game_id);
				echo "$sql\n";
				query_no_result($sql);
				
				$sql = sprintf("select id from gamevideos where game_id = %d and newvideo_id = %d", $game_id, $newvideo_id);
				$gamevideo_id = query_existing_id($sql);
				if ($gamevideo_id == 0) {
					$sql = "insert into gamevideos(game_id, newvideo_id) values($game_id, $newvideo_id)";
					echo "$sql\n";
					query_no_result($sql);
				}
				else {
					echo "game $game_id has newvideo $newvideo_id already.\n";
				}
			}
    }

    
    private function get_missing_recap_games_from_db() {
    	$sql = "select *, date_format(playtime, '%Y-%m-%d') as dd from games where recap = '' and state = 1 and host_id > 0 and guest_id > 0 order by playtime desc";
    	$games = get_rows_by_sql($sql);
    	return $games;
    }
    
    public function update_missing_recap_video() {
    	$nextDate = "";
    	$allgames = $this->get_missing_recap_games_from_db();
    	foreach ($allgames as $game) {
	    	$nextDate = "";
	    	print_r($game);
  	  	$games = $this->get_games(date("Y-m-d", strtotime("-1 day", strtotime($game["dd"]))), $nextDate);
  	  	$found = false;
				foreach ($games as $single) {
					if ($single["gamePk"] == $game["gamePk"] && $single["recap"] != "") {
						$game["recap"] = $single["recap"];
						$game["gameCenter"] = $single["gameCenter"];
		    		$sql = sprintf("update games set recap = '%s', gamecenter = '%s' where gamePk = %d", 
		    						$game["recap"], $game["gameCenter"], $game["gamePk"]);
		    		echo $sql . "\n";
		    		query_no_result($sql);
		    		
		    		$this->update_game_recap_video($game);
		    		print_r($single);
		    		$found = true;
		    		break;
					}
				}
				if (!$found) {
					print_r($games);
					break;
				}
				break;
    	}
    }

  }

?>

