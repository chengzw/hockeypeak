<template>
    <div class="video_item video_item_verticle" @click="gotopage('/play/index/'+movie.id)">
        <div class="video_item_image">
            <img referrerpolicy="no-referrer" class="video_item_image_snapshot" :src="movie.snapshot" mode="scaleToFill" />
        </div>
        <div class="video_item_content">
            <div class="title">{{movie.title}}</div>
            <div class="director"><span class="header">导演：</span><span>{{movie.director}}</span></div>
            <div class="actor"><span class="header">主演：</span><span>{{movie.actor}}</span></div>
            <div class="intro auto-hide auto-hide-2"><span class="header">简介：</span><span>{{movie.intro}}</span></div>
        </div>
    </div>
</template>
<script>
export default {
  name: 'detailedMovieItem',
  props: ['movie']
}
</script>

<style scoped>
.video_item_image {
    float: left;
    width: 70px;
    margin-right: 10px;
}
.video_item_verticle {
    height: 100px;
}
.video_item_verticle img {
    height: 90px;
}
.video_item_content {
    text-align: left;
    font-size: 12px;
    line-height: 18px;
}
.video_item_content .title {
    font-weight: 600;
    font-size: 14px;
}
/* .video_item_content .header {
    font-weight: 550;
} */
.auto-hide {
    /* white-space: nowrap; */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}
.auto-hide-2 {
    -webkit-line-clamp: 2 !important;
}
.auto-hide-3 {
    -webkit-line-clamp: 3 !important;
}
</style>
