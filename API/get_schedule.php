<?php
function get_games($date) {
		$content = get_cache("schedule", $date);
		if ($content != "") {
			return json_decode($content, true);
		}
    $sql = "select games.*, teama.name as host_name, teama.abbr as host_abbr, teama.logo as host_logo, teamb.name as guest_name, teamb.abbr as guest_abbr, teamb.logo as guest_logo, teamgamesa.scores_t as host_score, teamgamesb.scores_t as guest_score from games, teams teama, teams teamb, teamgames teamgamesa, teamgames teamgamesb where games.host_id = teama.id and games.guest_id = teamb.id and games.host_id = teamgamesa.team_id and games.guest_id = teamgamesb.team_id and games.id = teamgamesa.game_id and games.id = teamgamesb.game_id and games.playtime >= '$date' and games.playtime < date_add('$date', interval 1 day) order by games.playtime";
    // echo $sql;
    $rows = get_rows_by_sql($sql);
    $data = array();
    $all_finished = true;
    $ids = array();
    foreach ($rows as $row) {
        $game = _row_to_game($row);
        // 2024-10-31: nhl官网的数据，主队和客队是反的，网页里也是把away_team显示在home_team前面
        $game = _switch_host_guest($game);
        $game['video_id'] = 0;
        // print_r($row);
        // var_dump($row);
        if (intval($row['state']) != 1) {
        	$all_finished = false;
        }
        else {
        	$ids[] = $row['id'];
        }
        // echo $row['id'] . " state: " . $row['state'] . "tip: $all_finished\n";
        $data[] = $game;
    }
    $games = array();
    if (count($ids) > 0) {
    	$sql = "select * from condensed where game_id in (" . implode(',', $ids) . ")";
    	$rows = get_rows_by_sql($sql);
  		foreach ($data as $index => $game) {
  			foreach ($rows as $row) {
  				$id = $row['game_id'];
	  			if ($game['id'] == $id) {
	  				$game['video_id'] = intval($row['video_id']);
	  				break;
	  			}
  			}
  			if ($game['video_id'] == 0) {
  			  $all_finished = false;
  			}
  			$games[] = $game;
  		}
    }
    else {
    	$games = $data;
    }
    if (count($rows) > 0 && $all_finished) {
	    set_cache("schedule", $date, json_encode($games));
    }
    return $games;
}

function get_game_dates($date, $showdates = 5, $datebuffer = 2) {
		$content = get_cache("dates", $date);
		if ($content != "") {
			return json_decode($content, true);
		}
		
		// $showdates = 5;		// 显示5天
		// $datebuffer = 2;	// 前后有两天buffer（不显示。用于快速切换日期）
		// 以5天为例：正常情况下当前日期前后各显示2天，buffer 2天。
		// 如果当前日期前面buffer不足（有显示的2天），则后面也最多buffer 2天
		// 如果当前日期前面显示的日期不足，则后面先补足显示的日期，再buffer 2天。
		
		// 有可能指定日期没有比赛
    $sql = "select distinct date_format(playtime, '%Y-%m-%d') as dd from games where playtime >= '$date' order by dd limit " . ($showdates + $datebuffer);
    $nextdates = get_rows_by_sql($sql);
    $sql = "select distinct date_format(playtime, '%Y-%m-%d') as dd from games where playtime < '$date' order by dd desc limit " . ($showdates + $datebuffer);
    $prevdates = get_rows_by_sql($sql);
    
    $prevcount = 0;
    $nextcount = 0;

    $dates = array();
    // 先计算要显示的
    $nextcount = 1 + floor($showdates / 2);		// 正常情况下，后面的数据包括指定日期和半数。如显示5，则1+2；如显示6，则也是1+2
    $nextcount = min($nextcount, count($nextdates));
    // 确定了next之后，算出prev的数目
    $prevcount = $showdates - $nextcount;
    if ($prevcount > count($prevdates)) {
    	// 数据不够
    	$prevcount = count($prevdates);
    	$nextcount = $showdates - $prevcount;			// 不可能前后数据都不够！
    }
    
    // 再加上buffer
    $prevcount += min($datebuffer, count($prevdates) - $prevcount);
    $nextcount += min($datebuffer, count($nextdates) - $nextcount);
    
    for ($i = $prevcount - 1; $i >= 0; $i--) {
        $dates[] = array("date" => $prevdates[$i]["dd"], "current" => false);
    }

		if ($nextcount > 0) {
	    $dates[] = array("date" => $nextdates[0]["dd"], "current" => true);
	    for ($i = 1; $i < $nextcount; $i++) {
	        $dates[] = array("date" => $nextdates[$i]["dd"], "current" => false);
	    }
		}
		else {
			$dates[count($dates)-1]["current"] = true;
		}
    set_cache("dates", $date, json_encode($dates));
    return $dates;
}

function get_game_dates3($date) {
		$showdates = 5;		// 显示5天
		$datebuffer = 0;	// 前后有两天buffer（不显示。用于快速切换日期）
		// 以5天为例：正常情况下当前日期前后各显示2天，buffer 2天。
		// 如果当前日期前面buffer不足（有显示的2天），则后面也最多buffer 2天
		// 如果当前日期前面显示的日期不足，则后面先补足显示的日期，再buffer 2天。
		
    $sql = "select distinct date_format(playtime, '%Y-%m-%d') as dd from games where playtime > date_add('$date', interval 1 day) order by dd limit 6";
    $nextdates = get_rows_by_sql($sql);
    $sql = "select distinct date_format(playtime, '%Y-%m-%d') as dd from games where playtime < '$date' order by dd desc limit 6";
    $prevdates = get_rows_by_sql($sql);

    $dates = array();
    // 先计算要显示的
    if (count($nextdates) < floor($showdates / 2)) {
        $prevcount = ($showdates - 1) - count($nextdates);
    }
    else {
        $prevcount = ($showdates - 1) / 2;
    }
    $prevcount = min($prevcount, count($prevdates));
    $nextcount = min(($showdates - 1) - $prevcount, count($nextdates));
    // 再加上buffer
    $prevcount = min($prevcount + $datebuffer, count($prevdates));
    $nextcount = min($nextcount + $datebuffer, count($nextdates));
    for ($i = $prevcount - 1; $i >= 0; $i--) {
        $dates[] = array("date" => $prevdates[$i]["dd"], "current" => false);
    }
    $dates[] = array("date" => $date, "current" => true);

    for ($i = 0; $i < $nextcount; $i++) {
        $dates[] = array("date" => $nextdates[$i]["dd"], "current" => false);
    }
    return $dates;
}
?>
