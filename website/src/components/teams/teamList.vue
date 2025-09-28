<template>
  <div class="teamList">
    <div v-for="(team, team_index) in teamList" :key="team_index" class="list_team_row" @click="gotopage('/teams/info/'+team.id)">
        <img :src="team.logo"/>
        <div>{{team.name}}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'teamList',
  props: ['teamList'],
  methods: {
  }
}
</script>

<style>
.list_team_row {
text-align: center;
padding: 15px 0px;
margin: 0px;
}

.list_team_row img {
  height: 85px;
}
</style>
