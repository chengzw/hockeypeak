#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();
    echo "get players start date(" . date("H:i:s") . ")... ";
    $players = $Nhl->get_all_player();
    $player_count = count($players);
    echo "end date(" . date("H:i:s") . ") player_count:".$player_count;

    foreach ($players as $player_index => $player) {
        echo "\n  update [".($player_index+1)."/".$player_count."] player start date(" . date("H:i:s") . ")... ";
        $player = array(
            "nhl_url" => $nhl_host . '/player/' . $player['nhl_player_id']
        );
        handle_nhl_player($player);
        echo "end date(" . date("H:i:s") . ")";
    }
    // echo json_encode($result);
    // print_r($players);
    // exit();
?>