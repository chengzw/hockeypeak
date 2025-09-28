<template>
    <div class="playlist" v-if="playlist">
        <div v-if="playlist.type == 'video'" class="video_block_videos video_block_items">
            <div v-for="(video, idx) in playlist.videos" :key="idx" class="block-item-video">
                <videoItem :video='video'></videoItem>
            </div>
        </div>
        <div v-if="playlist.type == 'movie'" class="video_block_movies video_block_items">
            <div v-for="(movie, idx) in playlist.movies" :key="idx" class="block-item-movie">
                <movieItem :movie='movie'></movieItem>
            </div>
        </div>
    </div>
</template>
<script>
import videoItem from '@/components/template/videoItem'
import movieItem from '@/components/template/movieItem'

export default {
  name: 'playList',
  props: ['playlist'],
  components: {
    videoItem,
    movieItem
  },
  updated () {
    console.log('playlist2 updated', this.$props.playlist)
    this.$store.state.header_name = this.playlist.title
  }
}
</script>

<style scoped>
.video_block_videos {
  margin: 0 5px;
}
.block-item-video {
  margin-bottom: 12px;
}
</style>
