<?php
    include_once("/home/services/php_utils/string.util.php");
    
// 队伍
class NhlTeam {
    public $name;
    public $english;
    public $english_short;
    public $english_abbr;
    public $abbr;
    public $logo;
    public $homepage;
    public $nhl_team_id;

    public $id;
    
    public function __construct($team) {
        $this->name = array_key_exists("name", $team)?$team['name']:"";
        $this->english = array_key_exists("english", $team)?$team['english']:"";
        $this->english_short = array_key_exists("english_short", $team)?$team['english_short']:"";
        $this->english_abbr = array_key_exists("english_abbr", $team)?$team['english_abbr']:"";
        $this->abbr = array_key_exists("abbr", $team)?$team['abbr']:"";
        $this->logo = array_key_exists("logo", $team)?$team['logo']:"";
        $this->homepage = $team['homepage'];
        $this->nhl_team_id = array_key_exists("nhl_team_id", $team)?intval($team['nhl_team_id']):0;
        $this->id = 0;
    }
}

// 运动员
class NhlPlayer {
    public $name;
    public $english;
    public $birthday;
    public $birthplace;
    public $shoots;
    public $twitter;
    public $nhl_url;
    public $team_id;
    public $position;
    public $height;
    public $weight;
    public $num;
    public $nhl_player_id;
    public $avatar;
    public $cover;

    public $id;
    
    public function __construct($player) {
        global $position_config;
        $shoots = array_key_exists("shoots", $player) && stripos($player["shoots"], "Right") !== false?1:0;
        $position = array_key_exists("position", $player) && array_key_exists($player["position"], $position_config)?$position_config[$player["position"]]:0;
        if (array_key_exists("position", $player) && !array_key_exists($player["position"], $position_config)) {
            echo "\n     Unknown position:" . $player["position"] . " \n";
        }

        $this->name = array_key_exists("name", $player)?$player['name']:"";
        $this->english = array_key_exists("english", $player)?$player['english']:"";
        $this->birthday = array_key_exists("birthday", $player)?$player['birthday']:"";
        $this->birthplace = array_key_exists("birthplace", $player)?$player['birthplace']:"";
        $this->shoots = $shoots;
        $this->twitter = array_key_exists("twitter", $player)?$player['twitter']:"";
        $this->nhl_url = array_key_exists("nhl_url", $player)?$player['nhl_url']:"";
        $this->team_id = array_key_exists("team_id", $player)?$player['team_id']:0;
        $this->position = $position;
        $this->height = array_key_exists("height", $player)?$player['height']:0;
        $this->weight = array_key_exists("weight", $player)?$player['weight']:0;
        $this->num = array_key_exists("num", $player)?$player['num']:0;
        $this->nhl_player_id = array_key_exists("nhl_player_id", $player)?$player['nhl_player_id']:0;
        $this->avatar = array_key_exists("avatar", $player)?$player['avatar']:"";
        $this->cover = array_key_exists("cover", $player)?$player['cover']:"";
    }
}
    
// 视频
class NhlVideo {
    public $name;
    public $description;
    public $created_date;
    public $tags;
    public $snapshot;
    // public $kind;
    public $nhl_video_id;
    public $duration;

    public $id;
    
    public function __construct($video) {
        if (array_key_exists("duration", $video) && stripos($video["duration"], "PT") !== false) {
            $video["duration"] = get_time_by_str($video["duration"]);
        }
        if (array_key_exists("tags", $video)) {
            $tags_array = json_decode($video["tags"], true);
            $video["tags"] = implode(",", $tags_array);
        }
        $this->name =  array_key_exists("name", $video)?$video['name']:"";
        $this->description =  array_key_exists("description", $video)?$video['description']:"";
        $this->created_date =  array_key_exists("created_date", $video)?$video['created_date']:"";
        $this->tags =  array_key_exists("tags", $video)?$video['tags']:"";
        $this->snapshot =  array_key_exists("snapshot", $video)?$video['snapshot']:"";
        // $this->kind =  array_key_exists("kind", $video)?$video['kind']:0;
        $this->nhl_video_id =  array_key_exists("nhl_video_id", $video)?$video['nhl_video_id']:0;
        $this->duration =  array_key_exists("duration", $video)?$video['duration']:0;
        $this->videourls =  array_key_exists("videourls", $video)?$video['videourls']:array();
        $this->id = 0;
    }
}
    
