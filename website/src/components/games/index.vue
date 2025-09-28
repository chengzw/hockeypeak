<template>
    <div>
        <div class="gameTabs">
            <div v-for="(tab, tab_index) in tabs" :key="tab_index" class="item" :class="$store.state.gameCurrTabName === tab.name?'curr_tab':''" @click="clickGameTab(tab.name)">
                {{tab.title}}
            </div>
        </div>
        <div class="game_pages">
            <loading :show="loading"></loading>
            <!-- 赛程 -->
            <div v-if="$store.state.gameCurrTabName === tabs[0].name &&  gameSchedule.displayDates.length > 0" id="game_schedule_page">
                <div id="gameScheduleDays">
                    <div id="gameScheduleDay_prev" @click="clickDisplayDay(gameSchedule.prev)" class="item">
                        <div class="left_arrow arrow"></div>
                    </div>
                    <div v-for="(day, idx) in gameSchedule.displayDates" :key="idx" @click="clickDisplayDay(day.date)" :class="gameSchedule.currDate === day.date?'curr_display_day':''" class="gameScheduleDay item">
                        <div class="day_date">
                            {{day.day}}
                        </div>
                        <div class="day_name">
                            {{day.name}}
                        </div>
                    </div>
                    <div id="gameScheduleDay_next" class="item" @click="clickDisplayDay(gameSchedule.next)">
                        <div class="right_arrow arrow"></div>
                    </div>
                </div>
                <div id="gameScheduleData">
                    <div id="gamesList">
                        <div v-for="(game, idx) in gameSchedule.games" :key="idx">
                            <div class="list_game" @click="onClickGame(game)">
                                <div class="list_game_time item">{{game.time}}</div>
                                <div class="list_game_logo item">
                                    <img :src="game.host_logo" mode="aspectFit" />
                                </div>
                                <div class="list_game_name item">{{game.host_abbr}}</div>
                                <div class="list_game_score item">{{game.host_score}}</div>
                                <div> : </div>
                                <div class="list_game_score item">{{game.guest_score}}</div>
                                <div class="list_game_name item">{{game.guest_abbr}}</div>
                                <div class="list_game_logo item">
                                    <img :src="game.guest_logo" mode="aspectFit" />
                                </div>
                                <div class="list_game_state item">{{game.state_str}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- 锦集 -->
            <div v-if="$store.state.gameCurrTabName === tabs[1].name" id="game_condensed_page">
                <div v-for="(video, idx) in gameCondensed.videos" :key="idx">
                    <listVideo :video='video'></listVideo>
                </div>
            </div>

            <!-- 热门 -->
            <div v-if="$store.state.gameCurrTabName === tabs[2].name" id="game_recaps_page">
                <div v-for="(video, idx) in popularVideos.videos" :key="idx">
                    <listVideo :video='video'></listVideo>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import teamList from '@/components/teams/teamList'
import listVideo from '@/components/template/listVideo'

export default {
  name: 'TeamList',
  components: {
    teamList,
    listVideo
  },
  methods: {
    clickGameTab: function (currTabName) {
      this.$store.state.gameCurrTabName = currTabName
      if (currTabName === 'condensed' && this.gameCondensed.total === 0) {
        this.getCondensedVideoList(1)
      } else if (currTabName === 'popular' && this.popularVideos.total === 0) {
        this.getPopularVideoList(1)
      } else if (currTabName === 'schedule' && this.gameSchedule.games.length === 0) {
        this.getScheduleList(this.gameSchedule.currDate)
      }
    },
    getDisplayDate: function (dates, today, currDate) {
      var result = {
        displayDates: [],
        prev: '',
        next: '',
        currDate: ''
      }
      var currIndex = 0
      var minIndex = 0
      var maxIndex = 0
      var dateMaxIndex = dates.length - 1

      for (var dateIndex in dates) {
        if ((currDate !== '' && currDate === dates[dateIndex].date) || (currDate === '' && dates[dateIndex].current)) {
          result.currDate = dates[dateIndex].date
          currIndex = Number(dateIndex)
          break
        }
      }
      if (currIndex >= 2 && currIndex + 2 <= dateMaxIndex) {
        minIndex = currIndex - 2
        maxIndex = currIndex + 2
      } else if (currIndex < 2) {
        minIndex = 0
        maxIndex = 4
      } else if (currIndex + 2 > dateMaxIndex) {
        maxIndex = dateMaxIndex
        minIndex = maxIndex - 4
      }

      for (dateIndex in dates) {
        if (dateIndex < minIndex || dateIndex > maxIndex) {
          continue
        }
        var dateobj = this.getDateStr(dates[dateIndex].date, 0)
        var date = {
          date: dates[dateIndex].date,
          day: dateobj.date.substring(5),
          name: (today === dates[dateIndex].date) ? '今天' : dateobj.week
        }
        if (currIndex - 1 === dateIndex) {
          result.prev = dates[dateIndex].date
        }
        if (currIndex === dateIndex) {
          result.currDate = dates[dateIndex].date
        }
        if (currIndex + 1 === dateIndex) {
          result.next = dates[dateIndex].date
        }
        result.displayDates.push(date)
      }
      return result
    },

    // 获取赛程列表
    getScheduleList: function (inputDate) {
      this.loading = true
      this.$ajax.get('/interface.php?a=get_schedule&date=' + inputDate + '&buffer=' + 2).then((result) => {
        if (result.data.res === 'OK') {
          var dates = result.data.dates
          var games = result.data.games

          var gameSchedule = this.gameSchedule
          var displayDatesData = this.getDisplayDate(dates, this.gameSchedule.today, '')
          gameSchedule.displayDates = displayDatesData.displayDates
          let dateIndex = 0
          for (dateIndex = 0; dateIndex < dates.length; dateIndex++) {
            if (dates[dateIndex].current) {
              break
            }
          }
          // now dateIndex point to current date
          console.log(dateIndex, dates, dates[dateIndex], dateIndex + 1, dates[dateIndex + 1])
          if (dateIndex > 0 && dates[dateIndex - 1]) {
            gameSchedule.prev = dates[dateIndex - 1].date
          }
          if (dateIndex < dates.length - 1 && dates[dateIndex + 1]) {
            gameSchedule.next = dates[dateIndex + 1].date
          }
          console.log(gameSchedule)
          gameSchedule.currDate = displayDatesData.currDate

          for (var gameIndex in games) {
            games[gameIndex].time = games[gameIndex].playtime.substring(11, 16)
            games[gameIndex].state_str = this.state_config[games[gameIndex].state]
            games[gameIndex].host_logo = games[gameIndex].host_id > 0 ? ('/static/images/team_logos/' + games[gameIndex].host_id + '.svg') : this.formatLogo(games[gameIndex].host_logo)
            games[gameIndex].guest_logo = games[gameIndex].guest_id > 0 ? ('/static/images/team_logos/' + games[gameIndex].guest_id + '.svg') : this.formatLogo(games[gameIndex].guest_logo)
          }

          gameSchedule.dates = dates
          gameSchedule.games = games

          this.gameSchedule = gameSchedule
          this.loading = false
        }
      })
    },
    clickDisplayDay: function (currDate) {
      console.log('ClickDisplayDay', currDate, this.gameSchedule)
      if (currDate !== '') {
        var gameSchedule = this.gameSchedule
        var displayDatesData = this.getDisplayDate(this.gameSchedule.dates, this.gameSchedule.today, currDate)
        gameSchedule.displayDates = displayDatesData.displayDates
        gameSchedule.prev = displayDatesData.prev
        gameSchedule.next = displayDatesData.next
        gameSchedule.currDate = displayDatesData.currDate
        gameSchedule.games = []

        this.gameSchedule = gameSchedule

        this.getScheduleList(currDate)
      } else {
        console.log('currDate id null')
      }
    },

    // 获取锦集视频列表
    getCondensedVideoList: function (page) {
      console.log(page)
      if (this.gameCondensed.pageno < 3 && page > this.gameCondensed.pageno && (this.gameCondensed.total === 0 || (this.gameCondensed.total > this.gameCondensed.count))) {
        this.loading = true
        this.$ajax.get('/interface.php?a=get_condensed_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var videos = result.data.videos
            var gameCondensed = this.gameCondensed
            for (var itemIndex in videos.items) {
              videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
              videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)
              videos.items[itemIndex].name = videos.items[itemIndex].host_abbr + ' vs ' + videos.items[itemIndex].guest_abbr
              // {{video.host_abbr}} vs {{video.guest_abbr}}
              gameCondensed.videos.push(videos.items[itemIndex])
            }
            gameCondensed.pageno++
            gameCondensed.total = Number(videos.total)
            gameCondensed.count = Number(videos.count) + gameCondensed.count
            this.gameCondensed = gameCondensed
            this.loading = false
            this.getCondensedSpareData(this.gameCondensed.pageno + 1)
          } else {
            this.loading = false
          }
        })
      }
    },
    // 获取锦集备用数据
    getCondensedSpareData: function (page) {
      if (this.gameCondensed.total > this.gameCondensed.count) {
        this.$ajax.get('/interface.php?a=get_condensed_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var videos = result.data.videos
            var gotVideos = []
            for (var itemIndex in videos.items) {
              videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
              videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)
              videos.items[itemIndex].name = videos.items[itemIndex].host_abbr + ' vs ' + videos.items[itemIndex].guest_abbr
              gotVideos.push(videos.items[itemIndex])
            }

            var gameCondensed = this.gameCondensed
            gameCondensed.spareData = gotVideos
            this.gameCondensed = gameCondensed
          }
        })
      } else {
        var gameCondensed = this.gameCondensed
        gameCondensed.spareData = []
        this.gameCondensed = gameCondensed
      }
    },

    // 获取热门视频列表
    getPopularVideoList: function (page) {
      if (this.popularVideos.pageno < 3 && page > this.popularVideos.pageno && (this.popularVideos.total === 0 || (this.popularVideos.total > this.popularVideos.count))) {
        this.loading = true
        this.$ajax.get('/interface.php?a=get_popularvideo_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var videos = result.data.videos
            var popularVideos = this.popularVideos
            for (var itemIndex in videos.items) {
              videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
              videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)
              popularVideos.videos.push(videos.items[itemIndex])
            }
            popularVideos.pageno++
            popularVideos.total = Number(videos.total)
            popularVideos.count = Number(videos.count) + popularVideos.count
            this.popularVideos = popularVideos
            this.loading = false
            this.getPopularSpareData(this.popularVideos.pageno + 1)
          } else {
            this.loading = false
          }
        })
      }
    },
    // 获取热门备用数据
    getPopularSpareData: function (page) {
      if (this.popularVideos.total > this.popularVideos.count) {
        this.$ajax.get('/interface.php?a=get_popularvideo_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var videos = result.data.videos
            var gotVideos = []
            for (var itemIndex in videos.items) {
              videos.items[itemIndex].duration = this.formatSeconds(videos.items[itemIndex].duration)
              videos.items[itemIndex].snapshot = this.getNewImagePath(videos.items[itemIndex].snapshot)
              gotVideos.push(videos.items[itemIndex])
            }

            var popularVideos = this.popularVideos
            popularVideos.spareData = gotVideos
            this.popularVideos = popularVideos
          }
        })
      } else {
        var popularVideos = this.popularVideos
        popularVideos.spareData = []
        this.popularVideos = popularVideos
      }
    },

    onClickGame: function (game) {
      if (game.state === 1 && game.video_id > 0) {
        this.$router.push({
          path: '/play/index/' + game.video_id
        })
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
    },

    onReachBottom: function () {
      if (this.$route.path !== '/games/index' && this.$route.path !== '/') {
        return ''
      }
      var condensedSpareDataNum = Number(this.gameCondensed.spareData.length)
      var popularSpareDataNum = Number(this.popularVideos.spareData.length)
      if (this.$store.state.gameCurrTabName === 'condensed' && condensedSpareDataNum > 0) {
        var gameCondensed = this.gameCondensed
        var spareData = gameCondensed.spareData
        var currData = gameCondensed.videos

        for (var itemIndex in spareData) {
          currData.push(spareData[itemIndex])
        }
        gameCondensed.pageno++
        gameCondensed.videos = currData
        gameCondensed.count = condensedSpareDataNum + gameCondensed.count

        this.gameCondensed = this.gameCondensed
        this.getCondensedSpareData(gameCondensed.pageno + 1)
      } else if (this.$store.state.gameCurrTabName === 'popular' && popularSpareDataNum > 0) {
        var popularVideos = this.popularVideos
        spareData = popularVideos.spareData
        currData = popularVideos.videos
        for (itemIndex in spareData) {
          currData.push(spareData[itemIndex])
        }
        popularVideos.pageno++
        popularVideos.videos = currData
        popularVideos.count = condensedSpareDataNum + popularVideos.count

        this.popularVideos = this.popularVideos
        this.getPopularSpareData(popularVideos.pageno + 1)
      }
    }

  },
  mounted () {
    window.addEventListener('scroll', this.scrollBottom, true)
  },
  created () {
    if (this.$store.state.gameCurrTabName === 'condensed') {
      this.getCondensedVideoList(1)
    } else if (this.$store.state.gameCurrTabName === 'popular') {
      this.getPopularVideoList(1)
    } else {
      this.getScheduleList(this.gameSchedule.currDate)
    }
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    var today = this.getDateStr(null, 0).date
    // console.log("today:"+today);
    return {
      loading: false,
      tabs: [
        {
          name: 'schedule',
          title: '赛程'
        },
        {
          name: 'condensed',
          title: '集锦'
        },
        {
          name: 'popular',
          title: '热门'
        }
      ],
      // currTabName: 'schedule',
      state_config: {
        0: '未开始',
        1: '已结束',
        2: '比赛中',
        3: '推迟',
        9: '未知'
      },
      gameSchedule: {
        today: today,
        games: [],
        currDate: today,
        prev: '',
        next: '',
        displayDates: [],
        dates: []
      },
      gameCondensed: {
        videos: [],
        spareData: [],
        pageno: 0,
        total: 0,
        count: 0
      },
      popularVideos: {
        videos: [],
        spareData: [],
        pageno: 0,
        total: 0,
        count: 0
      }
    }
  }
}
</script>
<style>
.gameTabs{
  display: flex;
  height: 40px;
  line-height: 40px;
  border-bottom: 3px solid #3C746B;
  text-align: center;
}

