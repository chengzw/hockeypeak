#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();
    echo "get teams start date(" . date("H:i:s") . ")... ";
    $teams = $Nhl->get_all_team();
    $team_count = count($teams);
    echo "end date(" . date("H:i:s") . ") team_count:".$team_count;


    foreach ($teams as $team_index => $team) {
        echo "\n  get [".($team_index+1)."/".$team_count."] team players start date(" . date("H:i:s") . ")... ";
        $url = str_replace("[nhl_team_id]", $team['nhl_team_id'], $players_config['url']);
        
        $content = file_get_contents($url);
        if (strlen($content) > 1000) {
            $players = getResultByConfig($url, $players_config['config'], $content);
            if (count($players) > 0) {
                $player_count = count($players);
                foreach ($players as $player_index => $player) {
                    echo "\n      get [".($player_index+1)."/".$player_count."] player start date(" . date("H:i:s") . ")... ";
                    $player = array(
                        "nhl_url" => $nhl_host . '/player/' . $player['person']['id']
                    );
        // print_r($player);
        // exit();
                    $players[$player_index] = handle_nhl_player($player);
                    echo "end date(" . date("H:i:s") . ")";
                }
            }
        }
        echo "end date(" . date("H:i:s") . ")";
    }
    // echo json_encode($result);
    // print_r($players);
    // exit();
?>