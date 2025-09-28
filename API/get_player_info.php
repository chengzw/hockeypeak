<?php
function get_player_info($player_id) {

	$sql = "select players.*, teams.name as teamname from players, teams where players.id = $player_id and players.team_id = teams.id";
  $row = get_row_by_sql($sql);
  return _row_to_player($row);
}

function get_playervideo_list($player_id, $pageno = 0) {
  $sql = "select count(*) from playervideos where player_id = $player_id";
  $total = get_count($sql);
  $limit = _get_limit_param($total, $pageno);

  $sql = "select videos.* from videos, playervideos where videos.id = playervideos.video_id and playervideos.player_id = $player_id order by videos.created_date desc $limit";
  $rows = get_rows_by_sql($sql);
  $data = array();
  foreach($rows as $row) {
      $data[] = _row_to_video($row);
  }
  return _format_list($total, $pageno, $data);
}
?>
