<template>
    <div class="video_item video_item_horizon" @click="gotopage('/play/index/'+video_id)">
        <div class="video_item_image">
            <img referrerpolicy="no-referrer" class="video_item_image_snapshot" :src="video.snapshot" mode="scaleToFill" />
        </div>
        <div class="video_item_content">
            <div class="title">{{video.name}}</div>
            <!-- <div class="director">发布时间：{{video.created_date}}</div> -->
        </div>
    </div>
</template>
<script>
export default {
  name: 'videoItem',
  props: ['video'],
  computed: {
    video_id: function () {
      return this.video.id ? this.video.id : this.video.video_id
    }
  }
}
</script>

<style scoped>
.video_item img {
    width: 100%;
}
.video_item_content {
    text-align: left;
    font-size: 12px;
}
.video_item_content .title {
    font-size: 14px;
}
</style>
