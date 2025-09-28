import Vue from 'vue'
import Router from 'vue-router'
// import TeamList from '@/components/teams/list'
// import TeamInfo from '@/components/teams/info'

// import GamesIndex from '@/components/games/index'

// import PlayerList from '@/components/players/list'
// import PlayerInfo from '@/components/players/info'

// import PlayIndex from '@/components/play/index'

// import UserIndex from '@/components/user/index'
// import UserCollection from '@/components/user/collection'
// import UserLately from '@/components/user/lately'
const TeamList = () => import('@/components/teams/list')
const TeamInfo = () => import('@/components/teams/info')

const GamesIndex = () => import('@/components/games/index')

const VideosIndex = () => import('@/components/videos/index')
const VideosList = () => import('@/components/videos/list')

const PlayerList = () => import('@/components/players/list')
const PlayerInfo = () => import('@/components/players/info')

const PlayIndex = () => import('@/components/play/index')

const UserIndex = () => import('@/components/user/index')
const UserCollection = () => import('@/components/user/collection')
const UserLately = () => import('@/components/user/lately')

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'VideosIndex',
      component: VideosIndex,
      meta: {
        keepAlive: true,
        title: '精彩视频'
      }
    },
    {
      path: '/videos/index',
      name: 'VideosIndex',
      component: VideosIndex,
      meta: {
        keepAlive: true,
        title: '精彩视频'
      }
    },
    {
      path: '/videos/list/:list_id',
      name: 'VideosList',
      component: VideosList,
      meta: {
        keepAlive: true,
        title: '视频列表'
      }
    },
    {
      path: '/games/index',
      name: 'GamesIndex',
      component: GamesIndex,
      meta: {
        keepAlive: true,
        title: '冰球比赛'
      }
    },
    {
      path: '/teams/list',
      name: 'TeamList',
      component: TeamList,
      meta: {
        keepAlive: true,
        title: '球队列表'
      }
    },
    {
      path: '/teams/info/:team_id',
      name: 'TeamInfo',
      component: TeamInfo,
      meta: {
        title: '球队详情'
      }
    },
    {
      path: '/players/list',
      name: 'PlayerList',
      component: PlayerList,
      meta: {
        keepAlive: true,
        title: '球星列表'
      }
    },
    {
      path: '/players/info/:player_id',
      name: 'PlayerInfo',
      component: PlayerInfo,
      meta: {
        title: '球星详情'
      }
    },
    {
      path: '/play/index/:video_id',
      name: 'PlayIndex',
      component: PlayIndex,
      meta: {
        title: '播放视频'
      }
    },
    {
      path: '/user/index',
      name: 'UserIndex',
      component: UserIndex,
      meta: {
        keepAlive: true,
        title: '我的'
      }
    },
    {
      path: '/user/collection',
      name: 'UserCollection',
      component: UserCollection,
      meta: {
        title: '我的收藏'
      }
    },
    {
      path: '/user/lately',
      name: 'UserLately',
      component: UserLately,
      meta: {
        title: '最近播放'
      }
    }
  ]
})
