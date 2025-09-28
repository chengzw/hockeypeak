<template>
    <div>
        <div v-if="data_is_error" class="data_is_null_str">
        数据异常，请尝试刷新！
        </div>
        <div v-else>
            <div id="player" v-if="player_info.name">
                <div id="player_image">
                    <img id="player_info_cover" :src="player_info.cover" />
                    <div id="player_info_avatar">
                        <img  class="player_avatar" :src="player_info.avatar" />
                    </div>
                </div>
                <div id="player_info">
                <div>{{player_info.name}}</div>
                <div>{{player_info.num}}号 | {{player_info.position}}</div>
                <div>
                    <img :src="'/static/images/team_logos/' + player_info.team_id +'.svg'" />
                    <div class="player_info_team_name">{{player_info.teamname}}</div>
                </div>
                </div>
            </div>
            <div id="video_list">
                <div v-for="(video, idx) in playerVideos" :key="idx">
                  <listVideo :video='video'></listVideo>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import listVideo from '@/components/template/listVideo'

export default {
  name: 'PlayerInfo',
  components: {
    listVideo
  },
  methods: {
    // 获取备用数据
    getSpareData: function (page) {
      if (this.total > this.count) {
        this.$ajax.get('/interface.php?a=get_playervideo_list&id=' + this.player_info.id + '&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var videos = result.data.videos
            var playerVideos = []
            for (var itemIndex in videos.items) {
              videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
              videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)

              playerVideos.push(videos.items[itemIndex])
            }
            this.spareData = playerVideos
          }
        })
      } else {
        this.spareData = []
      }
    },

    onReachBottom: function () {
      if (this.$route.path.indexOf('/players/info') < 0) {
        return ''
      }
      var spareDataNum = Number(this.spareData.length)
      if (spareDataNum > 0) {
        var players = this.spareData
        var playerVideos = this.playerVideos
        for (var itemIndex in players) {
          playerVideos.push(players[itemIndex])
        }

        this.pageno = this.pageno + 1
        this.count = spareDataNum + this.count

        this.getSpareData(this.pageno + 1)
      }
    },

    scrollBottom () {
      // 滚动到页面底部时
      const el = document.getElementById('content')
      const windowHeight = window.screen.height
      const scrollTop = document.documentElement.scrollTop || document.body.scrollTop
      const contentHeight = el.clientHeight
      const toBottom = contentHeight - windowHeight - scrollTop
      if (toBottom < 10 && !this.finished && !this.loading) {
        this.onReachBottom()
      }
    }
  },
  mounted () {
    window.addEventListener('scroll', this.scrollBottom, true)
  },
  created () {
    var playerId = this.$router.currentRoute.params.player_id
    if (playerId === undefined) {
      playerId = 3
    }
    if (playerId > 0) {
      this.$ajax.get('/interface.php?a=get_player_info&id=' + playerId).then((result) => {
        if (result.data.res === 'OK') {
          for (var itemIndex in result.data.videos.items) {
            var video = result.data.videos.items[itemIndex]
            result.data.videos.items[itemIndex].duration = this.formatSeconds(video.duration)
            result.data.videos.items[itemIndex].snapshot = this.getNewImagePath(video.snapshot)
          }
          result.data.player.cover = this.getNewImagePath(result.data.player.cover)
          result.data.player.avatar = this.getNewImagePath(result.data.player.avatar)
          this.player_info = result.data.player
          this.playerVideos = result.data.videos.items
          this.pageno = 1
          this.total = result.data.videos.total
          this.count = result.data.videos.count
          this.$store.state.header_name = this.player_info.name

          this.getSpareData(this.pageno + 1)
        } else {
          this.data_is_error = true
        }
      })
    }
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    return {
      data_is_error: false,
      spareData: [],
      player_info: [],
      playerVideos: [],
      pageno: 0,
      total: 0,
      count: 0
    }
  }
}
</script>

<style scoped>
#player{
    margin-bottom: 50px;
}
#player_image{
    width: 100%;
}
#player_image>img{
    width: 100%;
}
.player_avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    position: absolute;
    bottom: 50%;
}
#player_info_avatar {
    position: relative;
    text-align: center;
    margin-right: 60px;
    height: 40px;
}
#player_info img {
width: 40px;
height: 30px;
vertical-align: middle;
}
.player_info_team_name {
display: inline-block;
}

</style>
