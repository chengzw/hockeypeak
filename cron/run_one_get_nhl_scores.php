#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();
    // 获取比赛列表
    // $scores_config['start_data'] = "2020-02-16";
    $start = new DateTime($scores_config['start_data']);
    // $scores_config['end_data'] = date("Y-m-d");
    $end = new DateTime($scores_config['end_data']);
    $data_is_null_num = 0;
    foreach(new DatePeriod($start, new DateInterval('P1D'), $end) as $d){
        $date = $d->format('Y-m-d');
        $url = str_replace("[date]", $date, $scores_config['url']);
        echo "get [".$date."] scores start time(" . date("H:i:s") . ")... ";

        // 抓取一天比赛
        $content = file_get_contents($url, false);
        if (strlen($content) > 1000) {
            $data_is_null_num = 0;
            $game_list = getResultByConfig($url, $scores_config['config'], $content);

            $game_count = count($game_list);
            echo " game total:" . $game_count . "... ";
            foreach ($game_list as $game_index => $scores) {
                // echo "\n    get [" . ($game_index+1) . "/" . $game_count . "] one day game start date(" . date("H:i:s") . ")... ";
                $game = get_game_by_score($scores);
                // print_r($game);
                // exit();
                handle_nhl_game($game);
                // echo "end time(" . date("H:i:s") . ")";
            }
        }
        else {
            $data_is_null_num++;
            // echo " data id null!! ";
        }

        echo "end time(" . date("H:i:s") . ")";
        if ($data_is_null_num >= 10) {
            echo "10 day data id null!!! ";
            break;
        }
    }
?>