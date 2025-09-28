<?php
	class NhlBase {
    protected $start_date;
    protected $end_date;
    protected $teams;
    protected $stop;
    
    public function __construct($start_date, $end_date) {
    	global $teams;
    	
    	$this->start_date = $start_date;
    	$this->end_date = $end_date;
			if ($end_date == -1) {
				$this->end_date = date("Y-m-d", strtotime("+1 year"));
			}
			echo "date range: $start_date, " . $this->end_date . "\n";
    	$this->teams = $teams;
    	$this->stop = false;
    }
		 
	protected function process_by_date($type) {
	    $start = new DateTime($this->start_date);
	    $end = new DateTime($this->end_date);
	    $nextDate = $this->start_date;
	    foreach(new DatePeriod($start, new DateInterval('P1D'), $end) as $d){
	    	if ($this->stop) {
	    		echo "stopped for class stopped.\n";
	    		break;
	    	}
	    	if ($nextDate == "") {
	    		echo "no next date, stopped.\n";
	    		break;
	    	}
			$date = $d->format('Y-m-d');
			if ($date != $nextDate) {
				echo "nextDate is: $nextDate, skip date: $date...\n";
				continue;
			}
			echo "process $date...\n";
			$func = "update_date_$type";
			$nextDate = $this->$func($date);
		}
	}
    
    protected function get_games($date, &$nextDate) {
		$url = sprintf("https://api-web.nhle.com/v1/score/%s", $date);
		echo "get games for $date..., url: $url\n";
		// print_r(file_get_contents($url));
    	// JsonCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false)
    	$content = "";
    	$debug = false;
    	$catcher = new JsonCatcher($url, true, $content, null, true, $debug);
    	$params = array("nextDate" => new JsonPattern(array("nextDate")));
    	$result = $catcher->GetProperties($params);
    	$nextDate = $result["nextDate"];
		echo "get next date: " . $nextDate . "\n";
    	$properties = array("playtimeUTC" => "startTimeUTC",
    								"host_nhl_id" => "homeTeam|id",
    								"host_score" => "homeTeam|score",
    								"host_abbr" => "homeTeam|abbrev",
    								"guest_nhl_id" => "awayTeam|id",
    								"guest_score" => "awayTeam|score",
    								"guest_abbr" => "awayTeam|abbrev",
    								"gamePk" => "id",
    								"recap" => "threeMinRecap",
    								"gameCenter" => "gameCenterLink",
    								"goals" => "goals");
    	$games = $catcher->GetList(array("games"), $properties);
    	foreach ($games as &$game) {
    		// print_r($game);
    		$game["playtime"] = date("Y-m-d H:i:s", strtotime($game["playtimeUTC"]));
    		$game["host_id"] = $this->teams[$game["host_nhl_id"]]["id"];
    		$game["host_name"] = $this->teams[$game["host_nhl_id"]]["name"];
    		$game["guest_id"] = $this->teams[$game["guest_nhl_id"]]["id"];
    		$game["guest_name"] = $this->teams[$game["guest_nhl_id"]]["name"];
    		$game["host_scores1"] = 0;
    		$game["host_scores2"] = 0;
    		$game["host_scores3"] = 0;
    		$game["host_scores_ot"] = 0;
    		$game["host_scores_so"] = 0;
    		$game["guest_scores1"] = 0;
    		$game["guest_scores2"] = 0;
    		$game["guest_scores3"] = 0;
    		$game["guest_scores_ot"] = 0;
    		$game["guest_scores_so"] = 0;
    		if (!array_key_exists("goals", $game) || empty($game["goals"])) {
    			continue;
    		}
    		foreach ($game["goals"] as $goal) {
    			if ($goal["teamAbbrev"] == $game["host_abbr"]) {
    				$name = "host";
    			}
    			else {
    				$name = "guest";
    			}
    			$suffix = strval($goal["period"]);
    			if ($goal["period"] == 4) {
    				$suffix = "_ot";
    			}
    			else if ($goal["period"] == 5) {
    				$suffix = "_so";
    			}
    			$key = $name . "_scores" . $suffix;
    			$game[$key]++;
    			// print_r($goal);
    			// echo "key: $key\n";
    		}
    		unset($game["goals"]);
    	}
    	return $games;
    }
	}
?>

