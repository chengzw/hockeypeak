<?php
    include_once('/home/services/php_utils/base.php');
    include_once('/home/services/php_utils/api-base.php');
    include_once('conn.php');
    include_once('config.php');
    include_once('_funcs.php');
    include_once('cache.php');
    
    include_once('get_gamevideo_list.php');
    include_once('get_video_index_blocks.php');
    include_once('get_player_info.php');
    include_once('get_player_list.php');
    include_once('get_popularvideo_list.php');
    include_once('get_condensed_list.php');
    include_once('get_schedule.php');
    include_once('get_team_info.php');
    include_once('get_teams.php');
    include_once('get_video_info.php');

    include_once('weixinjiekou.php');

    enable_cors();

    $action = _get('a');
    $id = _get_num('id');
    $pageno = _get_num('pageno');
    $date = _get('date');
    $url = _get('url');
    if ($action === "") {
        $action = _post('a');
        $url = _post('url');
    }
    $debug = _get_num('debug') == 1;

    if ($debug) {
        echo "a: $action\n";
    }
    switch ($action) {
    case 'get_player_list':
        $data = array("players" => get_player_list($pageno));
        break;
    case 'get_player_info':
        $data = array("player" => get_player_info($id),
                      "videos" => get_playervideo_list($id, $pageno));
        break;
    case 'get_video_index_blocks':
        $data = array("blocks" => get_video_index_blocks($pageno));
        break;
    case 'get_playlist':
        $data = array("list" => get_playlist($id));
        break;
    case 'get_teams':
        $data = array("conferences" => get_teams());
        break;
    case 'get_team_info':
        $data = array("team" => get_team_info($id),
                      // "videos" => get_teamvideo_list($id, $pageno),
                      "videos" => get_teamnewvideo_list($id, $pageno),
                      "images" => get_teamimages($id));
        break;
    case 'get_schedule':
        $date = _get('date');
        $buffer = _get_num('buffer');
        $dates = get_game_dates($date, 5, $buffer);
        foreach ($dates as $curr) {
        	if ($curr["current"] == true) {
        		$date = $curr["date"];
        		break;
        	}
        }
        $games = get_games($date);
        $data = array("dates" => $dates, "games" => $games);
        break;
    case 'get_dates':
        $date = _get('date');
        $data = array("dates" => get_game_dates2($date),
                      // "games" => get_games($date)
                      );
        break;
    case 'get_video_info':
        // $video = get_video_info($id);
        $video = get_newvideo_info($id);
        $relates = get_relatevideo_list($id, $video["kind"], $pageno);
        $data = array("video" => $video, "newvideo" => $newvideo, "videos" => $relates);
        break;
    case 'get_playervideo_list':
        $data = array("videos" => get_playervideo_list($id, $pageno));
        break;
    case 'get_teamvideo_list':
        // $data = array("videos" => get_teamvideo_list($id, $pageno));
        $data = array("videos" => get_teamnewvideo_list($id, $pageno));
        break;
    // case 'get_teamimage_list':
    //     $data = array("images" => get_teamimage_list($id, $pageno));
    //     break;
    case 'get_gamevideo_list':
        $data = array("videos" => get_gamevideo_list($id, $pageno));
        break;
    case 'get_popularvideo_list':
        $data = array("videos" => get_popularvideo_list($pageno));
        break;
    case 'get_condensed_list':
    		$data = array("videos" => get_condensed_list($pageno));
    		break;
    case 'get_relatevideo_list':
        $data = array("videos" => get_relatevideo_list($id, 0, $pageno));
        break;
    case 'weixinjiekou':
        $data = get_weixin_data($url);
        break;
    case 'clear_cache':
        $end = _get('end');
        $data = clear_cache("schedule", $date, $end);
        $data = clear_cache("dates", $date, $end);
        break;
    case 'test':
        $data = array("videos" => get_condensed_list($pageno));
        break;
    }
    $result = formatResult("OK", $data);
    if ($debug) {
        echo "data: \n";
        print_r($data);
        echo "result: \n";
        print_r($result);
    }
    close_connection();
    echo json_encode($result, false);
    // print_r(formatResult("OK", $data));
    // returnJson(formatResult("OK", $data));
?>
