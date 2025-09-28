<?php
    include_once('/home/services/php_utils/catcher.class.php');

    function GetNhlSchedule($start_date) {
        $schedule_url = 'https://api-web.nhle.com/v1/schedule/' . $start_date;
        // JsonCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false)
        $isUtf8 = true;
        $content = '';
        $content_pattern = null;
        $outputUtf8 = true;
        $debug = false;
        $catcher = new JsonCatcher($schedule_url, $isUtf8, $content, $content_pattern, $outputUtf8, $debug);
        $game_week = $catcher->getList('gameWeek', 'games');
        print_r($game_week);
    }

    GetNhlSchedule('2025-04-18');
?>
