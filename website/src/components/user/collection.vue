<template>
<div>
  <div v-if="videos.length == 0" class="data_is_null_str">
    暂无收藏
  </div>
  <div v-else>
    <div v-for="(video, idx) in videos" :key="idx">
        <div class="list_video3">
            <div class="list_video3_image" @click="gotopage('/play/index/'+video.id)">
                <img :src="video.snapshot" />
            </div>
            <div class="list_video3_right">
                <div class="list_video3_name">
                    <div>{{video.name}}</div>
                    <div class="list_video3_timeKey">收藏时间:{{video.preserveTime}}</div>
                </div>
                <div class="list_video3_operation">
                    <button class="video_dalete" @click="deleteVideos(video.id)">删</button>
                    <!-- <button class="video_share" @click="video_share('/play/index/'+video.id ,video.snapshot, video.name)">分享</button> -->
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
</template>

<script>

export default {
  name: 'UserCollection',
  methods: {
    // 删除收藏视频
    deleteVideos: function (videoId) {
      if (videoId > 0) {
        var collectionVideos = this.getLocalData('collectionVideos')
        collectionVideos = eval(collectionVideos)
        collectionVideos = collectionVideos === '' ? [] : collectionVideos
        var index = this.getArrayIndexKeyByValue(collectionVideos, 'id', videoId)

        if (index > -1) {
          collectionVideos.splice(index, 1)
          this.setLocalData('collectionVideos', JSON.stringify(collectionVideos))

          collectionVideos.reverse()
          this.videos = collectionVideos
        }
      }
    }
  },
  created () {
    var collectionVideos = this.getLocalData('collectionVideos')
    collectionVideos = eval(collectionVideos)
    collectionVideos = collectionVideos === '' ? [] : collectionVideos

    if (collectionVideos.length > 1) {
      collectionVideos.reverse()
    }
    this.videos = collectionVideos
    this.wxapi.initWxApi(window.location.href, '', this.$route.meta.title, this.$route.meta.title)
  },
  data () {
    return {
      videos: []
    }
  }
}
</script>

<style scoped>
.list_video3{
  margin: 5px 0px;
  height: 130px;
  border-bottom: 1px solid #f0f0f0;
  position: relative;
}
.list_video3_image {
  position: absolute;
  width: 40%;
  height: 125px;
  display: inline-block;
  vertical-align: bottom;
  margin-left: 2%;
}
.list_video3_image img{
  width: 100%;
  height: 100%;
}
.list_video3_right{
  margin-left: 44%;
  margin-right: 2%;
  width: 54%;
  display: inline-block;
}
.list_video3_name{
  height: 80px;
    text-align: left;
}
.list_video3_timeKey{
  font-size:14px;
  color: #a8a8a8;
}

.list_video3_operation{
  text-align: right;
}
</style>
