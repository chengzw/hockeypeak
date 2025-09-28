<template>
    <div class="movie_item" @click="gotopage('/play/index/'+movie.id)">
        <div class="movie_item_image">
            <img referrerpolicy="no-referrer" class="video_item_image_snapshot" :src="movie.snapshot" mode="scaleToFill" />
        </div>
        <div class="movie_item_content">
            <div class="title">{{movie.title}}</div>
        </div>
    </div>
</template>
<script>
export default {
  name: 'movieItem',
  props: ['movie']
}
</script>

<style scoped>
.movie_item img {
    width: 100%;
}
.movie_item_content {
    text-align: center;
    font-size: 12px;
    line-height: 18px;
}
.movie_item_content .title {
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
.auto-hide-4 {
    -webkit-line-clamp: 4 !important;
}
.auto-hide-5 {
    -webkit-line-clamp: 5 !important;
}
</style>
