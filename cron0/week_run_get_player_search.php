#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();

    echo "get player search start date(" . date("H:i:s") . ")... ";
    // 抓取url、pattern配置
    $pos = 0;

    $url = "https://www.nhl.com/player";
    $content = "";
    $config = array(
        'content_pattern' => array(
            "base_pattern" => 'top-players-wrapper_inner`a<footer',"result_pattern" => '`a',
            ),
        'delimitor' => 'class="player-container"',
        "item_patterns" => array(
            'nhl_player_id' => array(
                "base_pattern" => '<a href="/player/`adata-id="`b"`cdata-name="`d</h3>`e</a>',"result_pattern" => '`b',
            )
        ),
    );

    // 搜索nhl运动员id对应我们运动员id
    $conn = $Nhl->get_connection();
    $player_ids = array();
    $result = mysql_query("select id,nhl_player_id from players", $conn);
    while ($row = mysql_fetch_assoc($result)) {
        $player_ids[$row["nhl_player_id"]] = $row["id"];
    }

    // 将当前playersearch中记录排名设置为默认值10000
    mysql_query("UPDATE playersearch SET pos = 10000", $conn);
// exit();
    // 抓取ranking
    $search = getResultByConfig($url, $config, $content);

    // 写数据库
    foreach ($search as $player) {
        $pos++;
        if (array_key_exists($player["nhl_player_id"], $player_ids)) {
            $playersearch["pos"] = $pos;
            $playersearch["player_id"] =$player_ids[$player["nhl_player_id"]];

            $NhlPlayersearch = new NhlPlayersearch($playersearch);
            $Nhl->player_to_search($NhlPlayersearch);

            $Nhl->player_search_to_rank($NhlPlayersearch);
            // print_r($NhlPlayersearch);exit();
        }
    }
    echo "end date(" . date("H:i:s") . ") ";

    echo "\n********************************* date(" . date("H:i:s") . ") ******************************************\n";
    // exit();
?>