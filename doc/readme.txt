tabbar图标：
大小：60*60
颜色：
  1. normal: #999999(153,153,153)
  2. selected: #FFFFFF(255,255,255)


前端：
用vue开发。
代码在SVN的路径：Codes\webapp\nhl
网站部署在47.75.200.128上



前端发布步骤：
1. npm run build
2. 将dist目录上传到hockeypeak.com根目录，并将目录名“dist”修改为“to be published”
3. 在命令行进入hockeypeak.com目录，运行目录下的publish.sh（自动备份并发布）



前端接口：
设置：config\prod.env.js，当前设置为：
API_ROOT: '"https://hockeypeak.ip008.com/nhl/"'    （hockeypeak.ip008.com在ali99服务器）
https://hockeypeak.ip008.com/nhl/interface.php?a=get_video_index_blocks


前端接口访问的数据库hockeypeak.ip008.com/nhl/conn.php：
(腾讯云rds, "root", "Vidown!!2018");
select_db("nhl");



自动更新程序：
在200.128上：crontab -l -u gaoyang
##run one##
#59 12 16 * * php /home/gaoyang/nhl/run_one_get_nhl_scores.php >> /home/gaoyang/nhl/run_one_get_nhl_scores.log 2>> /home/gaoyang/nhl/run_one_get_nhl_scores.log
##week run##
30 10 * * 1 php /home/gaoyang/nhl/week_run_get_player_search.php >> /home/gaoyang/nhl/log/week_run_get_player_search.log
##day run##
30 2 * * * php /home/gaoyang/nhl/update_schedule.php >> /home/gaoyang/nhl/log/update_schedule.log
##12h run##
0 */12 * * * php /home/gaoyang/nhl/update_nhl_player.php >> /home/gaoyang/nhl/log/update_nhl_player.log
##many run##
0 */2 * * * php /home/gaoyang/nhl/nhl_crontab.php
##3min run##
*/3 0-14 * * * php /home/gaoyang/nhl/update_nhl_scores.php > /home/gaoyang/nhl/log/update_nhl_scores1.log
*/10 14-23 * * * php /home/gaoyang/nhl/update_nhl_scores.php > /home/gaoyang/nhl/log/update_nhl_scores.log
程序说明：
数据库设置在nhl_class.php：
        $DB_NAME = "nhl";
        $DB_USER = "biofarm";
        $DB_PASSWORD ="Vidown!!2018";
        $DB_HOST = "localhost";
        $DB_CHARSET = 'utf8';


