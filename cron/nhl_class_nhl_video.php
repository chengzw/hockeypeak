<?php
	include_once('/home/services/php_utils/catcher.class.php');

	define('VIDEO_KIND_RECAP', 0);
	define('VIDEO_KIND_PLAYER', 2);
	define('VIDEO_KIND_TEAM', 3);
	class NhlVideo {
    public function __construct() {
		}
		
		public function add_video($url, $kind) {
			if ($url == "") {
				return 0;
			}
			
    	$sql = "select * from newvideos where url = '$url'";
    	$result = get_row_by_sql($sql);
    	$video_id = 0;
    	if (is_array($result) && count($result) > 0) {
    		return 0;
    	}
    	$detail = $this->get_video_detail($url);
    	if ($detail["name"] == "" || $detail["playurl"] == "") {
    		return 0;
    	}
    	// print_r($detail);
  		$sql = sprintf("insert into newvideos(url, name, description, nhl_video_date, snapshot, playurl, kind, created_date) values('%s', '%s', '%s', '%s', '%s', '%s', %d, '%s')",
  							$url,
  							real_escape_query_string($detail["name"]),
  							real_escape_query_string($detail["description"]),
  							$detail["date"],
  							$detail["snapshot"],
  							$detail["playurl"],
  							$kind,
  							date("Y-m-d H:i:s"));
    	echo "$sql\n";
  		$video_id = query_with_id($sql);
    	return $video_id;
		}
		
		public function update_video($url, $kind) {
			if ($url == "") {
				return 0;
			}
			
    	$sql = "select * from newvideos where url = '$url'";
    	$result = get_row_by_sql($sql);
    	$video_id = 0;
    	if (is_array($result) && count($result) > 0) {
    		$video_id = $result["id"];
    		if ($result["playurl"] != "") {
    			return $video_id;
    		}
    	}
    	$detail = $this->get_video_detail($url);
    	// print_r($detail);
    	if ($detail["name"] == "" || $detail["playurl"] == "") {
    		return 0;
    	}
    	if ($video_id > 0) {
    		$sql = sprintf("update newvideos set name = '%s', description = '%s', nhl_video_date = '%s', snapshot = '%s', playurl = '%s', kind = %d where id = %d",
    							real_escape_query_string($detail["name"]),
    							real_escape_query_string($detail["description"]),
    							$detail["date"],
    							$detail["snapshot"],
    							$detail["playurl"],
    							$kind,
    							$video_id);
	    	echo "$sql\n";
	    	query_no_result($sql);
    	}
    	else {
    		$sql = sprintf("insert into newvideos(url, name, description, nhl_video_date, snapshot, playurl, kind, created_date) values('%s', '%s', '%s', '%s', '%s', '%s', %d, '%s')",
    							$url,
    							real_escape_query_string($detail["name"]),
    							real_escape_query_string($detail["description"]),
    							$detail["date"],
    							$detail["snapshot"],
    							$detail["playurl"],
    							$kind,
    							date("Y-m-d H:i:s"));
	    	echo "$sql\n";
    		$video_id = query_with_id($sql);
    	}
    	return $video_id;
		}
	  
	  private function get_video_detail($video_url) {
			$content = "";
			$debug = false;
			$content_pattern = new BasePattern('<div class="nhl-c-video-detail">`a</div>', '`a');
			// BasePattern($patt, $result, $patt2="", $result2="")
			// WebCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false,$referer="") {
			$catcher = new WebCatcher($video_url, true, $content, $content_pattern, true, $debug);
			// ListPattern($content_pattern, $delimitor, $item_patterns, $outputUtf8, $debug=false)
			$property_patterns = array('description' => new BasePattern('"description":"`a"', '`a'),
	            'date' => new BasePattern('"uploadDate":"`aT', '`a'),
	            'name' => new BasePattern('"name":"`a"', '`a'),
	            'snapshot' => new BasePattern('"thumbnailUrl":["`a"', '`a'),
	            // 'duration' => new BasePattern('<div class="nhl-c-card__duration">`a</div>', '`a'),
	            'playurl' => new BasePattern('"contentUrl":"`a"', '`a'),
	            );
			$properties = $catcher->GetProperties($property_patterns);
			return $properties;
	  }
		
	}

?>
	