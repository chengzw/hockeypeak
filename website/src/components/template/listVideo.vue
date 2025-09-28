<template>
    <div class="list_video">
        <div class="list_video_image">
            <div @click="gotopage('/play/index/'+video_id)" class="video">
                <img class="video-icon" src="/static/images/video.png" />
                <div class="list_video_duration">{{video.duration}}</div>
                <img class="list_video_snapshot" :src="video.snapshot" mode="scaleToFill" />
            </div>
        </div>
        <div class="list_video_title">
          <div class="list_video_name">
              <div class="video_name">{{video.name}}</div>
              <div class="video_created_date">发布时间：{{video.created_date}}</div>
          </div>
          <div class="list_video_operation">
              <!-- <button class="video_share" @click="video_share('/play/index/'+video.id ,video.snapshot, video.name)">分享</button> -->
          </div>
        </div>
    </div>
</template>
<script>
export default {
  name: 'listVideo',
  props: ['video'],
  computed: {
    video_id: function () {
      return this.video.id ? this.video.id : this.video.video_id
    }
  }
}
</script>

<style>

.list_video{
  height: 250px;
  padding: 10px;
}
.list_video_image{
  height: 80%;
}
.list_video_image > div {
  position: relative;
  width: 100%;
  height: 100%;
}
.list_video_duration {
  position: absolute;
  bottom: 2px;
  right: 5px;
}
.list_video_title{
  height: 20%;
  line-height: 20%;
  text-align: left;
}
.video-icon{
  position: absolute;
  bottom: 5px;
  left: 7px;
  width: 50px;
  height: 50px;
}
.list_video_name{
  margin: 5px 0px;
  width: 80%;
  display: inline-block;
  height: 100%;
  line-height: 100%;
}
.list_video_operation{
  position: absolute;
  width: 19%;
  display: inline-block;
  text-align: right;
  height: initial;
  line-height: 100%;
  vertical-align: middle;
}

.list_video_snapshot{
  width: 100%;
  height: 100%;
}

.video_name{
  line-height: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  overflow: hidden;
  width: 100%;
  -webkit-box-orient: vertical;
}
</style>
