#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();
    // 获取队伍列表
    echo "get teams start date(" . date("H:i:s") . ")... ";
    $teams = getResultByConfig($teams_config['url'], $teams_config['config']);
    echo "end date(" . date("H:i:s") . ")";
    
    // $teams = array(array(
    //     'homepage' => "https://www.nhl.com/islanders",
    //     'english_short' => "NY Islanders"
    // ));
    // print_r($teams);
    // exit();
    $team_count = count($teams);
    foreach ($teams as $team_index => $team) {
        echo "\n  get [".($team_index+1)."/".$team_count."] team start date(" . date("H:i:s") . ")... ";
        if(!array_key_exists("homepage", $team)) {
            echo " homepage is null! end date(" . date("H:i:s") . ")";
            continue;
        }
        if (substr($team['homepage'], 0,4) != 'http') {
            $team['homepage'] = 'https://www.nhl.com/'.$team['homepage'];
        }
        $homepage = $team['homepage'];
        // if ($Nhl->get_team_id_by_homepage($homepage) > 0) {
        //     echo "team is exists! end date(" . date("H:i:s") . ")";
        //     continue;
        // }

        // 获取队伍详情
        $team_details = getResultByConfig($homepage, $team_config);
        if (count($team_details) > 0) {
            if (array_key_exists("img", $team_details[0])) {
                $team_details[0]['img'] = choose_image_by_resolution_ratio($team_details[0]['img']);
            }
            if (array_key_exists("english_abbr", $team_details[0])) {
                $team_details[0]['english_abbr'] = strtoupper($team_details[0]['english_abbr']);
            }
            $team = $team + $team_details[0];
            $NhlTeam = new NhlTeam($team);
            echo " nhl_team_id:" . $NhlTeam->nhl_team_id . " ";
            $Nhl->get_team_id($NhlTeam);
            
            if (array_key_exists("img", $team) && $NhlTeam->id > 0) {
                $image = array(
                    "url" => $team["img"],
                    "title" => array_key_exists("img_title", $team)?$team["img_title"]:"",
                    "description" => array_key_exists("img_description", $team)?$team["img_description"]:""
                );

                $NhlImage = new NhlImage($image);
                if ($NhlImage->nhl_image_id > 0) {
                    $Nhl->get_image_id($NhlImage);
                    if ($NhlImage->id > 0) {
                        $Nhl->image_to_team($NhlImage->id, $NhlTeam->id);
                    }
                }
            }
            if (array_key_exists("blog", $team) && count($team['blog'])>0 && $NhlTeam->id > 0) {
                $blog_count = count($team['blog']);
                echo " blog total:" . $blog_count . "... ";
                foreach ($team['blog'] as $blog_index => $value) {
                    // echo "\n    get [" . ($blog_index+1) . "/" . $blog_count . "] blog start date(" . date("H:i:s") . ")... ";
                    if (array_key_exists("video_url", $value) && array_key_exists("nhl_video_id", $value)) {
                        $video_id = get_video_id($value);
                        if ($video_id > 0) {
                            $Nhl->video_to_team($video_id, $NhlTeam->id);
                        }
                    }
                    else if (array_key_exists("img_url", $value) && array_key_exists("nhl_image_id", $value)) {
                        // echo " nhl_image_id:".$value['nhl_image_id']." ";
                        $value['url'] =  $value["img_url"];
                        $NhlImage = new NhlImage($value);
                        $Nhl->get_image_id($NhlImage);
                        if ($NhlImage->id > 0) {
                            $Nhl->image_to_team($NhlImage->id, $NhlTeam->id);
                        }
                    }
                    // echo "end date(" . date("H:i:s") . ")";
                }
            }
            else if ($NhlTeam->id == 0) {
                echo "\n       error:get NhlTeam->id is 0    ";
                print_r($team);
                print_r($NhlTeam);
                echo "\n";
            }
        }
        echo "end date(" . date("H:i:s") . ")";

        $teams[$team_index] = $team;
    }
    // $result['teams'] = $teams;
    // print_r($teams);
    // exit();
?>