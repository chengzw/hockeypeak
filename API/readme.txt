
接口说明
统一接口：https://parse.vidowncdn.top/nhl/interface.php
正式部署的时候，域名替换为wxapp.ip008.com
参数说明：
a：具体要取什么数据？可以是以下内容：
   'get_player_list'：获取运动员列表
   'get_player_info': 获取运动员详情
   'get_teams': 获取全部队伍（返回数组）
   'get_team_info': 获取一个队伍详情
   'get_schedule': 获取赛程
   'get_video_info': 获取视频详情
   'get_playervideo_list'：获取用户视频列表
   'get_teamvideo_list'：获取球队视频列表
   'get_gamevideo_list': 获取比赛视频列表
   'get_popularvideo_list': 获取热门视频列表
   'get_relatevideo_list'：获取一个视频的相关视频列表
id: 获取详情时候的id。例如获取运动员详情时，id表示player_id
pageno：获取列表的页数。没有这个参数，或者这个参数不合法，都相当于是1。
date：获取赛程时，指定要获取哪天的赛程。例如：2019-10-10

返回数据说明：
list：凡是函数名带list的，返回list格式的内容。list定义如下：
{
  size: 每页有几个item？
  count：本次返回了几个item？
  total：总共有几个item？（可用于计算页数）
  pageno：此次返回的数据，是第几页？（有可能跟请求的pageno不一样）
  items：数组
}

// TODO
1. 相关视频的算法还需要改进
2. 需要填写更多的球星名字
