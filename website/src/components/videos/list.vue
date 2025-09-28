<template>
    <div class="playList">
      <playList :playlist='playlist'></playList>
    </div>
</template>

<script>
import playList from '@/components/template/playList'

export default {
  name: 'PlayerList',
  components: {
    playList
  },
  methods: {
  },
  activated () {
    console.log('current list: ', this.listId, 'param list: ', this.$router.currentRoute.params.list_id)
    if (this.$router.currentRoute.params.list_id !== this.listId) {
      this.listId = this.$router.currentRoute.params.list_id
      this.$ajax.get('/interface.php?a=get_playlist&id=' + this.listId).then((result) => {
        if (result.data.res === 'OK') {
          this.playlist = result.data.list
        }
      })
    }
  },
  data () {
    return {
      playlist: null,
      listId: 0
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
