<template>
  <div id="app">
    <div id="header" v-show="!is_tabbar"><x-header @on-click-back="on_click_back()" :left-options="left_options" style="background-color:#F7F7FA;">冰球巅峰 - {{$store.state.header_name}}</x-header></div>
    <div class="container" id="content">
      <keep-alive>
          <router-view :key="$route.fullPath" v-if="$route.meta.keepAlive" v-wechat-title='$route.meta.title'></router-view>
      </keep-alive>
      <router-view :key="$route.fullPath" v-if="!$route.meta.keepAlive" v-wechat-title='$route.meta.title'></router-view>
    </div>
    <div class="footer_blank">
    </div>
    <div id="tabbar">
      <TabBar></TabBar>
    </div>
  </div>
</template>

<script>
import TabBar from './components/template/tabbar'
import { XHeader } from 'vux'

export default {
  name: 'App',
  components: {
    TabBar,
    XHeader
  },
  computed: {
    is_tabbar: function () {
      var result = false
      var currPage = this.$route.path
      if (currPage === '/' || currPage === '/videos/index' || currPage === '/games/index' || currPage === '/teams/list' || currPage === '/players/list' || currPage === '/user/index') {
        result = true
      }
      return result
    }
  },
  methods: {
    on_click_back: function () {
      console.log(window.history, this.prevPage)
      if (!this.prevPage) {
        this.$router.push({
          path: '/games/index'
        })
      } else {
        this.$router.back(-1)
      }

      // this.gotopage(this.prevPage)
      // if (window.history.length <= 1) {
      //   this.gotopage('/games/index')
      // } else {
      //   this.$router.back(-1)
      // }
    }
  },
  mounted () {
    // 创建cnzz统计js
    const CNZZ_ID = 1279027434
    const script = document.createElement('script')
    script.src = `https://s95.cnzz.com/z_stat.php?id=${CNZZ_ID}&web_id=${CNZZ_ID}`
    script.language = 'JavaScript'
    document.body.appendChild(script)
  },
  watch: {
    '$route': {
      handler (to, from) {
        if (!from) {
          this.prevPage = '/games/index'
        } else {
          this.prevPage = from.fullPath
        }
        console.log('route change: from ', from, 'to: ', to, 'prevPage: ', this.prevPage)
        setTimeout(() => { // 避免首次获取不到window._czc
          if (window._czc) {
            let location = window.location
            let contentUrl = location.pathname + location.hash
            let refererUrl = '/'
            // 用于发送某个URL的PV统计请求，
            window._czc.push(['_trackPageview', contentUrl, refererUrl])
          }
        }, 300)
      },
      immediate: true // 首次进入页面即执行
    }
  },
  data () {
    return {
      prevPage: '',
      left_options: {
        preventGoBack: true,
        backText: ''
      }
    }
  }
}
</script>
<style>
body{
  margin: 0px;
}
a{ text-decoration:none}
img{
  vertical-align: middle;
  width: 100%;
  height: 100%;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin: 0px;
}
#tabbar{
  position: fixed;
  height: 65px;
  width: 100%;
  bottom: 0px;
}
.vux-header .vux-header-left .left-arrow:before {
    border: 2px solid #000 !important;
    border-width: 2px 0 0 2px !important;
}
.weui-tabbar{
  position: relative !important;
}

.arrow{
  width: 10px;
  height: 10px;
  border-top: 2px solid #999;
  border-right: 2px solid #999;
  position: relative;
  top: 20px;
}

.video_share,.video_dalete{
  border: 1px solid #3C746B;
  margin: 5px 0px 5px 5px;
  font-weight: normal;
  color: #3C746B;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 17px;
  background-color: #f8f8f8;
}

.footer_blank{
      height: 65px;
}

.weui-tabbar__item.weui-bar__item_on .weui-tabbar__label {
    color: #ffffff !important;
}
.weui-bar__item_on {
    background-color: #3c746b;
}
#logo{
  height: 180px;
  padding: 5px 0px;
}
#logo>img{
  width: unset;
  height: 100%;
}
.vux-header-title {
    color: #020202 !important;
    text-align: left !important;
    margin: 0 38px !important;
}
</style>
