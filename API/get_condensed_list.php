<?php
function get_condensed_list($pageno = 0) {
    $sql = "select count(*) from condensed";
    $total = get_count($sql);
    $limit = _get_limit_param($total, $pageno);
  
    $sql = "select videos.*, teama.abbr as host_abbr, teamb.abbr as guest_abbr from videos, condensed, games, teams as teama, teams as teamb where videos.id = condensed.video_id and condensed.game_id = games.id and games.host_id = teama.id and games.guest_id = teamb.id order by games.playtime desc $limit";
    $rows = get_rows_by_sql($sql);
    $data = array();
    foreach($rows as $row) {
    		// $row["host_abbr"] = "主队";
    		// $row["guest_abbr"] = "客队";
        $data[] = _row_to_condensed($row);
    }
    return _format_list($total, $pageno, $data);
  }
?>
