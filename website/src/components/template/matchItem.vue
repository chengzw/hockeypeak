<template>
    <div class="match_item" @click="gotopage('/play/index/'+video_id)">
        <div class="match_item_team">
            <div class="match_item_team_logo">
                <img :src="match.host_logo" mode="aspectFit" />
            </div>
            <div class="match_item_team_name item">{{match.host_abbr}}</div>
        </div>
        <div class="match_item_result">
            <div>{{match.host_score}} : {{match.guest_score}}</div>
            <div>
                <img src="/static/images/video.png" />
            </div>
        </div>
        <div class="match_item_team">
            <div class="match_item_team_logo">
                <img :src="match.guest_logo" mode="aspectFit" />
            </div>
            <div class="match_item_team_name item">{{match.guest_abbr}}</div>
        </div>
        <!-- <div class="list_game_time item">{{playtime}}</div> -->
    </div>
</template>
<script>
export default {
  name: 'matchItem',
  props: ['match'],
  computed: {
    playtime: function () {
        return this.match.playtime.substr(11, 5)
    },
    video_id: function () {
      return this.match.video_id
    }
  }
}
</script>

<style scoped>
.match_item{
  margin: 1px 0;
  padding: 10px 0;
  /* background-color: #eef3f8; */
  text-align: center;
  border-bottom: 2px solid #eef3f8;
}
.match_item > div{
  display: inline-block;
}
.match_item_team {
    width: 35%;
}
.match_item_result {
    width: 20%;
    font-weight: 700;
    font-size: 18px;
    /* line-height: 60px; */
    vertical-align: top;
    padding-top: 10px;
}
.match_item_result img {
    width: 20px;
    height: 20px;
}
.match_item_play_button {
    height: 30px;
}
.match_item_team_logo{
  /* max-width: 8%; */
  width: 40px;
  height: 40px;
  margin: 0 auto;
}
.match_item_team_logo img {
    width: 30px;
    height: 30px;
}
.match_item_team_name {
}
.list_game_time{
  float : left;
  width: 13%;
}
.list_game_name{
  width: 16%;
}
.list_game_score{
  width: 6%;
}
.list_game_state{
  float : right;
  margin-right: 1%;
}
</style>
