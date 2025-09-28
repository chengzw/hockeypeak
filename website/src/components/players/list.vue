<template>
    <div class="playerList">
        <div v-for="(player, idx) in playerList" :key="idx" class="list_player">
            <div class="list_player_cover">
                <img :src="player.cover" @click="gotopage('/players/info/'+player.id)" />
            </div>
            <div class="list_player_info">
                <div class="list_player_avatar">
                    <img class="player_avatar" :src='player.avatar' @click="gotopage('/players/info/'+player.id)"/>
                </div>
                <div class="list_player_details" @click="gotopage('/players/info/'+player.id)">
                    <div>姓名：{{player.abbr}}</div>
                    <div>号码：{{player.num}}号</div>
                    <div>
                        <img :src="'/static/images/team_logos/' + player.team_id + '.svg'" />
                        <div class="player_detail_team_name">{{player.teamname}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
  name: 'PlayerList',
  methods: {
    // 获取运动员列表
    getPlayerList: function (page) {
      if (page > this.pageno && (this.total === 0 || (this.total > this.count))) {
        this.$ajax.get('/interface.php?a=get_player_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var players = result.data.players
            var playerList = this.playerList
            for (var itemIndex in players.items) {
              players.items[itemIndex].cover = this.getNewImagePath(players.items[itemIndex].cover)
              players.items[itemIndex].avatar = this.getNewImagePath(players.items[itemIndex].avatar)
              playerList.push(players.items[itemIndex])
            }
            this.playerList = playerList
            this.pageno = this.pageno + 1
            this.total = Number(players.total)
            this.count = Number(players.count) + this.count

            this.getSpareData(page + 1)
          }
        })
      }
    },

    // 获取备用数据
    getSpareData: function (page) {
      if (this.total > this.count) {
        this.$ajax.get('/interface.php?a=get_player_list&pageno=' + page).then((result) => {
          if (result.data.res === 'OK') {
            var players = result.data.players
            var playerList = []
            for (var itemIndex in players.items) {
              players.items[itemIndex].cover = this.getNewImagePath(players.items[itemIndex].cover)
              players.items[itemIndex].avatar = this.getNewImagePath(players.items[itemIndex].avatar)

              playerList.push(players.items[itemIndex])
            }
            this.spareData = playerList
          }
        })
      } else {
        this.spareData = []
      }
    },

    onReachBottom: function () {
      if (this.$route.path !== '/players/list') {
        return ''
      }
      var spareDataNum = Number(this.spareData.length)
      if (spareDataNum > 0) {
        var players = this.spareData
        var playerList = this.playerList
        for (var itemIndex in players) {
          playerList.push(players[itemIndex])
        }

        // this.spareData = playerList
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
    this.getPlayerList(1)
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    return {
      spareData: [],
      playerList: [],
      count: 0,
      pageno: 0,
      total: 0
    }
  }
}
</script>

<style>
.player_avatar {
width: 60px;
height: 60px;
border-radius: 50%;
}
.list_player_details img {
width: 40px;
height: 30px;
float: left;
}

.list_player {
    position: relative;
    margin: 10px 10px 50px 10px;
}
.list_player_cover {
    width: 100%;
}

.list_player_cover img{
    width: inherit;
}
.list_player_info{
    position: relative;
}
.list_player_avatar {
    position: absolute;
    top: -40%;
    left: 5px;
}
.list_player_details {
    margin-left: 100px;
    text-align: left;
    line-height: 28px;
}
</style>
