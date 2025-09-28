<?php
    include_once('/home/services/php_utils/base.php');
    include_once('conn.php');
    include_once('config.php');
    include_once('_funcs.php');
    include_once('cache.php');
    
    include_once('get_gamevideo_list.php');
    include_once('get_player_info.php');
    include_once('get_player_list.php');
    include_once('get_popularvideo_list.php');
    include_once('get_schedule.php');
    include_once('get_team_info.php');
    include_once('get_teams.php');
    include_once('get_video_info.php');

    $action = "clear_cache";
    $id = 2;
    $pageno = 0;
    $date = "2020-08-08";
    switch ($action) {
    case 'get_player_list':
        $data = array("players" => get_player_list($pageno));
        break;
    case 'get_player_info':
        $data = array("player" => get_player_info($id),
                        "videos" => get_playervideo_list($id, $pageno));
        break;
    case 'get_teams':
        $data = array("conferences" => get_teams());
        break;
    case 'get_team_info':
        $data = array("team" => get_team_info($id),
                        "videos" => get_teamvideo_list($id, $pageno),
                        "images" => get_teamimage_list($id, $pageno));
        break;
    case 'get_schedule':
        break;
    case 'get_video_info':
        break;
    case 'get_playervideo_list':
        $data = array("videos" => get_playervideo_list($id, $pageno));
        break;
    case 'get_teamvideo_list':
        $data = array("videos" => get_teamvideo_list($id, $pageno));
        break;
    case 'get_teamimage_list':
        $data = array("images" => get_teamimage_list($id, $pageno));
        break;
    case 'get_gamevideo_list':
        break;
    case 'get_popularvideo_list':
        break;
    case 'clear_cache':
        // $end = _get('end');
        $end = "2020-10-03";
        $data = clear_cache("schedule", $date, $end);
        $data = clear_cache("dates", $date, $end);
    }
    close_connection();
    // returnJson(formatResult("OK", $data));
    print_r(formatResult("OK", $data));
?>
