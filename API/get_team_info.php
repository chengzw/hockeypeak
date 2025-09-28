<?php
function get_team_info($team_id) {

  $sql = "select * from teams where id = $team_id";
  $row = get_row_by_sql($sql);
  return _row_to_team($row);
}

function get_teamvideo_list($team_id, $pageno = 0) {
  $sql = "select count(*) from teamvideos where team_id = $team_id and video_id > 0 order by id desc";
  $total = get_count($sql);
  $limit = _get_limit_param($total, $pageno);
  
  $sql = "select videos.* from videos, teamvideos where videos.id = teamvideos.video_id and teamvideos.team_id = $team_id order by videos.created_date desc $limit";
  $rows = get_rows_by_sql($sql);
  $data = array();
  foreach($rows as $row) {
      $data[] = _row_to_video($row);
  }
  return _format_list($total, $pageno, $data);
}

function get_teamnewvideo_list($team_id, $pageno = 0) {
  $sql = "select count(*) from teamvideos where team_id = $team_id and newvideo_id > 0 order by id desc";
  $total = get_count($sql);
  $limit = _get_limit_param($total, $pageno);

  $sql = "select newvideos.* from newvideos, teamvideos where newvideos.id = teamvideos.newvideo_id and teamvideos.team_id = $team_id order by newvideos.nhl_video_date desc, created_date desc $limit";
  $rows = get_rows_by_sql($sql);
  $data = array();
  foreach($rows as $row) {
      $data[] = _row_to_newvideo($row);
  }
  return _format_list($total, $pageno, $data);
}

function get_teamimages($team_id) {
    $sql = "select images.* from images, teamimages where images.id = teamimages.image_id and teamimages.team_id = $team_id order by images.created_date desc limit ".TEAM_IMAGE_LIMIT;
    $rows = get_rows_by_sql($sql);
    $data = array();
    foreach($rows as $row) {
        $data[] = _row_to_image($row);
    }
    return $data;
}
?>