// 视频分辨率
class NhlVideourl {
    public $format;
    public $width;
    public $height;
    public $name;
    public $url;
    public $quality;

    public $id;
    
    public function __construct($videourl) {
        $this->format =  array_key_exists("format", $videourl)?$videourl['format']:"";
        $this->width =  array_key_exists("width", $videourl) && $videourl['width']>0?$videourl['width']:0;
        $this->height =  array_key_exists("height", $videourl) && $videourl['height']>0?$videourl['height']:0;
        $this->name =  array_key_exists("name", $videourl)?$videourl['name']:"";
        $this->url =  array_key_exists("url", $videourl)?$videourl['url']:"";
        $this->quality =  array_key_exists("quality", $videourl)?$videourl['quality']:0;
    }
}

// 图片
class NhlImage {
    public $title;
    public $description;
    public $url;
    public $created_date;
    public $nhl_image_id;

    public $id;
    
    public function __construct($image) {
        if (array_key_exists("url", $image) && !array_key_exists("nhl_image_id", $image)) {
            $image['nhl_image_id'] = GetPatternSubString1("/photos/`a/" ,"`a" , $image['url']);
        }
        $this->title =  array_key_exists("title", $image)?$image['title']:"";
        $this->description =  array_key_exists("description", $image)?$image['description']:"";
        $this->url =  array_key_exists("url", $image)?$image['url']:"";
        $this->created_date =  array_key_exists("created_date", $image)?$image['created_date']:date("Y-m-d");
        $this->nhl_image_id =  array_key_exists("nhl_image_id", $image)?intval($image['nhl_image_id']):"";
        $this->id = 0;
    }
}

// 比赛
class NhlGame {
    public $playtime;
    public $host_id;
    public $guest_id;
    public $gamecenter;
    public $recap_id;
    public $condensed_id;
    public $gamePk;
    public $state;

    public $id;
    
    public function __construct($game) {
        $this->gamePk = array_key_exists("gamePk", $game)?$game['gamePk']:0;
        if (array_key_exists("playtime", $game)) {
            $game['playtime'] = date('Y-m-d H:i:s', strtotime($game["playtime"] . ' +8 hour'));
        }
        else {
            $game['playtime'] = "";
        }
        $this->playtime = $game['playtime'];
        // $this->host = array_key_exists("host", $game)?$game['host']:"";
        $this->host_id = array_key_exists("host_id", $game)?$game['host_id']:0;
        // $this->guest = array_key_exists("guest", $game)?$game['guest']:"";
        $this->guest_id = array_key_exists("guest_id", $game)?$game['guest_id']:0;
        $this->gamecenter = array_key_exists("gamecenter", $game)?$game['gamecenter']:"";
        $this->recap_id = array_key_exists("recap_id", $game)?$game['recap_id']:0;
        $this->condensed_id = array_key_exists("condensed_id", $game)?$game['condensed_id']:0;
        $this->state = array_key_exists("state", $game)?$game['state']:0;
        $this->id = array_key_exists("id", $game) && $game['id']>0?$game['id']:0;
    }
}

// 比赛队伍得分
class NhlTeamgames {
    public $game_id;
    public $team_id;
    public $code;
    public $scores1;
    public $scores2;
    public $scores3;
    public $scores_ot;
    public $scores_so;
    public $scores_t;
    public $shoots_so;
    public $goals_so;

    public $id;
    
    public function __construct($teamgame) {
        $this->game_id = array_key_exists("game_id", $teamgame)?$teamgame['game_id']:0;
        $this->team_id = array_key_exists("team_id", $teamgame)?$teamgame['team_id']:0;
        $this->code = array_key_exists("code", $teamgame)?$teamgame['code']:"";
        $this->scores1 = array_key_exists("scores1", $teamgame)?$teamgame['scores1']:0;
        $this->scores2 = array_key_exists("scores2", $teamgame)?$teamgame['scores2']:0;
        $this->scores3 = array_key_exists("scores3", $teamgame)?$teamgame['scores3']:0;
        $this->scores_ot = array_key_exists("scores_ot", $teamgame)?$teamgame['scores_ot']:0;
        $this->scores_so = array_key_exists("scores_so", $teamgame)?$teamgame['scores_so']:0;
        $this->scores_t = array_key_exists("scores_t", $teamgame)?$teamgame['scores_t']:0;
        $this->shoots_so = array_key_exists("shoots_so", $teamgame)?$teamgame['shoots_so']:0;
        $this->goals_so = array_key_exists("goals_so", $teamgame)?$teamgame['goals_so']:0;
        $this->id = 0;
    }
}
   
