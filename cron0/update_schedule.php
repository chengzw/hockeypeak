#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    
    // 抓比分的逻辑，可以直接就确定为：找所有未结束的比赛，且比赛时间小于当前时间加上4个小时（以后设置只在上午运行）
    $Nhl = new Nhl();
    echo "get games start date(" . date("H:i:s") . ")... ";
    $max_time = date("Y-m-d H:i:s",strtotime("+1 year"));
    $games = $Nhl->get_not_finished_game($max_time);
    echo "end date(" . date("H:i:s") . ") ";

    $start_data = date("Y-m-d",strtotime("-1 day"));
    $end_data = date("Y-m-d",strtotime("+1 year"));
    $start = new DateTime($start_data);
    $end = new DateTime($end_data);
    $is_update = false;
    $start_update_time = "";
    $end_update_time = "";

    $data_is_null_num = 0;
    foreach(new DatePeriod($start, new DateInterval('P1D'), $end) as $d){
        $date = $d->format('Y-m-d');
        $url = str_replace("[date]", $date, $scores_config['url']);
        echo "\nget [".$date."] scores start time(" . date("H:i:s") . ")... ";

        // 抓取一天比赛
        $content = file_get_contents($url, false);
        if (strlen($content) > 1000) {
            $game_list = getResultByConfig($url, $scores_config['config'], $content);
            $game_count = count($game_list);
            echo " game total:" . $game_count . "... ";
            foreach ($game_list as $game_index => $scores) {
                $game = get_game_by_score($scores);
                $NhlGame = handle_nhl_game($game);

                if (array_key_exists($NhlGame->id, $games)) {
                    echo "\n    update game id:" . $NhlGame->id . " host:" . $game['host']['name'] . " guest:" . $game['guest']['name'] . "  playtime:" . $NhlGame->playtime . "... ";
                    $games[$NhlGame->id]["is_update"] = true;
                    if ($NhlGame->playtime != $games[$NhlGame->id]['playtime']) {
                        $is_update = true;
                        if ($start_update_time != "") {
                            $start_update_time = (strtotime($NhlGame->playtime)<strtotime($start_update_time))?$NhlGame->playtime:$start_update_time;
                        }else {
                            $start_update_time = $NhlGame->playtime;
                        }
                        if ($end_update_time != "") {
                            $end_update_time = (strtotime($NhlGame->playtime)>strtotime($end_update_time))?$NhlGame->playtime:$end_update_time;
                        }else {
                            $end_update_time = $NhlGame->playtime;
                        }
                    }
                } else {
                    echo "\n    add game id:" . $NhlGame->id . " host:" . $game['host']['name'] . " guest:" . $game['guest']['name'] . "  playtime:" . $NhlGame->playtime . "... ";
                    $is_update = true;
                    if ($start_update_time != "") {
                        $start_update_time = (strtotime($NhlGame->playtime)<strtotime($start_update_time))?$NhlGame->playtime:$start_update_time;
                    }else {
                        $start_update_time = $NhlGame->playtime;
                    }
                    if ($end_update_time != "") {
                        $end_update_time = (strtotime($NhlGame->playtime)>strtotime($end_update_time))?$NhlGame->playtime:$end_update_time;
                    }else {
                        $end_update_time = $NhlGame->playtime;
                    }
                }
            }
        }
        else {
            $data_is_null_num++;
            echo " data id null!! ";
        }

        echo "end time(" . date("H:i:s") . ")";
        if ($data_is_null_num >= 10) {
            echo "10 day data id null!!! ";
            break;
        }
    }

    foreach ($games as $id => $game) {
        if (!array_key_exists("is_update", $game)) {
            $is_update = true;
            if ($start_update_time != "") {
                $start_update_time = (strtotime($game['playtime'])<strtotime($start_update_time))?$game['playtime']:$start_update_time;
            }else {
                $start_update_time = $game['playtime'];
            }
            if ($end_update_time != "") {
                $end_update_time = (strtotime($game['playtime'])>strtotime($end_update_time))?$game['playtime']:$end_update_time;
            }else {
                $end_update_time = $game['playtime'];
            }
            $Nhl->delete_game($game["id"]);
            echo "\n    delete game id:" . $game["id"] . " host_id:" . $game['host_id'] . " guest_id:" . $game['guest_id'] . "  playtime:" . $game['playtime'] . "... ";
        }
    }

    if ($is_update) {
        $start_update_date = date('Y-m-d',strtotime($start_update_time));
        $end_update_date = date('Y-m-d',strtotime($end_update_time));
        echo "\nhttps://parse.vidowncdn.top/nhl/interface.php?a=clear_cache&date=" . $start_update_date . "&end=" . $end_update_date;
        $cdn_update_content = file_get_contents("https://parse.vidowncdn.top/nhl/interface.php?a=clear_cache&date=" . $start_update_date . "&end=" . $end_update_date);
        $ip008_update_content = file_get_contents("https://parse.ip008.com/nhl/interface.php?a=clear_cache&date=" . $start_update_date . "&end=" . $end_update_date);
        echo "\ncdn=>" . $cdn_update_content . "\nip008=>" . $ip008_update_content;
    }
    echo "\n********************************* date(" . date("H:i:s") . ") ******************************************\n";
?>