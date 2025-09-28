<template>
    <div class="video_block">
        <div class="video_block_title">
            <span>{{block.title}}</span><span v-if="block.more" class="more-link" @click="gotopage(block.more);">更多</span>
        </div>
        <div v-if="block.type == 'match'" class="game_block_videos game_block_items">
            <div v-for="(video, idx) in block.videos" :key="idx">
                <matchItem :match='video'></matchItem>
            </div>
        </div>
        <div v-if="block.type == 'video'" class="video_block_videos video_block_items">
            <div v-for="(video, idx) in block.videos" :key="idx" class="block-item-video">
                <videoItem :video='video'></videoItem>
            </div>
        </div>
        <div v-if="block.type == 'movie'" class="video_block_movies video_block_items">
            <div v-for="(movie, idx) in block.movies" :key="idx" class="block-item-movie">
                <movieItem :movie='movie'></movieItem>
            </div>
        </div>
    </div>
</template>
<script>
import videoItem from '@/components/template/videoItem'
import movieItem from '@/components/template/movieItem'
import matchItem from '@/components/template/matchItem'

export default {
  name: 'videoBlock',
  props: ['block'],
  components: {
    videoItem,
    movieItem,
    matchItem
  }
}
</script>

<style scoped>
.video_block {
    text-align: left;
    padding: 0 5px 10px 5px;
    font-size: 14px;
    background-color: #d9d9d9;
}
.video_block_title {
    border-bottom: solid 1px #d9d9d9;
    padding: 3px 5px;
    background-color: white;
}
.more-link {
    float: right;
}
.game_block_items {
    background-color: white;
}
.video_block_items {
    padding-top: 10px;
    background-color: white;
}
.video_block_videos {
    display: inline-flex;
    justify-content: space-around;
    flex-wrap: wrap;
}
.video_block_movies {
    display: inline-flex;
    justify-content: space-around;
    flex-wrap: wrap;
}
.block-item-video {
    width: 45%;
    margin-bottom: 10px;
}
.block-item-movie {
    width: 23%;
    margin-bottom: 10px;
}
</style>