// 运动员排行
class NhlPlayerranking {
    public $rank_type;
    public $season;
    public $rank;
    public $player_id;

    public $id;
    
    public function __construct($player) {
        $this->rank_type = array_key_exists("rank_type", $player)?$player['rank_type']:"0";
        $this->season = array_key_exists("season", $player)?$player['season']:"";
        $this->rank = array_key_exists("rank", $player)?$player['rank']:10000;
        $this->player_id = $player['player_id'];
        $this->id = 0;
    }
}

   
// 运动员搜索排行
class NhlPlayersearch {
    public $pos;
    public $player_id;

    public $id;
    
    public function __construct($player) {
        $this->pos = array_key_exists("pos", $player)?$player['pos']:10000;
        $this->player_id = $player['player_id'];
        $this->id = 0;
    }
}

/**********************************************/

class Nhl {
    public function __construct() {
        // 128
        $DB_NAME = "nhl";
        $DB_USER = "biofarm";
        $DB_PASSWORD ="Vidown!!2018";
        $DB_HOST = "localhost";
        $DB_CHARSET = 'utf8';

        // 110
        // $DB_NAME = "nhl";
        // $DB_USER = "gaoyang";
        // $DB_PASSWORD ="gy@vidown";
        // $DB_HOST = "db.vidown.cn";
        // $DB_CHARSET = 'utf8';

        // 连接数据库        
        $this->conn = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
        $selected = mysql_select_db($DB_NAME, $this->conn) or die("Could not select database.");
        mysql_set_charset($DB_CHARSET, $this->conn);
        
    }
    
    public function __destruct() {
        mysql_close($this->conn);
    }
    
    public function get_connection() {
        return $this->conn;
    }
    
    // 获得全部队伍
    function get_all_team() {
        $teams = array();
        $result = mysql_query("select * from teams", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            $teams[] = $row;
        }
        return $teams;
    }

    // 获得全部运动员
    function get_all_player() {
        $players = array();
        $result = mysql_query("select * from players", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            $players[] = $row;
        }
        return $players;
    }
    
    // 获得队伍id
    function get_team_id($NhlTeam) {
        if ($NhlTeam->nhl_team_id > 0) {
            $sql = sprintf("select * from teams where nhl_team_id=%d", $NhlTeam->nhl_team_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $this->add_team($NhlTeam);
            }
            else
            {
                $NhlTeam->id = $row['id'];
            }
        }
    }

