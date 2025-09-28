<?php
	include_once('/home/services/php_utils/catcher.class.php');
    include_once("/home/services/php_utils/base.php");

    // 挑选图片分辨率
    function choose_image_by_resolution_ratio($str, $keyword="1704x960") {
        $got_str = "";
        $images = explode(",", $str);
        foreach ($images as $image_str) {
            if ($got_str == "") {
                $got_str = $image_str;
            }
            if (stripos($image_str, $keyword) !== false) {
                $got_str = $image_str;
                break;
            }
        }
        $result = GetPatternSubString1("http`a `b^^^" ,"http`a" , $got_str."  ^^^");
        return $result;
    }
    
    // 挑选需要的视频链接
    function choose_video_urls($video_urls_str) {
        $result = array();
        $video_urls_array = json_decode($video_urls_str, true);
        $max_definition = 0;
        $mp4_video = array();
        $videos = array();
        
        foreach ($video_urls_array as $video) {
            $mp4_size = GetPatternSubString1("_`aK", "`a", $video['name']);
            if ($max_definition < intval($mp4_size)) {
                $max_definition = $mp4_size;
                $mp4_video = $video;
            }
            if (stripos($video['name']."^^^", "_MOBILE^^^") != false || stripos($video['name']."^^^", "_WIRED^^^") != false) {
                $video['format'] = "hls";
                $video['quality'] = stripos($video['name']."^^^", "_MOBILE^^^") !== false?1:2;
                $videos[] = $video;
            }
        }
        if (count($mp4_video) > 0) {
            $mp4_video['width'] = $mp4_video['width'] > 0?$mp4_video['width']:GetPatternSubString1("K_`ax", "`a", $mp4_video['name']);
            $mp4_video['height'] = $mp4_video['height'] > 0?$mp4_video['height']:GetPatternSubString1($mp4_video['width']."x`a^^^", "`a", $mp4_video['name']."^^^");
            $mp4_video['format'] = "mp4";
            $mp4_video['quality'] = '0';
            $videos[] = $mp4_video;
        }
        foreach ($videos as $video) {
            $result[] = array(
                "format" => $video['format'],
                "width" => $video['width'],
                "height" => $video['height'],
                "name" => $video['name'],
                "url" => $video['url'],
                "quality" => $video['quality']
            );
        }
        // print_r($result);exit();
        return $result;
    }
    
    // 根据抓到的字符串提取时间
    function get_time_by_str($str) {
        $h = GetPatternSubString1("PT`aH" ,"`a" , $str);
        $m = GetPatternSubString1(($h == ""?"PT":"H")."`aM" ,"`a" , $str);
        $s = GetPatternSubString1(($m == ""?"PT":"M")."`aS" ,"`a" , $str);
        $result = $h*3600 + $m * 60 + $s;
        return $result;
    }

    // 获得一场比赛数据
    function get_game_by_score($scores) {
        global $nhl_host;
        global $nhl_api_host;
        global $geme_state_config;

        $result = array();
        
        $result['playtime'] = GetPatternSubString1("`aT`bZ", "`a `b", $scores['gameDate']);
        $result['playtime'] = date('Y-m-d H:i:s', strtotime($result["playtime"] . ' +8 hour'));
        $result['gamecenter'] = array_key_exists("gamecenter", $scores)?$nhl_api_host.$scores['gamecenter']:"";
        $result['state'] = (array_key_exists("status", $scores) && array_key_exists($scores["status"]['detailedState'], $geme_state_config))?$geme_state_config[$scores["status"]['detailedState']]:9;
        $result['gamePk'] = array_key_exists("gamePk", $scores)?$scores['gamePk']:0;

        if ($result['state'] == 9) {
            $str = "get_game_by_score: The state obtained is unknown! date:".date("Y-m-d H:i:s")." gamePk:".$result['gamePk']." state:".(array_key_exists("status", $scores)?$scores["status"]['detailedState']:"unknown!!!");
            echo "\n     ".$str."\n";
            report_error($str,584);
        }
        $result['host'] = array(
            "name" => $scores['teams']['home']['team']['name'],
            "code" => $scores['teams']['home']['leagueRecord']['wins']."-".$scores['teams']['home']['leagueRecord']['losses']."-".$scores['teams']['home']['leagueRecord']['ot'],
            "scores1" => 0,
            "scores2" => 0,
            "scores3" => 0,
            "scores_ot" => 0,
            "scores_so" => 0,
            "scores_t" => $scores['teams']['home']['score'],
            "shoots_so" => 0,
            "goals_so" => 0,

        );
        $result['guest'] = array(
            "name" => $scores['teams']['away']['team']['name'],
            "code" => $scores['teams']['away']['leagueRecord']['wins']."-".$scores['teams']['away']['leagueRecord']['losses']."-".$scores['teams']['away']['leagueRecord']['ot'],
            "scores1" => 0,
            "scores2" => 0,
            "scores3" => 0,
            "scores_ot" => 0,
            "scores_so" => 0,
            "scores_t" => $scores['teams']['away']['score'],
            "shoots_so" => 0,
            "goals_so" => 0,
        );

        if (isset($scores['content']['media']['epg'])) {
            foreach ($scores['content']['media']['epg'] as $video_details) {
                if ($video_details['title'] == "Extended Highlights" && count($video_details['items']) > 0) {
                    $result['recap'] = array(
                        'nhl_video_id' =>  $video_details['items'][0]['id'],
                        'video_url' =>  $nhl_host . "/video/c-" . $video_details['items'][0]['id'],
                    );
                }
                else if ($video_details['title'] == "Recap" && count($video_details['items']) > 0) {
                    $result['topicList'] = $video_details['topicList'];
                    $result['condensed'] = array(
                        'nhl_video_id' =>  $video_details['items'][0]['id'],
                        'video_url' =>  $nhl_host . "/video/c-" . $video_details['items'][0]['id'],
                    );
                }
            }
        }

        if (isset($scores['linescore'])) {
            if (array_key_exists("periods", $scores['linescore'])) {
                foreach ($scores['linescore']['periods'] as $scoring) {
                    if ($scoring['ordinalNum'] == "1st") {
                        $result["host"]['scores1'] = $scoring['home']['goals'];
                        $result["guest"]['scores1'] = $scoring['away']['goals'];
                    }
                    else if ($scoring['ordinalNum'] == "2nd") {
                        $result["host"]['scores2'] = $scoring['home']['goals'];
                        $result["guest"]['scores2'] = $scoring['away']['goals'];
                    }
                    else if ($scoring['ordinalNum'] == "3rd") {
                        $result["host"]['scores3'] = $scoring['home']['goals'];
                        $result["guest"]['scores3'] = $scoring['away']['goals'];
                    }
                    else if ($scoring['ordinalNum'] == "OT") {
                        $result["host"]['scores_ot'] = $scoring['home']['goals'];
                        $result["guest"]['scores_ot'] = $scoring['away']['goals'];
                    }
                }
            }
            if (array_key_exists("shootoutInfo", $scores['linescore'])) {
                $result["host"]['shoots_so'] = $scores['linescore']["shootoutInfo"]['home']['attempts'];
                $result["host"]['goals_so'] = $scores['linescore']["shootoutInfo"]['home']['scores'];
                $result["guest"]['shoots_so'] = $scores['linescore']["shootoutInfo"]['away']['attempts'];
                $result["guest"]['goals_so'] = $scores['linescore']["shootoutInfo"]['away']['scores'];

                $result["host"]['scores_so'] = $result["host"]['goals_so']>$result["guest"]['goals_so']?1:0;
                $result["guest"]['scores_so'] = $result["guest"]['goals_so']>$result["host"]['goals_so']?1:0;
            }
        }
        return $result;
    }

    function handle_nhl_player($player){
        global $Nhl;
        global $player_config;

        $nhl_url = $player['nhl_url'];

        for ($i=1; $i < 6; $i++) { 
            $player_details = getResultByConfig($nhl_url, $player_config);
            if (count($player_details) > 0) {
                if (array_key_exists("birthplace", $player_details[0])) {
                    $player_details[0]['birthplace'] = str_replace("\r", "", $player_details[0]['birthplace']);
                    $player_details[0]['birthplace'] = str_replace("\n", "", $player_details[0]['birthplace']);
                    for ($i=0; $i < 100; $i++) { 
                        $player_details[0]['birthplace'] = str_replace("  ", " ", $player_details[0]['birthplace']);
                        if (stripos($player_details[0]['birthplace'], "  ") === false) {
                            break 1;
                        }
                    }
                    $player_details[0]['birthplace'] = str_replace(" ,", ",", $player_details[0]['birthplace']);
                    $player_details[0]['birthplace'] = str_replace(", ", ",", $player_details[0]['birthplace']);
                }
                if (array_key_exists("height", $player_details[0])) {
                    $height_array = json_decode($player_details[0]["height"], true);
                    $player_details[0]['height'] = $height_array['value'];
                }
                if (array_key_exists("weight", $player_details[0])) {
                    $weight_array = json_decode($player_details[0]["weight"], true);
                    $player_details[0]['weight'] = $weight_array['value'];
                }
                $player = $player+$player_details[0];
                if (!array_key_exists("team_id", $player) && array_key_exists("team", $player)) {
                    $player["team_id"] = $Nhl->get_team_id_by_name($player["team"]);
                }
                
                $NhlPlayer = new NhlPlayer($player);
                echo " nhl_player_id:" . $NhlPlayer->nhl_player_id . " ";
                if ($NhlPlayer->team_id > 0) {
                    $Nhl->get_player_id($NhlPlayer);    
                }
                // echo "end date(" . date("H:i:s") . ")";
                if (array_key_exists("news", $player) && count($player['news'])>0 && $NhlPlayer->id > 0) {
                    $news_count = count($player['news']);
                    echo " news total:" . $news_count . "... ";
                    foreach ($player['news'] as $news_index => $value) {
                        // echo "\n    get [" . ($news_index+1) . "/" . $news_count . "] news start date(" . date("H:i:s") . ")... ";
                        $video_id = get_video_id($value);
                        if ($video_id > 0) {
                            $Nhl->video_to_player($video_id, $NhlPlayer->id);
                        }
                        // echo "end date(" . date("H:i:s") . ")";
                    }
                }
                else if ($NhlPlayer->id == 0) {
                    echo "\n       error:get NhlTeam->id is 0    ";
                    print_r($player);
                    print_r($NhlPlayer);
                    echo "\n";
                }
                break 1;
            }
            else {
                echo " get player details is null! :" . $i . "  ";
            }
        }

        return $player;
    }


    function handle_nhl_game($game){
        global $Nhl;

        $game['host_id'] = $Nhl->get_team_id_by_name($game['host']['name']);
        $game['guest_id'] = $Nhl->get_team_id_by_name($game['guest']['name']);
        if ($game['host_id'] > 0 && $game['guest_id'] > 0) {
            $NhlGame = new NhlGame($game);
            $Nhl->get_game_id($NhlGame);
            if ($NhlGame->id > 0) {
                $game['host']['team_id'] = $game['host_id'];
                $game['guest']['team_id'] = $game['guest_id'];
                $game['host']['game_id'] = $NhlGame->id;
                $game['guest']['game_id'] = $NhlGame->id;
                $NhlTeamgames = new NhlTeamgames($game['host']);
                $Nhl->add_teamgames($NhlTeamgames);
                $NhlTeamgames = new NhlTeamgames($game['guest']);
                $Nhl->add_teamgames($NhlTeamgames);
                
                if (array_key_exists("recap", $game)) {
                    echo "\n        get recap start date(" . date("H:i:s") . ")... ";
                    $video_id = get_video_id($game["recap"]);
                    if ($video_id > 0) {
                        $Nhl->video_to_recap($video_id, $NhlGame->id);
                    }
                    echo "end date(" . date("H:i:s") . ")";
                }
                if (array_key_exists("condensed", $game)) {
                    echo "\n        get condensed start date(" . date("H:i:s") . ")... ";
                    $video_id = get_video_id($game["condensed"]);
                    if ($video_id > 0) {
                        $Nhl->video_to_condensed($video_id, $NhlGame->id);
                    }
                    echo "end date(" . date("H:i:s") . ")";
                }
                if (array_key_exists("topicList", $game)) {
                    $game_videos = get_game_video_list($game['topicList']);
                    $video_count = count($game_videos);
                    foreach ($game_videos as $video_index => $video) {
                        echo "\n        get [" . ($video_index+1) . "/" . $video_count . "] vs. start date(" . date("H:i:s") . ")... ";
                        $video_id = get_video_id($video);
                        if ($video_id > 0) {
                            $Nhl->video_to_game($video_id, $NhlGame->id);
                        }
                        echo "end date(" . date("H:i:s") . ")";
                    }
                }
            }
        }
    }

    function get_video_id($video){
        global $Nhl;
        global $video_config;

        $video_id = 0;
        if (array_key_exists("video_url", $video) && array_key_exists("nhl_video_id", $video)) {
            // echo " nhl_video_id:".$video['nhl_video_id']." ";
            $video_id = $Nhl->get_video_id_by_nhl_video_id($video['nhl_video_id']);
            if ($video_id > 0) {
                // echo "video is exists!";
            }
            else {
                // 获取视频详情
                for ($i=1; $i < 6; $i++) { 
                    // echo " get video start:time(" . date("H:i:s") . ") ";
                    $video_details = getResultByConfig($video['video_url'], $video_config);
                    // echo " get video end:time(" . date("H:i:s") . ") ";
                    if (count($video_details) > 0 && array_key_exists("name", $video_details[0]) && $video_details[0]["name"] != "") {
                        if (array_key_exists("videourls", $video_details[0])) {
                            $video_details[0]['videourls'] = choose_video_urls($video_details[0]['videourls']);
                        }
                        $video =  $video + $video_details[0];
                        $NhlVideo = new NhlVideo($video);
                        $Nhl->get_video_id($NhlVideo);
                        $video_id = $NhlVideo->id;
                        break 1;
                    }
                    else {
                        // echo " get video name is null! :" . $i . "  ";
                    }
                }
            }
        }
    
        return $video_id;
    }

    // 获取比赛视频列表
    function get_game_video_list($topicList) {
        global $nhl_host;
        global $game_video_list_config;

        $videos_list = array();
        if ($topicList > 0) {
            $url = str_replace("[topicList]", $topicList, $game_video_list_config['url']);
            $game_video_list = getResultByConfig($url, $game_video_list_config['config']);
            if (count($game_video_list) > 0 && stripos($game_video_list[0]["title"], " vs. ") !== false) {
                $videos_list = ($game_video_list[0]['video_list']);
            }
        }
        return $videos_list;
    }

    // 获取视频列表
    function get_popular_video_list($url) {
        global $nhl_host;

        $videos_list = array(); 
        $content = file_get_contents($url);
        if (strlen($content) > 1000) {
            $got_array = json_decode($content, true);
            if (array_key_exists("docs", $got_array) && count($got_array['docs']) > 0) {
                foreach ($got_array['docs'] as $video) {
                    if (array_key_exists("asset_id", $video) && $video['asset_id'] > 0) {
                        $videos_list[] = array(
                            'nhl_video_id' => $video['asset_id'],
                            'video_url' => $nhl_host . "/video/c-" . $video['asset_id']
                        );
                    }
                }
            }
        }
        return $videos_list;
    }

    // 获取比赛比分
    function get_game_by_url($game){
        global $geme_state_config;

        $url = $game['gamecenter'];
        $content = file_get_contents($url);
        
        if (strlen($content) > 1000) {
            $got_array = json_decode($content, true);
            if (isset($got_array['gameData'])) {
                $game['playtime'] = GetPatternSubString1("`aT`bZ", "`a `b", $got_array['gameData']['datetime']['dateTime']);
                $game['state'] = (array_key_exists("status", $got_array['gameData']) && array_key_exists($got_array['gameData']["status"]['detailedState'], $geme_state_config))?$geme_state_config[$got_array['gameData']["status"]['detailedState']]:9;
                if ($game['state'] == 9) {
                    $str = "get_game_by_score: The state obtained is unknown! date:".date("Y-m-d H:i:s")." gamePk:".$game['gamePk']." state:".(array_key_exists("status", $got_array['gameData'])?$got_array['gameData']["status"]['detailedState']:"unknown!!!");
                    echo "\n     ".$str."\n";
                    report_error($str,584);
                }
                $game['host'] = array(
                    "name" => $got_array['gameData']['teams']['home']['name'],
                    "team_id" => $game['host_id'],
                    "game_id" => $game['id'],
                    "code" => "",
                    "scores1" => 0,
                    "scores2" => 0,
                    "scores3" => 0,
                    "scores_ot" => 0,
                    "scores_so" => 0,
                    "scores_t" => 0,
                    "shoots_so" => 0,
                    "goals_so" => 0,

                );
                $game['guest'] = array(
                    "name" => $got_array['gameData']['teams']['away']['name'],
                    "team_id" => $game['guest_id'],
                    "game_id" => $game['id'],
                    "code" => "",
                    "scores1" => 0,
                    "scores2" => 0,
                    "scores3" => 0,
                    "scores_ot" => 0,
                    "scores_so" => 0,
                    "scores_t" => 0,
                    "shoots_so" => 0,
                    "goals_so" => 0,
                );
            }
            if (isset($got_array['liveData']['linescore'])) {
                if (array_key_exists("periods", $got_array['liveData']['linescore'])) {
                    foreach ($got_array['liveData']['linescore']['periods'] as $scoring) {
                        if ($scoring['ordinalNum'] == "1st") {
                            $game["host"]['scores1'] = $scoring['home']['goals'];
                            $game["guest"]['scores1'] = $scoring['away']['goals'];
                        }
                        else if ($scoring['ordinalNum'] == "2nd") {
                            $game["host"]['scores2'] = $scoring['home']['goals'];
                            $game["guest"]['scores2'] = $scoring['away']['goals'];
                        }
                        else if ($scoring['ordinalNum'] == "3rd") {
                            $game["host"]['scores3'] = $scoring['home']['goals'];
                            $game["guest"]['scores3'] = $scoring['away']['goals'];
                        }
                        else if ($scoring['ordinalNum'] == "OT") {
                            $game["host"]['scores_ot'] = $scoring['home']['goals'];
                            $game["guest"]['scores_ot'] = $scoring['away']['goals'];
                        }
                    }
                }
                if (array_key_exists("shootoutInfo", $got_array['liveData']['linescore'])) {
                    $game["host"]['shoots_so'] = $got_array['liveData']['linescore']["shootoutInfo"]['home']['attempts'];
                    $game["host"]['goals_so'] = $got_array['liveData']['linescore']["shootoutInfo"]['home']['scores'];
                    $game["guest"]['shoots_so'] = $got_array['liveData']['linescore']["shootoutInfo"]['away']['attempts'];
                    $game["guest"]['goals_so'] = $got_array['liveData']['linescore']["shootoutInfo"]['away']['scores'];
    
                    $game["host"]['scores_so'] = $game["host"]['goals_so']>$game["guest"]['goals_so']?1:0;
                    $game["guest"]['scores_so'] = $game["guest"]['goals_so']>$game["host"]['goals_so']?1:0;
                }
            }
            $game["host"]['scores_t'] = $game["host"]['scores1']+$game["host"]['scores2']+$game["host"]['scores3']+$game["host"]['scores_ot']+$game["host"]['scores_so'];
            $game["guest"]['scores_t'] = $game["guest"]['scores1']+$game["guest"]['scores2']+$game["guest"]['scores3']+$game["guest"]['scores_ot']+$game["guest"]['scores_so'];
        }
    
        return $game;
    }
    
?>