.curr_tab{
  background-color: #3C746B;
  color: #FFFFFF;
}

#gameScheduleDays {
    height: 52px;
    margin: 5px 0px;
}

#gameScheduleDay_next, #gameScheduleDay_prev{
    display: inline-block;
    height: 100%;
    width: 10%;
    cursor: pointer;
}

.left_arrow{
    transform: rotate(225deg);
    left: 33%;

}

.right_arrow{
    transform: rotate(45deg);
    right: -33%;
}
.gameScheduleDay {
    display: inline-block;
    width: 15%;
    color: #8e8e8e;
    text-align: center;
    padding: 5px 0px;
    cursor: pointer;
}
.curr_display_day{
color: #3a84ff;
border-radius: 4px;
background-color: #eef3f8;
}
.item {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    flex: 1;
}

.list_game{
  margin: 2px 1%;
  background-color: #eef3f8;
}
.list_game > div{
  display: inline-block;
  line-height: 45px;
  height: 45px;
  vertical-align: middle;
  text-align: center;
}
.list_game_logo{
  max-width: 8%;
}
/* .list_game_logo img{
  height: 45px;
  width: 35px;
} */
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

.list_video{
  height: 250px;
  padding: 10px;
}
.list_video_image{
  height: 80%;
}
.list_video_image > div {
  position: relative;
  width: 100%;
  height: 100%;
}
.list_video_duration {
  position: absolute;
  bottom: 2px;
  right: 5px;
}
.list_video_title{
  height: 20%;
  line-height: 20%;
  text-align: left;
}
.video-icon{
  position: absolute;
  bottom: 5px;
  left: 7px;
  width: 50px;
  height: 50px;
}
.list_video_name{
  margin: 5px 0px;
  width: 80%;
  display: inline-block;
  height: 100%;
  line-height: 100%;
}
.list_video_operation{
  position: absolute;
  width: 19%;
  display: inline-block;
  text-align: right;
  height: initial;
  line-height: 100%;
  vertical-align: middle;
}

.list_video_snapshot{
  width: 100%;
  height: 100%;
}

*{
  -webkit-touch-callout:none;  /*系统默认菜单被禁用*/
  -webkit-user-select:none; /*webkit浏览器*/
  -khtml-user-select:none; /*早期浏览器*/
  -moz-user-select:none;/*火狐*/
  -ms-user-select:none; /*IE10*/
  user-select:none;
}
</style>
