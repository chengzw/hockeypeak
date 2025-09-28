<?php
    function _format_list($total, $pageno, $items) {
        return array("total" => $total,
                     "size" => PAGE_SIZE,
                     "count" => count($items),
                     "pageno" => $pageno,
                     "items" => $items);
    }

    function _get_limit_param($total, &$pageno) {
        $page_count = ceil($total / PAGE_SIZE);
        
        if ($pageno <= 0 || $pageno > $page_count) {
            $pageno = 1;
        }
        $start = ($pageno - 1) * PAGE_SIZE;
        return "limit " . $start . ", " . PAGE_SIZE;
    }
    function _row_to_array($row, $str_keys = [], $num_keys = []) {
        $arr = array();
        foreach ($num_keys as $key) {
            $arr[$key] = intval($row[$key]);
        }
        foreach ($str_keys as $key) {
            $arr[$key] = $row[$key];
        }
        return $arr;
    }
    function _group_rows($rows, $key, $keep_key = false) {
        $arr = array();
        foreach ($rows as $row) {
            $keyname = $row[$key];
            if (!array_key_exists($keyname, $arr)) {
                $arr[$keyname] = array();
            }
            $arr[$keyname][] = $row;
        }
        if (!$keep_key) {
            return array_values($arr);
        }
        return $arr;
    }

    function _row_to_player($row) {
        $posdefine = array(1 => "守门员", 2 => "后卫", 6 => "左边锋", 7 => "右边锋", 8 => "中锋");
        $player = _row_to_array($row, ["name", "abbr", "avatar", "cover", "teamname", "logo"], ["id", "team_id", "position", "num"]);
        $player["shoots"] = $row["shoots"] == 0 ? "左手杆" : "右手杆";
        $player["position"] = $posdefine[$player["position"]];
        return $player;
    }
    function _row_to_video($row) {
        $video = _row_to_array($row, ["name", "description", "created_date", "tags", "snapshot"], ["id", "duration"]);
        return $video;
    }
    function _row_to_newvideo($row) {
        $video = _row_to_array($row, ["name", "description", "playurl", "nhl_video_date", "snapshot"], ["id", "duration"]);
        $video["created_date"] = $video["nhl_video_date"];
        return $video;
    }
    function _row_to_condensed($row) {
        $video = _row_to_array($row, ["name", "description", "created_date", "tags", "snapshot", "host_abbr", "guest_abbr"], ["id", "duration"]);
        return $video;
    }
    function _row_to_video_detail($row) {
        $video = _row_to_array($row, ["name", "description", "created_date", "tags", "snapshot", "format", "url"], ["id", "duration"]);
        $video["type"] = "HLS";
        return $video;
    }
    function _row_to_newvideo_detail($row) {
        $video = _row_to_array($row, ["name", "url", "description", "nhl_video_date", "snapshot", "playurl"], ["id", "duration", "kind"]);
        // $video["url"] = $video["playurl"];
        return $video;
    }
    function _row_to_team($row) {
        $team = _row_to_array($row, ["name", "abbr", "english", "english_abbr", "logo"], ["id"]);
        return $team;
    }
    function _row_to_image($row) {
        $image = _row_to_array($row, ["title", "description", "created_date", "url"], ["id"]);
        return $image;
    }
    function _row_to_game($row) {
        $game = _row_to_array($row, ["playtime", "host_abbr", "guest_abbr", "host_logo", "guest_logo"], ["id", "state", "host_id", "guest_id", "host_score", "guest_score", "recap_video_id"]);
        return $game;
    }

    function formatResult($res, $data) {
        $result = array_merge(array("res" => $res), $data);
        return $result;
    }

    function _switch_host_guest($game) {
        $host_id = $game["host_id"];
        $guest_id = $game["guest_id"];
        $host_score = $game["host_score"];
        $guest_score = $game["guest_score"];
        $host_abbr = $game["host_abbr"];
        $guest_abbr = $game["guest_abbr"];
        $host_logo = $game["host_logo"];
        $guest_logo = $game["guest_logo"];
        $game["host_id"] = $guest_id;
        $game["guest_id"] = $host_id;
        $game["host_score"] = $guest_score;
        $game["guest_score"] = $host_score;
        $game["host_abbr"] = $guest_abbr;
        $game["guest_abbr"] = $host_abbr;
        $game["host_logo"] = $guest_logo;
        $game["guest_logo"] = $host_logo;
        return $game;
    }
?>
