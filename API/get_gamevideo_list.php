<?php
function get_gamevideo_list($game_id, $pageno = 0) {
    $sql = "select count(*) from gamevideos where game_id = $game_id";
    $total = get_count($sql);
    $limit = _get_limit_param($total, $pageno);
  
    $sql = "select videos.* from videos, gamevideos where videos.id = gamevideos.video_id and gamevideos.game_id = $game_id order by videos.created_date desc $limit";
    $rows = get_rows_by_sql($sql);
    $data = array();
    foreach($rows as $row) {
        $data[] = _row_to_video($row);
    }
    return _format_list($total, $pageno, $data);
  }
?>
