<?php

	class Nhl {
    public function __construct() {
    }
    
    public function __destruct() {
    }
    
    public function get_connection() {
        return $this->conn;
    }
    
    // ���ȫ������
    function get_all_team() {
    	$teams = get_table('teams');
    	return $teams;
    }

    // ���ȫ���˶�Ա
    function get_all_player() {
    	return get_table('players');
    }
    
    // ��ö���id
    function get_team_id($NhlTeam) {
      if ($NhlTeam->nhl_team_id > 0) {
        $sql = sprintf("select * from teams where nhl_team_id=%d", $NhlTeam->nhl_team_id);
        $row = get_row_by_sql($sql);
        // $result = mysql_query($sql, $this->conn);
        // $row = mysql_fetch_assoc($result);
        if(!$row) {
            $this->add_team($NhlTeam);
        }
        else
        {
            $NhlTeam->id = $row['id'];
        }
      }
    }

    // ��Ӷ���
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

    // ��ȡ��Ƶid
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
    
    // �����Ƶ
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

    // �޸���Ƶ״̬
    public function update_video_disabled($video_id ,$disabled) {
        $disabled = $disabled>0?1:0;
        $sql = sprintf("UPDATE videos SET disabled=%d WHERE id=%d", $disabled, $video_id);
        $result = mysql_query($sql, $this->conn);
        return $result;
    }
    
    
    // �����Ƶһ��������
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

    // ��Ƶ��������
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
    
    // ��ȡͼƬid
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

    // ��Ӷ���
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

    // ͼƬ��������
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
    
    // �������ƻ�ö���id
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

    // ������ҳ��ö���id
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
    
    // ����˶�Աid
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

    // ������ҳ����˶�Աid
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
    

    // ����˶�Ա
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

    // ��Ƶ�����˶�Ա
    public function video_to_player($video_id, $player_id) {
        if ($player_id == 0) {
            return -1; // ����
        }
        $sql = sprintf("select * from playervideos where video_id=%d and player_id=%d", $video_id, $player_id);
        $result = mysql_query($sql, $this->conn);
        $row = mysql_fetch_assoc($result);
        if(!$row) {
            $sql = sprintf("insert into playervideos(video_id, player_id) values(%d,%d)", $video_id, $player_id);
            mysql_query($sql, $this->conn);
            return 1; // ����
        }
        return 0; // δ����
    }
    
    // ����nhl_video_id�����Ƶid
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

    // ��ȡ����id
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

    // ��ȡ����id
    function update_game($NhlGame) {
        $sql = sprintf("UPDATE games SET playtime='%s', state=%d WHERE id=%d", $NhlGame->playtime, $NhlGame->state, $NhlGame->id);
        $result = mysql_query($sql, $this->conn);
    }


    // ��ӱ���
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

    // ��ӱ�������÷�
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
    
    // ��Ƶ�����ع�
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
    
    // ��Ƶ��������
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
    
    // ��Ƶ��������
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
    
    // ��Ƶ��������
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
    
    // ���δ��������
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
    
    // ���δ��������
    function get_not_finished_game($max_time="") {
        $games = array();
        $max_time = $max_time==""?date("Y-m-d H:i:s",strtotime("+4 hours")):$max_time;
        $sql = "select * from games where state!=1 && playtime<'".$max_time."'";
        $result = query_with_result($sql);
        foreach ($result as $row) {
        	$games[$row['id']] = $row;
        }
        /*
        $result = mysql_query("select * from games where state!=1 && playtime<'".$max_time."'", $this->conn);
        // $result = mysql_query("select * from games where state!=1", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            $games[$row['id']] = $row;
        }
        */
        return $games;
    }
    
    // ���û��Ƶ����
    function get_null_video_games_dates() {
        $dates = array();
        $result = mysql_query("select * from games where (recap_id=0 || condensed_id = 0) && state=1", $this->conn);
        while ($row = mysql_fetch_assoc($result)) {
            if (substr($row["playtime"],-8) == "08:00:00") {   //����ǰ8Сʱ��ʱ��������0�㣬����������ǰһ������ݼ�¼���Ҫץȡǰһ������
                $date = date('Y-m-d', strtotime($row["playtime"] . ' -9 hour'));
                $dates[$date] = $date;
            }
            $date = date('Y-m-d', strtotime($row["playtime"] . ' -8 hour'));
            $dates[$date] = $date;
        }
        
        return $dates;
    }

    // �˶�Աд������
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

    // �޸�����
    function update_playerranking($NhlPlayerranking) {
        $sql = sprintf("UPDATE playerrankings SET rank=%d WHERE id=%d", $NhlPlayerranking->rank, $NhlPlayerranking->id);
        $result = mysql_query($sql, $this->conn);
    }


    // �������
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


    // �˶�Աд����������
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

    // �޸���������
    function update_playersearch($NhlPlayersearch) {
        $sql = sprintf("UPDATE playersearch SET pos=%d WHERE id=%d", $NhlPlayersearch->pos, $NhlPlayersearch->id);
        $result = mysql_query($sql, $this->conn);
    }


    // �����������
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

    // �˶�Ա����д����������
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

    // �˶�Ա��������д������
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
