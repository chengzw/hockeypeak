<template>
  <div>
    <div id="team">
      <div id="team_images">
        <swiper>
            <swiper-item v-for="(image, index) in images" :key="index">
              <img :src="image.url" class="slide-image"  :preview="index" />
            </swiper-item>
        </swiper>
      </div>
      <div id="team_info">
        <div id="team_info_left">
          <img :src="team_info.logo" mode="aspectFit" />
        </div>
        <div id="team_info_right">
          <div>{{team_info.name}}</div>
          <div>{{team_info.english}}</div>
        </div>
      </div>
    </div>
    <div id="video_list">
      <div v-for="(video, idx) in team_videos" :key="idx">
        <listVideo :video='video'></listVideo>
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable no-unused-vars */
/* eslint-disable camelcase */
import { Previewer, Swiper, SwiperItem } from 'vux'
import listVideo from '@/components/template/listVideo'

export default {
  name: 'TeamInfo',
  components: {
    Previewer,
    Swiper,
    SwiperItem,
    listVideo
  },
  methods: {
    // 点击图片
    bindImage: function (current) {
      console.log(current)
    },

    onReachBottom: function () {
      if (this.$route.path.indexOf('/teams/info') < 0) {
        return ''
      }
      var spareDataNum = Number(this.spareData.length)
      if (spareDataNum > 0) {
        var teams = this.spareData
        var team_videos = this.team_videos
        for (var itemIndex in teams) {
          team_videos.push(teams[itemIndex])
        }

        this.pageno = this.pageno + 1
        this.count = spareDataNum + this.count

        this.getSpareData(this.pageno + 1)
      }
    },

    // 获取备用数据
    getSpareData: function (page) {
      if (this.total > this.count) {
        this.$ajax.get('/interface.php?a=get_teamvideo_list&pageno=' + page + '&id=' + this.team_info.id).then((result) => {
          if (result.data.res === 'OK') {
            var teams = result.data.videos.items
            var teamList = []
            for (var itemIndex in teams) {
              var video = teams[itemIndex]
              teams[itemIndex].duration = this.formatSeconds(video.duration)
              teams[itemIndex].snapshot = this.getNewImagePath(video.snapshot)

              teamList.push(teams[itemIndex])
            }

            this.spareData = teamList
          }
        })
      } else {
        this.spareData = []
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
    var teamId = this.$router.currentRoute.params.team_id
    if (teamId === undefined) {
      teamId = 25
    }
    if (teamId > 0) {
      this.$ajax.get('/interface.php?a=get_team_info&id=' + teamId).then((result) => {
        if (result.data.res === 'OK') {
          result.data.team.logo = '/static/images/team_logos/' + result.data.team.id + '.svg'
          this.$store.state.header_name = result.data.team.name

          for (var itemIndex in result.data.videos.items) {
            var video = result.data.videos.items[itemIndex]
            result.data.videos.items[itemIndex].duration = this.formatSeconds(video.duration)
            result.data.videos.items[itemIndex].snapshot = this.getNewImagePath(video.snapshot)
          }
          for (var imageIndex in result.data.images) {
            var image = result.data.images[imageIndex]
            result.data.images[imageIndex].url = this.getNewImagePath(image.url)
          }
          this.team_info = result.data.team
          this.images = result.data.images
          this.team_videos = result.data.videos.items
          this.total = result.data.videos.total
          this.count = result.data.videos.count
          this.pageno = 1

          this.getSpareData(this.pageno + 1)
        }
      })
    }
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    return {
      team_info: [],
      images: [],
      spareData: [],
      team_videos: [],
      total: 0,
      pageno: 0,
      count: 0
    }
  }
}
</script>

<style>
.slide-image{
  height: 100%;
  width: 100%;
}
#team_info{
  width: 250px;
  margin: 20px auto;
}
#team_info img {
  width: 60px;
  height: 60px;
  float: right;
  vertical-align: middle;
}
#team{
  margin: 10px;
}
#team_images{
  width: 100%;
}
#team_info_left{
  height: 100%;
  float: left;
}
#team_info_right{
  height: 100%;
  vertical-align: text-top;
}

</style>
