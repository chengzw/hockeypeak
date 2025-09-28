<?php
	include_once('/home/services/php_utils/catcher.class.php');

	function duration_str_to_sec($duration) {
		$hour = 0;
		$min = 0;
		$second = 0;
		$nums = explode(':', $duration);
		if (count($nums) == 3) {
			$hour = intval($nums[0]);
			$min = intval($nums[1]);
			$second = intval($nums[2]);
		}
		else if (count($nums) == 2) {
			$min = intval($nums[0]);
			$second = intval($nums[1]);
		}
		else if (count($nums) == 1) {
			$second = intval($nums[0]);
		}
		return $hour * 3600 + $min * 60 + $second;
	}
		
	// ¶ÓÎé
	class NhlTeam {
    public $name;
    public $english;
    public $english_short;
    public $english_abbr;
    public $abbr;
    public $logo;
    public $homepage;
    public $nhl_team_id;

    public $id;
    
    public function __construct($team) {
      $this->name = array_key_exists("name", $team)?$team['name']:"";
      $this->english = array_key_exists("english", $team)?$team['english']:"";
      $this->english_short = array_key_exists("english_short", $team)?$team['english_short']:"";
      $this->english_abbr = array_key_exists("english_abbr", $team)?$team['english_abbr']:"";
      $this->abbr = array_key_exists("abbr", $team)?$team['abbr']:"";
      $this->logo = array_key_exists("logo", $team)?$team['logo']:"";
      $this->homepage = $team['homepage'];
      $this->nhl_team_id = array_key_exists("nhl_team_id", $team)?intval($team['nhl_team_id']):0;
      $this->id = $team['id'];
    }
    
    public function update_images() {
    	$url = $this->homepage . "/news/";
			$list = $this->get_items($url);
			echo "$url found images: " . count($list) . "\n";
			foreach ($list as $item) {
				if ($item['title'] == '' || $item['date'] == '' || $item['img'] == '') {
					continue;
				}
				$img_title = real_escape_query_string($item['title']);
				$img_url = $item['img'];
				$img_date = $item['date'];
				$sql = sprintf("select id from images where title = '%s' or url = '%s'", $img_title, $img_url);
				$id = query_existing_id($sql);
				if ($id > 0) {
					echo "img <" . $img_title . "> exists.\n";
					continue;
				}
				$sql = sprintf("insert into images(title, url, nhl_image_date, created_date) values('%s', '%s', '%s', '%s')",
											$img_title, $img_url, $img_date, date("Y-m-d"));
				$id = query_with_id($sql);
				// echo $sql . "\n";
				$sql = sprintf("insert into teamimages(image_id, team_id) values(%d, %d)", $id, $this->id);
				echo $sql . "\n";
				query_with_id($sql);
			}
	  }
	  
	  public function update_videos() {
    	$url = $this->homepage . "/video/";
  		$debug = false;
    	$list = $this->get_items($url, true, $debug);
			if (count($list) == 0) {
				echo "no video found in $url.\n";
			}
			// print_r($list);
			foreach ($list as $item) {
				$video_url = $item['url'];
				if (substr($video_url, 0, 4) != 'http') {
					$video_url = 'https://www.nhl.com' . $video_url;
					// $item = array_merge($item, $info = $this->get_video_detail($video_url));
				}
				$item['duration_num'] = duration_str_to_sec($item['duration']);
				$NhlVideo = new NhlVideo();
				$video_kind = 3;		// team video
				$newvideo_id = $NhlVideo->add_video($video_url, $video_kind);
				if ($newvideo_id > 0) {
					$sql = sprintf("update newvideos set duration = %d where id = %d", $item['duration_num'], $newvideo_id);
					query_no_result($sql);
					
					$sql = sprintf("insert into teamvideos(newvideo_id, team_id) values(%d, %d)", $newvideo_id, $this->id);
					echo "$sql\n";
					query_with_id($sql);
				}
				// print_r($item);
			
				/*
				if ($item['title'] == '' || $item['playurl'] == '') {
					continue;
				}
				$video_title = real_escape_query_string($item['title']);
				$video_description = real_escape_query_string($item['description']);
				$video_date = $item['date'];
				$video_snapshot = $item['snapshot'];
				$video_url = $item['playurl'];
				$video_duration = $item['duration_num'];
				$sql = sprintf("select id from newvideos where name = '%s' or playurl = '%s'", $video_title, $video_url);
				// echo "$sql\n";
				$id = query_existing_id($sql);
				if ($id > 0) {
					echo "video <" . $video_title . "> exists.\n";
					continue;
				}
				$sql = sprintf("insert into newvideos(name, description, nhl_video_date, snapshot, playurl, duration, created_date) values('%s', '%s', '%s', '%s', '%s', %d, '%s')",
											$video_title, $video_description, $video_date, $video_snapshot, $video_url, $video_duration, date("Y-m-d H:i:s"));
				// echo "$sql\n";
				$id = query_with_id($sql);
				$sql = sprintf("insert into teamvideos(newvideo_id, team_id) values(%d, %d)", $id, $this->id);
				echo "$sql\n";
				query_with_id($sql);
				*/
			}
	  }
    
    private function get_items($url, $reverse = true, $debug = false) {
			$content = "";
			// BasePattern($patt, $result, $patt2="", $result2="")
			// WebCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false,$referer="") {
			$catcher = new WebCatcher($url, true, $content, null, true, $debug);
			// ListPattern($content_pattern, $delimitor, $item_patterns, $outputUtf8, $debug=false)
			$content_pattern = new BasePattern('nhl-c-editorial-list `a</main>', '`a');
			$delimitor = '</article';
			$item_patterns = array('title' => new BasePattern('<h3 class="fa-text__title">`a</h3>', '`a'),
	            'date' => new BasePattern('datetime="`aT', '`a'),
	            'img' => new BasePattern('2x, http`a 3x', 'http`a'),
	            'duration' => new BasePattern('<div class="nhl-c-card__duration">`a</div>', '`a'),
	            'url' => new BasePattern(' -video`ahref="`b"', '`b'),
	            );
			$list = $catcher->GetList($content_pattern, $delimitor, $item_patterns);
			if ($reverse) {
				return array_reverse($list);
			}
			return $list;
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
	            // 'date' => new BasePattern('"uploadDate":"`aT', '`a'),
	            'snapshot' => new BasePattern('"thumbnailUrl":["`a"', '`a'),
	            // 'duration' => new BasePattern('<div class="nhl-c-card__duration">`a</div>', '`a'),
	            'playurl' => new BasePattern('"contentUrl":"`a"', '`a'),
	            );
			$properties = $catcher->GetProperties($property_patterns);
			return $properties;
	  }
	}

?>
