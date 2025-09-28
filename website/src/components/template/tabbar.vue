<template>
    <tabbar>

        <!-- <tabbar-item v-for="(tab, tab_index) in tabBar" :key="tab_index" :selected="currTab == '/games/index'"> -->
            <!-- <img slot="icon" :src="tab.iconPath">
            <img slot="icon-active" :src="tab.selectedIconPath">
            <span slot="label">{{tab.text}}</span> -->
        <!-- <tabbar-item> -->
      <tabbar-item :selected="currTab == '/videos/index'"  link="/videos/index">
        <img slot="icon" src="/static/images/icon_videos.png">
        <img slot="icon-active" src="/static/images/icon_videos_selected.png">
        <span slot="label">视频</span>
      </tabbar-item>
      <tabbar-item :selected="currTab == '/games/index'"  link="/games/index">
        <img slot="icon" src="/static/images/icon_game.png">
        <img slot="icon-active" src="/static/images/icon_game_selected.png">
        <span slot="label">比赛</span>
      </tabbar-item>
      <tabbar-item :selected="currTab == '/teams/list'"  link="/teams/list">
        <img slot="icon" src="/static/images/icon_team.png">
        <img slot="icon-active" src="/static/images/icon_team_selected.png">
        <span slot="label">球队</span>
      </tabbar-item>
      <tabbar-item :selected="currTab == '/players/list'"  link="/players/list">
        <img slot="icon" src="/static/images/icon_player.png">
        <img slot="icon-active" src="/static/images/icon_player_selected.png">
        <span slot="label">球星</span>
      </tabbar-item>
      <tabbar-item :selected="currTab == '/user/index'"  link="/user/index">
        <img slot="icon" src="/static/images/icon_member.png">
        <img slot="icon-active" src="/static/images/icon_member_selected.png">
        <span slot="label">我的</span>
      </tabbar-item>
    </tabbar>
</template>
<script>
import { Tabbar, TabbarItem } from 'vux'

export default {
  name: 'TabBar',
  components: {
    Tabbar,
    TabbarItem
  },
  computed: {
    currTab: function () {
      var currPage = this.$route.path
      currPage = currPage === '/' ? '/videos/index' : currPage
      // console.log(currPage)
      // currPage = currPage.indexOf('/croptype/') == -1 ? currPage : '/croptype/plants'
      // // currPage = currPage.indexOf('/fertilizer/') == -1 ? currPage : '/fertilizer/list'
      // currPage = currPage.indexOf('/pic/') == -1 ? currPage : '/analysis/fertilize'
      return currPage
    }
  }
  // data () {
  //     return {
  //         tabBar:[
  //             {
  //                 "pagePath": "/games/index",
  //                 "iconPath": "/images/icon_game.png",
  //                 "selectedIconPath": "/images/icon_game_selected.png",
  //                 "text": "比赛"
  //             },
  //             {
  //                 "pagePath": "/teams/list",
  //                 "iconPath": "/images/icon_team.png",
  //                 "selectedIconPath": "/images/icon_team_selected.png",
  //                 "text": "球队"
  //             },
  //             {
  //                 "pagePath": "/players/list",
  //                 "iconPath": "/images/icon_player.png",
  //                 "selectedIconPath": "/images/icon_player_selected.png",
  //                 "text": "球星"
  //             },
  //             {
  //                 "pagePath": "/user/index",
  //                 "iconPath": "/images/icon_member.png",
  //                 "selectedIconPath": "/images/icon_member_selected.png",
  //                 "text": "我的"
  //             }
  //         ]
  //     }
  // }
}
</script>

<style>
.ivu-menu{
    z-index: unset !important;
}
</style>