    // 添加队伍
    public function add_team($NhlTeam) {
        $name = mysql_real_escape_string($NhlTeam->name);
        $english = mysql_real_escape_string($NhlTeam->english);
        $english_short = mysql_real_escape_string($NhlTeam->english_short);
        $english_abbr = mysql_real_escape_string($NhlTeam->english_abbr);
        $abbr = mysql_real_escape_string($NhlTeam->abbr);
        $logo = mysql_real_escape_string($NhlTeam->logo);
        $homepage = mysql_real_escape_string($NhlTeam->homepage);
        $nhl_team_id = $NhlTeam->nhl_team_id;
        
        // echo "\n    add one team:" . $name . "  ...";
        $sql = sprintf("insert into teams(name, english, english_short, english_abbr, abbr, logo, homepage, nhl_team_id) 
        values('%s','%s','%s','%s','%s','%s','%s',%d)",$name, $english, $english_short, $english_abbr, $abbr, $logo, $homepage, $nhl_team_id);
        if (mysql_query($sql, $this->conn)) {
            $NhlTeam->id = mysql_insert_id($this->conn);
            // echo "Team_id: ".$NhlTeam->id;
        }
        else {
            echo "add failed!!!\n";
            exit();
        }
    }

    // 获取视频id
    public function get_video_id($NhlVideo) {
        if ($NhlVideo->nhl_video_id > 0) {
            $sql = sprintf("select * from videos where nhl_video_id=%d", $NhlVideo->nhl_video_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $this->add_video($NhlVideo);
            }
            else
            {
                $NhlVideo->id = $row['id'];
            }
        }
    }
    
    // 添加视频
    public function add_video($NhlVideo) {
        $name = mysql_real_escape_string($NhlVideo->name);
        $description = mysql_real_escape_string($NhlVideo->description);
        $created_date = $NhlVideo->created_date;
        $tags = mysql_real_escape_string($NhlVideo->tags);
        $snapshot = mysql_real_escape_string($NhlVideo->snapshot);
        // $kind = $NhlVideo->kind;
        $nhl_video_id = $NhlVideo->nhl_video_id;
        $duration = $NhlVideo->duration;
        download_nhl_image($snapshot);

        // echo "\n    add one video:" . $name . "  ...";
        $sql = sprintf("insert into videos(name, description, created_date, tags, snapshot, nhl_video_id, duration) 
        values('%s','%s','%s','%s','%s',%d,%d)",$name, $description, $created_date, $tags, $snapshot, $nhl_video_id, $duration);
        if (count($NhlVideo->videourls) > 0 && mysql_query($sql, $this->conn)) {
            $NhlVideo->id = mysql_insert_id($this->conn);
            // echo "video_id: ".$NhlVideo->id;
            if ($NhlVideo->id > 0) {
                foreach ($NhlVideo->videourls as $videourl) {
                    $NhlVideourl = new NhlVideourl($videourl);
                    $this->add_videourl($NhlVideourl, $NhlVideo->id);
                }
            }
        }
        else if (count($NhlVideo->videourls) == 0) {
            var_dump($NhlVideo);
        }
        else {
            echo "add failed!!!\n";
            exit();
        }
    }
    
    // 添加视频一种清晰度
    public function add_videourl($NhlVideourl, $video_id) {
        $format =  mysql_real_escape_string($NhlVideourl->format);
        $width =  ($NhlVideourl->width);
        $height =  ($NhlVideourl->height);
        $name =  mysql_real_escape_string($NhlVideourl->name);
        $url =  mysql_real_escape_string($NhlVideourl->url);
        $quality =  ($NhlVideourl->quality);

        // echo "\n    add one videourl:" . $name . "  ...";
        $sql = sprintf("insert into videourls(format, width, height, name, url, quality, video_id) 
        values('%s',%d,%d,'%s','%s',%d,%d)", $format, $width, $height, $name, $url, $quality, $video_id);
        if (mysql_query($sql, $this->conn)) {
            $NhlVideourl->id = mysql_insert_id($this->conn);
            // echo "videourl_id: ".$NhlVideourl->id;
        }
        else {
            echo "add failed!!!\n";
            exit();
        }
    }

    // 视频关联队伍
    public function video_to_team($video_id, $team_id) {
        if ($team_id == 0) {
            return false;
        }
        $sql = sprintf("select * from teamvideos where video_id=%d and team_id=%d", $video_id, $team_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into teamvideos(video_id, team_id) values(%d,%d)", $video_id, $team_id);
            mysql_query($sql, $this->conn);
        }
        
    }
    
    // 获取图片id
    function get_image_id($NhlImage) {
        if ($NhlImage->nhl_image_id > 0) {
            $sql = sprintf("select * from images where nhl_image_id='%s'", $NhlImage->nhl_image_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $this->add_image($NhlImage);
            }
            else
            {
                // echo "image is exists!";
                $NhlImage->id = $row['id'];
                if (($row['title'] == "" && $NhlImage->title != "") || ($row['description'] == "" && $NhlImage->description != "")) {
                    $title =  mysql_real_escape_string($NhlImage->title);
                    $description =  mysql_real_escape_string($NhlImage->description);
                    $sql = sprintf("UPDATE images SET title='%s', description='%s' WHERE id=%d", $title, $description, $NhlImage->id);
                    $result = mysql_query($sql, $this->conn);
                }
            }
        }
    }

    // 添加队伍
    public function add_image($NhlImage) {
        $title = mysql_real_escape_string($NhlImage->title);
        $description = mysql_real_escape_string($NhlImage->description);
        $url = mysql_real_escape_string($NhlImage->url);
        $created_date = ($NhlImage->created_date);
        $nhl_image_id = ($NhlImage->nhl_image_id);
        
        // echo "\n    add one image:" . $name . "  ...";
        $sql = sprintf("insert into images(title, description, url, created_date, nhl_image_id) 
        values('%s','%s','%s','%s',%d)",$title, $description, $url, $created_date, $nhl_image_id);
        if (mysql_query($sql, $this->conn)) {
            $NhlImage->id = mysql_insert_id($this->conn);
            // echo "image_id: ".$NhlImage->id;
        }
        else {
            echo "add failed!!!\n";
            exit();
        }
    }

    // 图片关联队伍
    public function image_to_team($image_id, $team_id) {
        if ($team_id == 0) {
            return false;
        }
        $sql = sprintf("select * from teamimages where image_id=%d and team_id=%d", $image_id, $team_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into teamimages(image_id, team_id) values(%d,%d)", $image_id, $team_id);
            mysql_query($sql, $this->conn);
        }
        
    }
    
    // 根据名称获得队伍id
    function get_team_id_by_name($name) {
        $sql = sprintf("select * from teams where name='%s' || english='%s' || english_short='%s'", $name, $name, $name);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $team_id = 0;
        }
        else
        {
            $team_id = $row['id'];
        }
        return $team_id;
    }

    // 根据主页获得队伍id
    function get_team_id_by_homepage($homepage) {
        $sql = sprintf("select * from teams where homepage='%s'", $homepage);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $team_id = 0;
        }
        else
        {
            $team_id = $row['id'];
        }
        return $team_id;
    }
    
    // 获得运动员id
    function get_player_id($NhlPlayer) {
        if ($NhlPlayer->nhl_player_id > 0) {
            $sql = sprintf("select * from players where nhl_player_id=%d", $NhlPlayer->nhl_player_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $this->add_player($NhlPlayer);
            } else {
                $NhlPlayer->id = $row['id'];
            }
        }
    }

    // 根据主页获得运动员id
    function get_player_id_by_nhl_url($nhl_url) {
        $sql = sprintf("select * from players where nhl_url='%s'", $nhl_url);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $player_id = 0;
        }
        else
        {
            $player_id = $row['id'];
        }
        return $player_id;
    }
    

    // 添加运动员
    public function add_player($NhlPlayer) {
        $name = mysql_real_escape_string($NhlPlayer->name);
        $birthday = mysql_real_escape_string($NhlPlayer->birthday);
        $birthplace = mysql_real_escape_string($NhlPlayer->birthplace);
        $shoots = ($NhlPlayer->shoots);
        $twitter = mysql_real_escape_string($NhlPlayer->twitter);
        $nhl_url = mysql_real_escape_string($NhlPlayer->nhl_url);
        $team_id = ($NhlPlayer->team_id);
        $position = mysql_real_escape_string($NhlPlayer->position);
        $height = ($NhlPlayer->height);
        $weight = ($NhlPlayer->weight);
        $num = ($NhlPlayer->num);
        $nhl_player_id = ($NhlPlayer->nhl_player_id);
        $avatar = mysql_real_escape_string($NhlPlayer->avatar);
        $cover = mysql_real_escape_string($NhlPlayer->cover);
        
        // echo "\n    add one player:" . $name . "  ...";
        $sql = sprintf("insert into players(name, birthday, birthplace, shoots, twitter, nhl_url, team_id, position, height, weight, num, nhl_player_id, avatar, cover) 
        values('%s','%s','%s',%d,'%s','%s',%d,'%s',%d,%d,%d,%d,'%s','%s')",$name, $birthday, $birthplace, $shoots, $twitter, $nhl_url, $team_id, $position, $height, $weight, $num, $nhl_player_id, $avatar, $cover);
        if (mysql_query($sql, $this->conn)) {
            $NhlPlayer->id = mysql_insert_id($this->conn);
            // echo "player_id: ".$NhlPlayer->id;
        }
        else {
            echo "add failed!!!\n";
            exit();
        }
    }

    // 视频关联运动员
    public function video_to_player($video_id, $player_id) {
        if ($player_id == 0) {
            return -1; // 出错
        }
        $sql = sprintf("select * from playervideos where video_id=%d and player_id=%d", $video_id, $player_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into playervideos(video_id, player_id) values(%d,%d)", $video_id, $player_id);
            mysql_query($sql, $this->conn);
            return 1; // 新增
        }
        return 0; // 未新增
    }
    
    // 根据nhl_video_id获得视频id
    function get_video_id_by_nhl_video_id($nhl_video_id) {
        $sql = sprintf("select * from videos where nhl_video_id=%d", $nhl_video_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $video_id = 0;
        }
        else
        {
            $video_id = $row['id'];
        }
        return $video_id;
    }

    // 获取比赛id
    function get_game_id($NhlGame) {
        if ($NhlGame->gamePk > 0) {
            $sql = sprintf("select * from games where gamePk=%d", $NhlGame->gamePk);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $this->add_game($NhlGame);
            }
            else
            {
                $NhlGame->id = $row['id'];
                if ($NhlGame->state == 0 || $NhlGame->state == 2 || $NhlGame->state != $row['state']) {
                    $this->update_game($NhlGame);
                }
            }
        }
    }

    function delete_game($game_id) {
        mysql_query("DELETE FROM games WHERE id=" . $game_id, $this->conn);
        $this->delete_teamgames($game_id);
    }

    function delete_teamgames($game_id) {
        mysql_query("DELETE FROM teamgames WHERE game_id=" . $game_id, $this->conn);
    }

    // 获取比赛id
    function update_game($NhlGame) {
        $sql = sprintf("UPDATE games SET playtime='%s', state=%d WHERE id=%d", $NhlGame->playtime, $NhlGame->state, $NhlGame->id);
        $result = mysql_query($sql, $this->conn);
    }


    // 添加比赛
    public function add_game($NhlGame) {
        $playtime =  ($NhlGame->playtime);
        $host_id =  ($NhlGame->host_id);
        $guest_id =  ($NhlGame->guest_id);
        $gamecenter =  mysql_real_escape_string($NhlGame->gamecenter);
        $recap_id =  ($NhlGame->recap_id);
        $condensed_id =  ($NhlGame->condensed_id);
        $state =  ($NhlGame->state);
        $gamePk =  ($NhlGame->gamePk);
        
        $sql = sprintf("insert into games(playtime, host_id, guest_id, gamecenter, recap_id, condensed_id, state, gamePk) 
        values( '%s', %d, %d, '%s', %d, %d, %d, %d)",$playtime, $host_id, $guest_id, $gamecenter, $recap_id, $condensed_id, $state, $gamePk);
        if (mysql_query($sql, $this->conn)) {
            $NhlGame->id = mysql_insert_id($this->conn);
        }
        else {
            echo "game add failed!!!\n";
            print_r($sql);
            print_r($NhlGame);
            exit();
        }
    }

    // 添加比赛队伍得分
    public function add_teamgames($NhlTeamgames) {
        if ($NhlTeamgames->game_id > 0 && $NhlTeamgames->team_id > 0) {
            $game_id = $NhlTeamgames->game_id;
            $team_id = $NhlTeamgames->team_id;
            $code = $NhlTeamgames->code;
            $scores1 = $NhlTeamgames->scores1;
            $scores2 = $NhlTeamgames->scores2;
            $scores3 = $NhlTeamgames->scores3;
            $scores_ot = $NhlTeamgames->scores_ot;
            $scores_so = $NhlTeamgames->scores_so;
            $scores_t = $NhlTeamgames->scores_t;
            $shoots_so = $NhlTeamgames->shoots_so;
            $goals_so = $NhlTeamgames->goals_so;

            $sql = sprintf("select * from teamgames where game_id=%d && team_id=%d", $NhlTeamgames->game_id, $NhlTeamgames->team_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $sql = sprintf("insert into teamgames(game_id, team_id, code, scores1, scores2, scores3, scores_ot, scores_so, scores_t, shoots_so, goals_so) 
                values(%d, %d, '%s', %d, %d, %d, %d, %d, %d, %d, %d)",$game_id, $team_id, $code, $scores1, $scores2, $scores3, $scores_ot, $scores_so, $scores_t, $shoots_so, $goals_so);
                if (mysql_query($sql, $this->conn)) {
                    $NhlTeamgames->id = mysql_insert_id($this->conn);
                    echo " new teamgames id:" . $NhlTeamgames->id . "! ";
                }
                else {
                    echo "teamgames add failed!!!\n";
                    print_r($sql);
                    print_r($NhlTeamgames);
                    exit();
                }
            }
            else {
                $sql = sprintf("UPDATE teamgames SET code='%s', scores1=%d, scores2=%d, scores3=%d, scores_ot=%d, scores_so=%d, scores_t=%d, shoots_so=%d, goals_so=%d WHERE id=%d", 
                                $code, $scores1, $scores2, $scores3, $scores_ot, $scores_so, $scores_t, $shoots_so, $goals_so, $row['id']);
                mysql_query($sql, $this->conn);
            }
        }
    }
    
    // 视频关联回顾
    public function video_to_recap($video_id, $game_id) {
        if ($game_id == 0) {
            return false;
        }
        $sql = sprintf("select * from recaps where video_id=%d and game_id=%d", $video_id, $game_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into recaps(video_id, game_id) values(%d,%d)", $video_id, $game_id);
            mysql_query($sql, $this->conn);
            $recap_id = mysql_insert_id($this->conn);

            $sql = sprintf("UPDATE games SET recap_id=%d WHERE id=%d", $recap_id, $game_id);
            $result = mysql_query($sql, $this->conn);
        }
        
    }
    
    // 视频关联集锦
    public function video_to_condensed($video_id, $game_id) {
        if ($game_id == 0) {
            return false;
        }
        $sql = sprintf("select * from condensed where video_id=%d and game_id=%d", $video_id, $game_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into condensed(video_id, game_id) values(%d,%d)", $video_id, $game_id);
            mysql_query($sql, $this->conn);
            $condensed_id = mysql_insert_id($this->conn);

            $sql = sprintf("UPDATE games SET condensed_id=%d WHERE id=%d", $condensed_id, $game_id);
            $result = mysql_query($sql, $this->conn);
        }
        
    }
    
    // 视频关联比赛
    public function video_to_game($video_id, $game_id) {
        if ($game_id == 0) {
            return false;
        }
        $sql = sprintf("select * from gamevideos where video_id=%d and game_id=%d", $video_id, $game_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into gamevideos(video_id, game_id) values(%d,%d)", $video_id, $game_id);
            mysql_query($sql, $this->conn);
        }
    }
    
    // 视频关联热门
    public function video_to_popular($video_id) {
        $sql = sprintf("select * from popularvideos where video_id=%d", $video_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(is_array($row) && count($row) > 0) {
            mysql_query("DELETE FROM popularvideos WHERE video_id=" . $video_id, $this->conn);
        }
        $sql = sprintf("insert into popularvideos(video_id) values(%d)", $video_id);
        mysql_query($sql, $this->conn);
    }
    
    // 获得未结束比赛
    function get_not_finished_game_dates($max_time="",$min_time="") {
        $game_dates = array();
        $max_time = $max_time==""?date("Y-m-d H:i:s",strtotime("+4 hours")):$max_time;
        $min_time = $min_time!=""?(" && playtime>" . $min_time):"";
        $result = mysql_query("select playtime from games where state!=1 && state!=3".$min_time." && playtime<'".$max_time."'", $this->conn);
        // $result = mysql_query("select * from games where state!=1", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            $time = strtotime($row['playtime']) - 3600*8;
            $time2 = strtotime($row['playtime']) - 3600*12;
            $date = date('Y-m-d',$time);
            $date2 = date('Y-m-d',$time2);
            $game_dates[$date] = $date;
            $game_dates[$date2] = $date2;
        }
        return $game_dates;
    }
    
    // 获得未结束比赛
    function get_not_finished_game($max_time="") {
        $games = array();
        $max_time = $max_time==""?date("Y-m-d H:i:s",strtotime("+4 hours")):$max_time;
        $result = mysql_query("select * from games where state!=1 && playtime<'".$max_time."'", $this->conn);
        // $result = mysql_query("select * from games where state!=1", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            $games[$row['id']] = $row;
        }
        return $games;
    }
    
    // 获得没视频比赛
    function get_null_video_games_dates() {
        $dates = array();
        $result = mysql_query("select * from games where (recap_id=0 || condensed_id = 0) && state=1", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            if (substr($row["playtime"],-8) == "08:00:00") {   //当提前8小时的时间正好是0点，这组数据在前一天的数据记录里，需要抓取前一天数据
                $date = date('Y-m-d', strtotime($row["playtime"] . ' -9 hour'));
                $dates[$date] = $date;
            }
            $date = date('Y-m-d', strtotime($row["playtime"] . ' -8 hour'));
            $dates[$date] = $date;
        }
        
        return $dates;
    }

    // 运动员写入排名
    function player_to_ranking($NhlPlayerranking) {
        if ($NhlPlayerranking->player_id > 0 && $NhlPlayerranking->season > 0) {
            $sql = sprintf("select * from playerrankings where player_id=%d && season=%d && rank_type=%d", $NhlPlayerranking->player_id, $NhlPlayerranking->season, $NhlPlayerranking->rank_type);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            // var_dump(!$row);exit();
            if(!$row) {
                $this->add_playerranking($NhlPlayerranking);
            }
            else {
                $NhlPlayerranking->id = $row['id'];
                $this->update_playerranking($NhlPlayerranking);
            }
        }
    }

    // 修改排名
    function update_playerranking($NhlPlayerranking) {
        $sql = sprintf("UPDATE playerrankings SET rank=%d WHERE id=%d", $NhlPlayerranking->rank, $NhlPlayerranking->id);
        $result = mysql_query($sql, $this->conn);
    }


    // 添加排名
    public function add_playerranking($NhlPlayerranking) {
        $rank_type =  ($NhlPlayerranking->rank_type);
        $season =  ($NhlPlayerranking->season);
        $rank =  ($NhlPlayerranking->rank);
        $player_id =  ($NhlPlayerranking->player_id);
        
        $sql = sprintf("insert into playerrankings(rank_type, season, rank, player_id) 
        values( %d, %d, %d, %d)",$rank_type, $season, $rank, $player_id);
        if (mysql_query($sql, $this->conn)) {
            $NhlPlayerranking->id = mysql_insert_id($this->conn);
        }
        else {
            echo "playerranking add failed!!!\n";
            print_r($sql);
            print_r($NhlPlayerranking);
            exit();
        }
    }


    // 运动员写入搜索排名
    function player_to_search($NhlPlayersearch) {
        if ($NhlPlayersearch->player_id > 0) {
            $sql = sprintf("select * from playersearch where player_id=%d", $NhlPlayersearch->player_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            // var_dump(!$row);exit();
            if(!$row) {
                $this->add_playersearch($NhlPlayersearch);
            }
            else {
                $NhlPlayersearch->id = $row['id'];
                $this->update_playersearch($NhlPlayersearch);
            }
        }
    }

    // 修改搜索排名
    function update_playersearch($NhlPlayersearch) {
        $sql = sprintf("UPDATE playersearch SET pos=%d WHERE id=%d", $NhlPlayersearch->pos, $NhlPlayersearch->id);
        $result = mysql_query($sql, $this->conn);
    }


    // 添加搜索排名
    public function add_playersearch($NhlPlayersearch) {
        $pos =  ($NhlPlayersearch->pos);
        $player_id =  ($NhlPlayersearch->player_id);
        
        $sql = sprintf("insert into playersearch(pos, player_id) values( %d, %d)", $pos, $player_id);
        if (mysql_query($sql, $this->conn)) {
            $NhlPlayersearch->id = mysql_insert_id($this->conn);
        }
        else {
            echo "playersearch add failed!!!\n";
            print_r($sql);
            print_r($NhlPlayersearch);
            exit();
        }
    }

    // 运动员排名写入搜索排名
    function player_rank_to_search($NhlPlayerranking) {
        if ($NhlPlayerranking->player_id > 0) {
            $sql = sprintf("select * from playersearch where player_id=%d", $NhlPlayerranking->player_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            if(!$row) {
                $playersearch["player_id"] = $NhlPlayerranking->player_id;
                $NhlPlayersearch = new NhlPlayersearch($playersearch);
                $this->add_playersearch($NhlPlayersearch);
            }
        }
    }

    // 运动员搜索排名写入排名
    function player_search_to_rank($NhlPlayersearch) {
        if ($NhlPlayersearch->player_id > 0) {
            $sql = sprintf("select * from playerrankings where player_id=%d", $NhlPlayersearch->player_id);
            $result = mysql_query($sql, $this->conn);
            $row = mysql_fetch_assoc($result);
            // var_dump(!$row);exit();
            if(!$row) {
                $playerranking["player_id"] = $NhlPlayersearch->player_id;
    
                $NhlPlayerranking = new NhlPlayerranking($playerranking);
                $this->add_playerranking($NhlPlayerranking);
            }
        }
    }

    private $conn;
}

?>