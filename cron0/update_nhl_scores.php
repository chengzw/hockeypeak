#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    
    // 抓比分的逻辑，可以直接就确定为：找所有未结束的比赛，且比赛时间小于当前时间加上4个小时（以后设置只在上午运行）
    $Nhl = new Nhl();
    echo "get games start date(" . date("H:i:s") . ")... ";
    $game_list = $Nhl->get_not_finished_game();
    $game_count = count($game_list);
    echo "end date(" . date("H:i:s") . ") game_count:".$game_count;

    foreach ($game_list as $game_index => $game) {
        echo "\n  update [".($game_index+1)."/".$game_count."] game start date(" . date("H:i:s") . ")... ";
        $game = get_game_by_url($game);
        $NhlTeamgames = new NhlTeamgames($game['host']);
        $Nhl->add_teamgames($NhlTeamgames);
        $NhlTeamgames = new NhlTeamgames($game['guest']);
        $Nhl->add_teamgames($NhlTeamgames);
        echo "end date(" . date("H:i:s") . ")";
    }
?>