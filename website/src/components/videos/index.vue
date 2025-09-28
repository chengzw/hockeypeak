<template>
  <div>
    <div class="blocks">
      <div v-for="(block, idx) in blocks" :key="idx" class="block">
        <videoBlock :block='block'></videoBlock>
      </div>
    </div>
    <div v-if="false">
      <span>查看更多视频</span>
    </div>
  </div>
</template>

<script>
import videoItem from '@/components/template/videoItem'
import movieItem from '@/components/template/movieItem'
import videoBlock from '@/components/template/videoBlock'

export default {
  name: 'VideoList',
  components: {
    videoItem,
    videoBlock,
    movieItem
  },
  methods: {
    getBlocks: function () {
      console.log('get blocks. ')
      this.$ajax.get('/interface.php?a=get_video_index_blocks').then((result) => {
        if (result.data.res === 'OK') {
          this.blocks = result.data.blocks
        }
      })
    }
  },
  mounted () {
    console.log('mounted. len: ' + this.blocks.length)
  },
  activated () {
    console.log('activated. len: ' + this.blocks.length)
    if (this.blocks.length === 0) {
      this.getBlocks()
    }
  },
  created () {
    console.log('created. len: ' + this.blocks.length)
  },
  data () {
    return {
      blocks: []
    }
  }
}
</script>
