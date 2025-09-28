#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/nhl_function.php");
    include_once(__DIR__."/nhl_class.php");

    $Nhl = new Nhl();

    // 赛季、排名类型、抓取url、pattern配置
    $season = 2019;
    $rank = 0;
    $rank_type = 0;

    $url = "https://www.nhl.com/news/nhl-fantasy-hockey-top-250-rankings-players-2019-20/c-281505474?tid=277729150";
    $content = "";
    $config = array(
        'content_pattern' => array(
            "base_pattern" => '<span class="token-data" data-token-data="`a</span>',"result_pattern" => '`a',
            ),
        'delimitor' => '&quot;playerCard&quot;',
        "item_patterns" => array(
            'nhl_player_id' => array(
                "base_pattern" => '&quot;id&quot;:&quot;`a&quot;',"result_pattern" => '`a',
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

    // 将当前ranking中记录排名设置为默认值10000
    mysql_query("UPDATE playerrankings SET rank = 10000 WHERE season = $season && rank_type=$rank_type", $conn);
// exit();
    // 抓取ranking
    $ranking = getResultByConfig($url, $config, $content);

    // 写数据库
    foreach ($ranking as $player) {
        $rank++;
        if (array_key_exists($player["nhl_player_id"], $player_ids)) {
            $playerranking["rank_type"] = $rank_type;
            $playerranking["season"] = $season;
            $playerranking["rank"] = $rank;
            $playerranking["player_id"] =$player_ids[$player["nhl_player_id"]];

            $NhlPlayerranking = new NhlPlayerranking($playerranking);
            $Nhl->player_to_ranking($NhlPlayerranking);

            $Nhl->player_rank_to_search($NhlPlayerranking);
            // print_r($NhlPlayerranking);exit();
        }
    }
    // exit();
?>