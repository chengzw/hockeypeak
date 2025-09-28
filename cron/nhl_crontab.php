<?php
$team_cmd = "php /home/gaoyang/nhl/update_nhl_team.php >> /home/gaoyang/nhl/log/update_nhl_team.log"; //12分左右
exec($team_cmd);

$scores_cmd = "php /home/gaoyang/nhl/update_nhl_scores.php >> /home/gaoyang/nhl/log/update_nhl_scores.log"; //1分多 没有比赛信息，没有抓取视频时间，(有数据预留10分)
exec($cmd);
$game_video_cmd = "php /home/gaoyang/nhl/update_nhl_game_video.php >> /home/gaoyang/nhl/log/update_nhl_game_video.log"; //1分多 没有比赛信息，没有抓取视频时间，(有数据预留10分)
exec($cmd);

$popular_video_cmd = "php /home/gaoyang/nhl/get_nhl_popular.php >> /home/gaoyang/nhl/log/get_nhl_popular.log"; //10分左右
exec($cmd);

// $player_cmd = "php /home/gaoyang/nhl/update_nhl_player.php >> /home/gaoyang/nhl/log/update_nhl_player.log"; //2小时10分左右
// exec($player_cmd);
?>