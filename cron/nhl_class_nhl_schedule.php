<?php
	include_once('/home/services/php_utils/catcher.class.php');
	include_once('nhl_class_nhl.php');

	class NhlSchedule extends NhlBase {
		private $force_update;
    
    public function __construct($start_date, $end_date = -1) {

			parent::__construct($start_date, $end_date);
			$this->force_update = false;
			echo "start: " . $this->start_date . ", end: " . $this->end_date . "\n";
    }
    
    public function set_force_update($force) {
    	$this->force_update = $force;
    }
    
    public function update_schedule() {
    	$this->process_by_date("schedule");
    }
    
    protected function update_date_schedule($date) {
    	echo "update_date_schedule: $date\n";
    	$nextDate = "";
    	$games = $this->get_games($date, $nextDate);
    	// print_r($games);
    	foreach ($games as $game) {
    		if ($game["host_id"] == 0 || $game["guest_id"] == 0) {
    			print_r($game);
    			continue;
    		}
    		$sql = "select * from games where gamePk = " . $game["gamePk"];
    		$row = get_row_by_sql($sql);
    		$id = 0;
    		if (is_array($row) && count($row) > 0) {
    			$id = intval($row["id"]);
    		}
    		if ($id == 0) {
	    		$sql = sprintf("insert into games(playtime, host_id, guest_id, gameCenter, state, gamePk) values('%s', %d, %d, '%s', 0, %d)",
	    							$game["playtime"], $game["host_id"], $game["guest_id"], $game["gameCenter"], $game["gamePk"]);
	    		echo $sql . "\n";
	    		$id = query_with_id($sql);
    		}
    		else if ($this->force_update) {
    			// 更新赛程的时候，不添加recap，只有等比赛完更新score的时候才添加进去
  				$sql = sprintf("update games set playtime = '%s', host_id = %d, guest_id = %d, gameCenter = '%s' where id = %d", 
  												$game["playtime"],
  												$game["host_id"],
  												$game["guest_id"],
  												$game["gameCenter"],
  												$id);
  				echo ($sql);
  				query_no_result($sql);
    		}
    		
    		if ($id > 0) {
    			$this->check_insert_teamgame($id, $game["host_id"]);
    			$this->check_insert_teamgame($id, $game["guest_id"]);
    		}
    	}
    	return $nextDate;
    }
    
    private function check_insert_teamgame($game_id, $team_id) {
			$sql = "select * from teamgames where game_id = $game_id and team_id = $team_id";
			$teamgame_id = query_existing_id($sql);
			if ($teamgame_id == 0) {
				$sql = "insert into teamgames(game_id, team_id) values($game_id, $team_id)";
				echo $sql . "\n";
				query_no_result($sql);
			}
    }
  }

?>

