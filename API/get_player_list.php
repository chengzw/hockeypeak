<?php

function get_player_list($pageno = 0) {
		
		$sql = "select count(*) from players, playerrankings, playersearch where players.id = playerrankings.player_id and players.id = playersearch.player_id and players.name is not null";
    // $condition = "players.name is not null";
    // $sql = "select count(*) from players where $condition";
    $total = get_count($sql);
    $limit = _get_limit_param($total, $pageno);
    // $sql = "select players.*, teams.name as teamname, teams.logo from players, teams where players.team_id = teams.id and $condition $limit";
    $sql = "select players.*, teams.name as teamname, teams.logo, (playersearch.pos * 10 + playerrankings.rank) as rank from players, playerrankings, playersearch, teams where players.id = playerrankings.player_id and players.id = playersearch.player_id and players.name is not null and players.team_id = teams.id order by rank $limit";
    $rows = get_rows_by_sql($sql);

    $data = array();
    foreach($rows as $row) {
        // $row["name"] = $row["english"];
        $data[] = _row_to_player($row);
    }
    return _format_list($total, $pageno, $data);
}
?>
