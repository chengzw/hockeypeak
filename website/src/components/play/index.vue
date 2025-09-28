<template>
    <div>
        <loading :show="loading"></loading>
        <div class="sharebox" v-if="sharebox" @click="sharebox=false"></div>
        <toast v-model="showPositionValue" type="text" width="50%" :time="3000" text="请使用浏览器分享功能分享本视频"></toast>
        <div v-if="data_is_error" class="data_is_null_str">
        数据异常，请尝试刷新！
        </div>
        <div v-else>
            <div id="video_info_box">
                <div id="video_details">
                  <div class='player-box'>
                    <iframe allowfullscreen="true" name="iframe0" :src="videosrc" frameborder="0" seamless="" scrolling="no"></iframe>
                  </div>
                </div>
                <div id="video_operation">
                    <button class="video_collection" @click="clickCollectionVideo(video_info.id)">
                        <div><img class="video_icon" :src="operation.collection?'/static/images/icon_saved.png':'/static/images/icon_save.png'" /></div>
                        <div>收藏</div>
                    </button>
                    <button class="video_forward" @click="video_reminders()">
                        <div><img class="video_icon" src="/static/images/share_wechat.png" /></div>
                        <div>分享</div>
                    </button>
                </div>
            </div>
            <div id="video_list">
                <div v-for="(video, idx) in relate_videos" :key="idx">
                  <listVideo :video='video'></listVideo>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/* eslint-disable no-eval */

// import 'video.js/dist/video-js.css'
// import videojs from 'video.js'
import listVideo from '@/components/template/listVideo'
import { Toast } from 'vux'

// import Hlsjs from 'hls.js'

