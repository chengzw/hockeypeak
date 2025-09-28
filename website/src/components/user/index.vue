<template>
<div>
  <div id="logo"><img src="/static/images/hockeypeak.png" alt=""></div>
  <div id="user_info" v-show="false">
  </div>
  <div id="user_relevant">
    <group>
      <cell title="我的收藏" is-link link="/user/collection" value-align='left'>
        <img slot="icon" width="20" src="/static/images/icon_saved.png">
      </cell>
      <cell title="最近播放" is-link link="/user/lately" value-align='left'>
        <img slot="icon" width="20" src="/static/images/icon_play.png">
      </cell>
    </group>
  </div>
</div>
</template>

<script>
import { Group, Cell } from 'vux'

export default {
  name: 'UserIndex',
  components: {
    Group,
    Cell
  },
  created () {
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  }
}
</script>

<style scoped>

#user_avatarurl,#user_nickname{
  line-height: 100%;
  display: inline-block;
}
#user_nickname{
  margin-left: 10px
}
#user_relevant img{
    width: 28px;
    height: 28px;
    vertical-align: middle;
    border-radius: 50%;
    position: relative;
    top: -2px;
}

/* #user_relevant>div{
    text-align: left;
  margin: 0px 5px;
  height: 65px;
  line-height: 65px;
  border-top: 1px solid #5f677a;
} */

.user_relevant_name{
  display: inline-block;
  position: relative;
}

.right_arrow{
position: relative;
transform: rotate(45deg);
margin-top: 7%;
margin-right: 5%;
float: right;
}
</style>
