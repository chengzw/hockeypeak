<?php

function get_new_game_list() {
    // $sql = "select games.playtime, games.id as game_id, videos.id as video_id, videos.duration, videos.snapshot, teamhost.name as host_abbr, teamhost.id as host_id, teamguest.name as guest_abbr, teamguest.id as guest_id, gamehost.scores_t as host_score, gameguest.scores_t as guest_score from games, recaps, videos, teams as teamhost, teams as teamguest, teamgames as gamehost, teamgames as gameguest where recaps.game_id = games.id and recaps.video_id = videos.id and teamhost.id = games.host_id and teamguest.id = games.guest_id and games.state = 1 and gamehost.game_id = games.id and gamehost.team_id = teamhost.id and gameguest.game_id = games.id and gameguest.team_id = teamguest.id order by games.playtime desc limit 8";
    $sql = "select games.playtime, games.id as game_id, games.recap_video_id as video_id, teamhost.name as host_abbr, teamhost.id as host_id, teamguest.name as guest_abbr, teamguest.id as guest_id, gamehost.scores_t as host_score, gameguest.scores_t as guest_score from games, teams as teamhost, teams as teamguest, teamgames as gamehost, teamgames as gameguest where games.recap_video_id > 0 and teamhost.id=games.host_id and teamguest.id=games.guest_id and games.state=1 and gamehost.game_id=games.id and gamehost.team_id=teamhost.id and gameguest.game_id=games.id and gameguest.team_id=teamguest.id order by games.playtime desc limit 8";
    // echo "$sql\n";
    $list = array("title" => "今日赛况",
                "type" => "match",
                "pos" => 1000,
                "more" => "/games/index",
                "videos" => array());
    $rows = get_rows_by_sql($sql);
    foreach ($rows as $row) {
        // 2024-10-31
        // nhl的home和away是反的，这里反过来
        $row = _switch_host_guest($row);
        $row["name"] = substr($row["playtime"], 0, 11) . $row["host_abbr"] . "vs" . $row["guest_abbr"] . "(" . $row["host_score"] . ":" . $row["guest_score"] . ")";
        $row["host_logo"] = "/static/images/team_logos/" . $row["host_id"] . ".svg";
        $row["guest_logo"] = "/static/images/team_logos/" . $row["guest_id"] . ".svg";
        $list["videos"][] = _row_to_array($row, array("name", "snapshot", "playtime", "host_abbr", "host_logo", "guest_abbr", "guest_logo"), array("video_id", "duration", "host_score", "guest_score"));
    }
    $list["count"] = count($list["videos"]);
    return $list;
}

function get_playlist($list_id) {
    $sql = "select lists.name as listname, lists.listtype, lists.pos, listvideos.*, videos.name, videos.snapshot, videos.duration from lists, listvideos, videos where lists.id = listvideos.list_id and listvideos.video_id = videos.id and lists.id = $list_id order by listvideos.pos desc";
    $rows = get_rows_by_sql($sql);
    if (count($rows) == 0) {
        return array();
    }
    $list = array("title" => $rows[0]["listname"],
                "type" => $rows[0]["listtype"] == 0 ? "video" : "movie",
                "videos" => array());
    foreach ($rows as $row) {
        $list["videos"][] = _row_to_array($row, array("name", "snapshot"), array("video_id", "duration"));
    }
    return $list;
}
function get_video_index_blocks() {
    $sql = "select lists.name as title, lists.pos as listpos, lists.listtype, listvideos.list_id, videos.id, videos.name, videos.snapshot, videos.duration from lists, listvideos, videos where lists.id = listvideos.list_id and listvideos.video_id = videos.id and lists.pos > 0 order by lists.pos desc, listvideos.pos desc";
    $times = array();
    $times[] = time();
    $rows = get_rows_by_sql($sql);
    $times[] = time();
    $blocks = array();
    $nhls = get_new_game_list();
    $times[] = time();
    foreach ($rows as $row) {
        if (!array_key_exists("list".$row["list_id"], $blocks)) {
            $blocks["list".$row["list_id"]] = array(
                        "title" => $row["title"],
                        "list_id" => $row["list_id"],
                        "more" => "/videos/list/" . $row["list_id"],
                        "pos" => $row["listpos"],
                        "type" => $row["listtype"] == 0 ? "video" : "movie",
                        "allvideos" => array());
        }
        $blocks["list".$row["list_id"]]["allvideos"][] = _row_to_array($row, array("name", "snapshot"), array("id", "duration"));
    }
    foreach($blocks as $list_id => $list) {
        $keys = array_rand($list["allvideos"], 6);
        $blocks[$list_id]["videos"] = array();
        foreach ($keys as $key) {
            $blocks[$list_id]["videos"][] = $list["allvideos"][$key];
        }
        unset($blocks[$list_id]["allvideos"]);
    }
    $blocks = array_values($blocks);
    if (count($nhls["videos"]) > 0) {
        $blocks0 = array();
        $blocks0[] = $nhls;
        $blocks = array_merge($blocks0, $blocks);
    }
    $times[] = time();
    $blocks[0]["info"] = $times;
    return $blocks;
    $video = array(
        "created_date" => '2020-11-10',
        "description" => 'Members of the United States Marine Corps join NHL Tonight to celebrate its 245th birthday',
        "duration" => 248,
        "id" => 19638,
        "name" => 'USMC 245th Birthday',
        "snapshot" => 'https://cms.nhl.bamgrid.com/images/photos/319608446/1136x640/cut.jpg',
        "tags" => 'NHL Network,NHL Tonight'
    );
    $movie = array(
        "id" => 19649,
        "title" => '飞吧冰球',
        "snapshot" => '//pic7.iqiyipic.com/image/20200908/b4/7e/v_141019859_m_601_m9_180_236.jpg',
        "director" => '林纬台',
        "actor" => '黄彦 韩振魁 梓聿',
        "intro" => '《飞吧冰球》讲述了齐齐哈尔两代冰球人传承冰球精神，追梦圆梦，争创“亚洲最佳冰球城市”的故事。影片用生动的形象和艺术表现，展示了齐齐哈尔市百万人上冰雪、丰富多彩的冰球赛事和冰雪器材市场的繁荣兴盛，片中激情滑雪、雪地放飞和室外温泉等北国冰雪特色场景，全方位呈现鹤城得天独厚的自然资源和人文情怀。'
    );

    $video_block = array("title" => '视频板块',
        "type" => 'video', "videos" => array($video, $video, $video, $video, $video, $video));

    $movie_block = array("title" => '电影板块',
        "type" => 'movie', "movies" => array($movie, $movie, $movie, $movie));

    return array($video_block, $movie_block);
  }
?>
