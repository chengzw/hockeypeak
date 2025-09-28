<template>
    <div>
        <div class="teamTabs">
            <div v-for="(tab, tabIndex) in tabs" :key="tabIndex" class="item" :class="curr_tabIndex === tabIndex?'curr_tab':''" @click="clickTab(tabIndex)">
                {{tab.title}}
            </div>
        </div>
        <div class="teamList_table">
            <div class="teamList_tbody table">
                <div v-for="(division, divisionIndex) in currDivisions" :key="divisionIndex" :class="divisionIndex === 0?'division_list_left':'division_list_right'">
                    <div class="division_title">{{division.title}}</div>
                    <teamList class="division_teams"  :teamList='division.teams'></teamList>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import teamList from '@/components/teams/teamList'

export default {
  name: 'TeamList',
  components: {
    teamList
  },
  methods: {
    clickTab (tabIndex) {
      this.curr_conferenceIndex = tabIndex
      this.curr_tabIndex = tabIndex
    }
  },
  computed: {
    currDivisions: function () {
      var result = []
      if (this.teamLists[this.curr_conferenceIndex] !== undefined) {
        result = this.teamLists[this.curr_conferenceIndex].divisions
      }
      return result
    }
  },
  created () {
    this.$ajax.get('/interface.php?a=get_teams').then((res) => {
      if (res.data.res === 'OK') {
        var tabs = []
        for (var conferenceIndex in res.data.conferences) {
          var divisions = res.data.conferences[conferenceIndex].divisions
          for (var divisionIndex in divisions) {
            var teams = divisions[divisionIndex].teams
            for (var teamIndex in teams) {
              teams[teamIndex].logo = '/static/images/team_logos/' + teams[teamIndex].id + '.svg'
            }
          }
          tabs.push({
            conferenceIndex: conferenceIndex,
            title: res.data.conferences[conferenceIndex].title
          })
        }
        this.tabs = tabs
        this.teamLists = res.data.conferences
      }
    })
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    return {
      tabs: [],
      curr_tabIndex: 0,
      curr_conferenceIndex: 0,
      teamLists: []
    }
  }
}
</script>

<style>
.curr_tab{
    background-color: #3C746B;
    color: #FFFFFF;
}
.teamTabs{
    display: -ms-flexbox;
    display: flex;
    height: 40px;
    line-height: 40px;
    border-bottom: 3px solid #3C746B;
    text-align: center;
}
.item {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    flex: 1;
}
.teamList_tbody {
margin: 15px 1% 0px 1%;
}

.division_title{
  margin: 0px 25px;
  line-height: 40px;
  background-color: #F2F2F2;
}
.division_list_left,.division_list_right{
  display: inline-block;
  width: 49.5%
}
.division_list_right{
  /* float: right; */
  vertical-align: top;
  border-left: 2px solid #B9B9B9;
}
</style>