export default {
  name: 'PlayIndex',
  components: {
    listVideo,
    Toast
  },
  methods: {
    video_reminders: function () {
      if (this.$store.state.is_weixin) {
        this.sharebox = true
      } else {
        this.showPositionValue = true
      }
    },
    // 添加视频到最近播放
    videoToLately: function () {
      var videoInfo = this.video_info
      var videoId = this.video_info.id
      videoInfo.preserveTime = this.getNowTime()
      var latelyVideos = this.getLocalData('latelyVideos')
      // console.log('videos: ', latelyVideos)
      if (!latelyVideos) {
        latelyVideos = []
      } else {
        latelyVideos = eval(latelyVideos)
      }

      var index = this.getArrayIndexKeyByValue(latelyVideos, 'id', videoId)

      if (index > -1) {
        latelyVideos.splice(index, 1)
      }
      latelyVideos.push(videoInfo)

      if (latelyVideos.length > 30) {
        latelyVideos.splice(0, 1)
      }
      this.setLocalData('latelyVideos', JSON.stringify(latelyVideos))
    },
    // 点击收藏
    clickCollectionVideo: function (videoId) {
      if (videoId < 1) {
        videoId = this.video_info.id
      }
      var operation = this.operation
      var collectionVideos = this.getLocalData('collectionVideos')
      collectionVideos = eval(collectionVideos)
      collectionVideos = collectionVideos == null ? [] : collectionVideos

      var index = this.getArrayIndexKeyByValue(collectionVideos, 'id', videoId)
      if (operation.collection) {
        if (index > -1) {
          collectionVideos.splice(index, 1)
        }
        operation.collection = false
      } else {
        if (index === -1) {
          var collectionVideo = this.video_info
          collectionVideo.preserveTime = this.getNowTime()
          collectionVideos.push(collectionVideo)
        }
        operation.collection = true
      }
      this.operation = operation

      this.setLocalData('collectionVideos', JSON.stringify(collectionVideos))
    },
    getVideo () {
      // this.myVideo = videojs('nhl_video', {
      //   bigPlayButton: true,
      //   textTrackDisplay: false,
      //   posterImage: false,
      //   errorDisplay: false,
      //   hls: {
      //     withCredentials: true
      //   }
      // })
    },
    playVideo (src) {
      // let video = this.$refs.videoElement// 定义挂载点
      // if (Hlsjs.isSupported()) {
      //   this.hlsjs = new Hlsjs()
      //   this.hlsjs.loadSource(src)
      //   this.hlsjs.attachMedia(video)
      //   this.hlsjs.on(Hlsjs.Events.MANIFEST_PARSED, () => {
      //     console.log('加载成功')
      //     console.log(this.$refs.videoElement)
      //     this.$refs.videoElement.play()
      //   })
      //   this.hlsjs.on(Hlsjs.Events.ERROR, (event, data) => {
      //     // 监听出错事件
      //     console.log('加载失败')
      //     console.log(event, data)
      //   })
      // } else {
      //   this.$message.error('不支持的格式')
      // }
    }
  },
  // watch: {
  //   '$route' (to, from) { // 监听路由是否变化
  //     console.log('route change, from ' + from.name + ' to ' + to.name)
  //     if (from.name === 'PlayIndex' && to.name === 'PlayIndex') {
  //       // this.$router.go(0)
  //       // this.$router.push(to.path)
  //     }
  //     // if (this.$route.params.video_id) { // 判断条件1  判断传递值的变化
  //     //   window.location.reload()
  //     // }
  //   }
  // },
  // activated () {
  //   console.log('activated. this video: ', this.video_info.video_id, ', param: ', this.$route.params.video_id)
  //   console.log(this.video_info)
  // },
  // mounted () {
  //   console.log('mounted: ', this.$route.params.video_id)
  //   console.log('video: ', this.myVideo)
  // },
  created () {
    console.log('created: ', this.$route.params.video_id)
    console.log('video: ', this.myVideo)
    // var data, option
    // wxapi.wxRegister(data, option) // data是微信配置信息，option是分享要配置的内容
    var videoId = this.$router.currentRoute.params.video_id
    if (videoId === undefined) {
      videoId = 3
    }
    if (videoId > 0) {
      this.loading = true
      this.$ajax.get('/interface.php?a=get_video_info&id=' + videoId).then((result) => {
        if (result.data.res === 'OK') {
          var video = result.data.video
          var videos = result.data.videos
          var relateVideos = this.relate_videos
          var operation = this.operation

          for (var itemIndex in videos.items) {
            videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
            videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)
            relateVideos.push(videos.items[itemIndex])
          }
          var collectionVideos = this.getLocalData('collectionVideos')
          collectionVideos = eval(collectionVideos)
          var index = this.getArrayIndexKeyByValue(collectionVideos, 'id', videoId)

          if (index > -1) {
            operation.collection = true
          }

          this.operation = operation
          video.snapshot = this.getNewImagePath(video.snapshot)
          // console.log('video: ', video)
          this.video_info = video
          this.relate_videos = relateVideos
          this.total = Number(videos.total)
          this.count = Number(videos.count)
          this.pageno = this.pageno + 1
          this.loading = false
          // this.video_info.type = 'url'

          this.videosrc = 'https://vip.czplayer.com/hockey/play.php?format=' + video.format + '&url=' + video.url

          // this.myVideo.src({
          //   src: video.url,
          //   type: 'application/x-mpegURL'
          // })
          // this.myVideo.play()
          // this.playVideo(video.url)

          this.videoToLately()
          this.$store.state.header_name = this.video_info.name
          this.wxapi.initWxApi(window.location.href, this.video_info.snapshot, this.video_info.name, this.video_info.name)
        } else {
          this.data_is_error = true
          this.loading = false
        }
      })
    }
    // this.$nextTick(() => {
    //   this.getVideo()
    // })
  },
  destroyed () {
    console.log('destroyed')
    this.$store.state.header_name = ''
    // this.myVideo.dispose()
  },
  data () {
    return {
      showPositionValue: false,
      videosrc: '',
      sharebox: false,
      loading: false,
      operation: {
        collection: false
      },
      myVideo: null,
      data_is_error: false,
      video_info: [],
      pageno: 0,
      total: 0,
      count: 0,
      relate_videos: []
    }
  }
}
</script>

<style scoped>
.player-box {
    width: 100%;
    background: #000;
    position:relative;
    height: 0;
    padding-bottom:56.25%;    /*需要用padding来维持16:9比例,也就是9除以16*/
}
.player-box iframe {
    position: absolute;
    top:0;
    left: 0;
    width: 100%;
    height: 100%;
}
#nhl_video {
    width: 100%;
    min-height: 211px;
}
#video_info_box{
  margin-bottom: 20px;
}
#video_details{
  margin-bottom: 20px;
}

#video_play{
  width: 100%;
}
#video_operation{
  text-align: center;
}
#video_operation > button{
    height: 100%;
    line-height: 70%;
    display: inline-block;
    width: 27.3%;
    margin: 0px 3%;
    padding: 0px;
    background-color: #FFF;
    border: unset;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    -webkit-user-modify: read-write-plaintext-only;
    outline: none;
}

.video_icon{
  width: 30px;
  height: 30px;
}
.sharebox{position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    background-image: url(/static/images/share_wechat_image.png);
    background-repeat: no-repeat;
    background-position: top center;
    background-size: contain;
    z-index: 999;
  }
</style>
