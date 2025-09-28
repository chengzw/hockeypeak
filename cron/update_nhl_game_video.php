#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");
    
// <!-- 抓视频，可以抓已经结束，但是还没有game video的比赛，这个应该可以select出来吧 -->
    $Nhl = new Nhl();
    $datas = $Nhl->get_null_video_games_dates();
    
    foreach ($datas as $date) {
        $url = str_replace("[date]", $date, $scores_config['url']);
        echo "\nget [".$date."] scores start time(" . date("H:i:s") . ")... ";

        // 抓取一天比赛
        $content = file_get_contents($url, false);
        if (strlen($content) > 1000) {
            $data_is_null_num = 0;
            $game_list = getResultByConfig($url, $scores_config['config'], $content);

            $game_count = count($game_list);
            echo " game total:" . $game_count . "... ";
            foreach ($game_list as $game_index => $scores) {
                $game = get_game_by_score($scores);
                $NhlGame = new NhlGame($game);
                $Nhl->get_game_id($NhlGame);
                if ($NhlGame->id > 0) {
                    if (array_key_exists("recap", $game)) {
                        // echo "\n        get recap start date(" . date("H:i:s") . ")... ";
                        $video_id = get_video_id($game["recap"]);
                        if ($video_id > 0) {
                            $Nhl->video_to_recap($video_id, $NhlGame->id);
                        }
                        // echo "end date(" . date("H:i:s") . ")";
                    }
                    if (array_key_exists("condensed", $game)) {
                        // echo "\n        get condensed start date(" . date("H:i:s") . ")... ";
                        $video_id = get_video_id($game["condensed"]);
                        if ($video_id > 0) {
                            $Nhl->video_to_condensed($video_id, $NhlGame->id);
                        }
                        // echo "end date(" . date("H:i:s") . ")";
                    }
                    if (array_key_exists("topicList", $game)) {
                        $game_videos = get_game_video_list($game['topicList']);
                        $video_count = count($game_videos);
                        foreach ($game_videos as $video_index => $video) {
                            // echo "\n        get [" . ($video_index+1) . "/" . $video_count . "] vs. start date(" . date("H:i:s") . ")... ";
                            $video_id = get_video_id($video);
                            if ($video_id > 0) {
                                $Nhl->video_to_game($video_id, $NhlGame->id);
                            }
                            // echo "end date(" . date("H:i:s") . ")";
                        }
                    }
                }
            }
        }
        echo "end time(" . date("H:i:s") . ")";
    }
?>