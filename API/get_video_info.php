<?php
include_once('/home/services/php_utils/catcher.class.php');

function get_video_info($video_id) {
  // 只获取一种格式HTTP_CLOUD_MOBILE
  // 20200811：HTTP_CLOUD_MOBILE质量太差，改成HTTP_CLOUD_WIRED
  // 2020-10-10: HTTP_CLOUD_WIRED质量也不行，用HTTP_CLOUD_WIRED_60
  $sql = "select videos.*, videourls.format, videourls.url from videos, videourls where videos.id = $video_id and videos.id = videourls.video_id and (videourls.name = 'HTTP_CLOUD_WIRED' or videourls.name = '')";
  $row = get_row_by_sql($sql);
  $video_detail = _row_to_video_detail($row);
  // $video_detail["type"] = "url";
  // $video_detail["url"] = str_replace("MasterWired.m3u8", "849957/game.m3u8", $video_detail["url"]);
  return $video_detail;
}

function update_playurl(&$row) {
  // $date_now = new DateTime();
  // $date_updated = new DateTime($row['updated_date']);
  // $interval = $date_now->diff($date_updated);
  // $diff_days = $interval->format('%a days');
  // if ($diff_days > 300) {
  //   // WebCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false,$referer="")
  //   $content = "";
  //   $catcher = new WebCatcher($row['url'], $isUtf8, $content, null);
  //   $row['url'] = "";
  // }
}

function get_newvideo_info($video_id) {
  $sql = "select * from newvideos where id = $video_id";
  $row = get_row_by_sql($sql);
  $video_detail = _row_to_newvideo_detail($row);
  // if ($row['updated_date'])
  $video_detail["format"] = "nhl";
  return $video_detail;
}

function get_relatenewvideo_list($video_id, $video_kind, $pageno = 0) {
  $data = array();
  // $video_kind = 3;
  $kinddefine = array("game", "game", "player", "team");
  $kind = $kinddefine[$video_kind];
  $sql = "select ".$kind."_id as kind_id from ".$kind."videos where newvideo_id = $video_id";
  // echo "[$sql]";
  $row = get_row_by_sql($sql);
  if (!is_array($row)) {
  	return $data;
  }
  $kind_id = $row["kind_id"];

  $sql = "select count(*) from ".$kind."videos where ".$kind."_id = $kind_id and video_id is null";
  $total = get_count($sql);
  $limit = _get_limit_param($total, $pageno);

  $sql = "select newvideos.* from newvideos, ".$kind."videos where ".$kind."videos.".$kind."_id = $kind_id and newvideos.id = ".$kind."videos.newvideo_id $limit";
  $rows = get_rows_by_sql($sql);
  foreach($rows as $row) {
      $data[] = _row_to_newvideo($row);
  }
  return _format_list($total, $pageno, $data);
}

function get_relatevideo_list($video_id, $video_kind, $pageno = 0) {
  $data = array();
  // $video_kind = 3;
  if ($video_kind == 3) {
  	return get_relatenewvideo_list($video_id, $video_kind, $pageno = 0);
  }
  $kinddefine = array("game", "game", "player", "team");
  $kind = $kinddefine[$video_kind];
  $sql = "select ".$kind."_id as kind_id from ".$kind."videos where video_id = $video_id";
  // echo "[$sql]";
  $row = get_row_by_sql($sql);
  if (!is_array($row)) {
  	return $data;
  }
  $kind_id = $row["kind_id"];

  $sql = "select count(*) from ".$kind."videos where ".$kind."_id = $kind_id";
  $total = get_count($sql);
  $limit = _get_limit_param($total, $pageno);

  $sql = "select videos.* from videos, ".$kind."videos where ".$kind."videos.".$kind."_id = $kind_id and videos.id = ".$kind."videos.video_id $limit";
  $rows = get_rows_by_sql($sql);
  foreach($rows as $row) {
      $data[] = _row_to_video($row);
  }
  return _format_list($total, $pageno, $data);
}
?>
