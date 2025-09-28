#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();
    $url = "https://search-api.svc.nhl.com/svc/search/v2/nhl_global_en/topic/277350912?page=[page]&sort=new&type=video";
    
    // 测试部分
    // $video = array(
    //     'nhl_video_id' => 5182382,
    //     'video_url' => $nhl_host . "/video/c-5182382"
    // );
    // get_video_id($video);
    // exit();
    $video_list = array();
    $max_page = 50;
    for ($i=1; $i <= $max_page; $i++) { 
        echo "\nget page:".($i)." popular video list start date(" . date("H:i:s") . ")... ";
        $page_url = str_replace("[page]", $i, $url);
        $page_video_list = get_popular_video_list($page_url);
        if (count($page_video_list) > 0) {
            $video_list = array_merge($video_list, $page_video_list);
        }
        echo "end date(" . date("H:i:s") . ")";
    }
    // print_r($video_list);exit();
    if (count($video_list) > 0) {
        $video_list = array_reverse($video_list); 
        $video_count = count($video_list);
        foreach ($video_list as $video_index => $video) {
            echo "\n    get [" . ($video_index+1) . "/" . $video_count . "] popular start date(" . date("H:i:s") . ")... ";
            $video_id = get_video_id($video);
            if ($video_id > 0) {
                $Nhl->video_to_popular($video_id);
            }
            echo "end date(" . date("H:i:s") . ")";
        }
    }
?>