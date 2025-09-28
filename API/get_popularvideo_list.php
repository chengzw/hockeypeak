<?php
function get_popularvideo_list($pageno = 0) {
    $sql = "select count(*) from popularvideos";
    $total = get_count($sql);
    $limit = _get_limit_param($total, $pageno);
  
    $sql = "select videos.* from videos, popularvideos where videos.id = popularvideos.video_id order by videos.created_date desc $limit";
    $rows = get_rows_by_sql($sql);
    $data = array();
    foreach($rows as $row) {
        $data[] = _row_to_video($row);
    }
    return _format_list($total, $pageno, $data);
  }
  ?>
